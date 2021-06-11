<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\BlogPlus\Model\Facebook;

use \Magento\Framework\App\Request\Http;
use \Magefan\Blog\Model\ResourceModel\Post\CollectionFactory as PostCollectionFactory;
use \Magefan\BlogPlus\Model\Config;

/**
 * Class RelatedPost
 * @package Magefan\BlogPlus\Model
 */
class Publisher
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var CollectionFactory
     */
    protected $postsCollectionFactory;

    /**
     * @var Config
     */
    protected $config;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger,
        PostCollectionFactory $postsCollectionFactory,
        Config $config
    ) {
        $this->config = $config;
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->postsCollectionFactory = $postsCollectionFactory;
    }

    /**
     * @param null $storeId
     * @return bool|\Facebook\Facebook
     * @throws \Facebook\Exceptions\FacebookSDKException
     */
    public function getFbApi($storeId = null)
    {
        if (!$this->config->getFbAppId($storeId)
            || !$this->config->getFbSecretKey($storeId)
            || !$this->config->getFbPageId($storeId)) {
            return false;
        }
        return new \Facebook\Facebook([
            'app_id' => $this->config->getFbAppId($storeId),
            'app_secret' => $this->config->getFbSecretKey($storeId),
            'default_graph_version' => 'v3.0',
        ]);
    }

    /**
     * @param $post
     * @param null $storeId
     * @return bool
     */
    public function publish($post, $storeId = null)
    {
        $postData = $this->getPostData($post);
        $fb = $this->getFbApi($storeId);
        try {
            $fb->post('/' . $this->config->getFbPageId($storeId) . '/feed', $postData, $this->config->getFbAccessToken());
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            $this->logger->debug('Graph returned an error: ' . $e->getMessage());
            return false;
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            $this->logger->debug('Facebook SDK returned an error: ' . $e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * @return $this
     */
    public function publishPosts()
    {
        $stores = $this->storeManager->getStores();

        foreach ($stores as $store) {
            $storeId = $store->getId();
            if (!$storeId) {
                continue; //skip admin (zero) store view
            }
            if ($this->config->isFbPublishEnabled($storeId)) {
                $posts = $this->getPublishPostCollection($storeId);
                foreach ($posts as $post) {
                    $result = $this->publish($post, $storeId);

                    if ($result) {
                        $post->setFbPublished(1);
                        $post->save();
                    }
                }
            }
        }


        return $this;
    }

    /**
     * @param null $storeId
     * @return \Magefan\Blog\Model\ResourceModel\Post\Collection
     */
    public function getPublishPostCollection($storeId = null)
    {
        $postsCollection = $this->postsCollectionFactory->create()
            ->addActiveFilter()
            ->addFieldToFilter('fb_published', ['null' => true])
            ->addFieldToFilter('fb_auto_publish', true)
            ->addFieldToFilter('publish_time', ['gteq' => time() - 86400]);

        if ($storeId) {
            $postsCollection->addStoreFilter($storeId);
        }

        $categories = $this->config->getFbAutopublishCategories($storeId);
        if (!in_array(0, $categories)) {
            $postsCollection->addCategoryFilter($categories);
        }
        return $postsCollection;
    }

    /**
     * @param $post
     * @return array
     */
    public function getPostData($post)
    {
        $postData = [];
        $postData['link'] = $post->getPostUrl();
        $postData['message'] = $post->getOgDescription();
        return $postData;
    }

    /**
     * @return \Magefan\BlogPlus\Model\Config
     */
    public function getConfig()
    {
        return $this->config;
    }
}
