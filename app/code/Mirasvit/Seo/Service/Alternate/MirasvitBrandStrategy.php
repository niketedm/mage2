<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.1.2
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Service\Alternate;

use Mirasvit\Brand\Model\Config\BrandPageConfig;

class MirasvitBrandStrategy
{
    private $manager;

    private $registry;

    private $url;

    private $storeManager;

    /**
     * MirasvitBrandStrategy constructor.
     *
     * @param \Magento\Framework\Module\Manager                $manager
     * @param \Magento\Framework\Registry                      $registry
     * @param \Mirasvit\Seo\Api\Service\Alternate\UrlInterface $url
     * @param \Magento\Store\Model\StoreManagerInterface       $storeManager
     */
    public function __construct(
        \Magento\Framework\Module\Manager $manager,
        \Magento\Framework\Registry $registry,
        \Mirasvit\Seo\Api\Service\Alternate\UrlInterface $url,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->manager      = $manager;
        $this->registry     = $registry;
        $this->url          = $url;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreUrls()
    {
        if (!$this->manager->isEnabled('Mirasvit_Brand') ||
                    !class_exists('\Mirasvit\Brand\Service\BrandUrlService')) {
            return [];
        }

        $this->url->getStoresCurrentUrl();
        $storeUrls       = [];
        $brandUrlService = \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Mirasvit\Brand\Service\BrandUrlService::class);
        $brand           = $this->registry->registry(BrandPageConfig::BRAND_DATA);

        foreach ($this->storeManager->getStores() as $store) {
            if (isset($brand[BrandPageConfig::BRAND_PAGE_ID])) {
                $urlKey = $brandUrlService->getBrandUrl(
                    false,
                    $brand[BrandPageConfig::BRAND_DEFAULT_NAME],
                    $store->getStoreId()
                );
                if ($brandUrlService->match($urlKey)) {
                    $storeUrls[$store->getStoreId()] = $store->getBaseUrl() . $urlKey;
                }
            } else {
                $storeUrls[$store->getStoreId()] = $brandUrlService->getBaseBrandUrl($store->getStoreId());
            }
        }

        return $storeUrls;
    }
}
