<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Magefan\Blog\Block\Search;

use Magento\Store\Model\ScopeInterface;

/**
 * Blog search result block
 */
class PostList extends \Magefan\Blog\Block\Post\PostList
{
    /**
     * Retrieve query
     * @return string
     */
    public function getQuery()
    {
        return (string)urldecode($this->getRequest()->getParam('q'));
    }

    /**
     * Prepare posts collection
     *
     * @return void
     */
    protected function _preparePostCollection()
    {
        parent::_preparePostCollection();
        $this->_postCollection->addSearchFilter(
            $this->getQuery()
        );
        $this->_postCollection->setOrder(
            self::POSTS_SORT_FIELD_BY_PUBLISH_TIME,
            \Magento\Framework\Api\SortOrder::SORT_DESC
        );
    }

    /**
     * Retrieve collection order field
     *
     * @return string
     */
    public function getCollectionOrderField()
    {
        return 'search_rate';
    }

    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $title = $this->_getTitle();
        $this->_addBreadcrumbs($title, 'blog_search');
        $this->pageConfig->getTitle()->set($title);

        $page = $this->_request->getParam(\Magefan\Blog\Block\Post\PostList\Toolbar::PAGE_PARM_NAME);
        if ($page < 2) {
            $robots = $this->config->getSearchRobots();
            $this->pageConfig->setRobots($robots);
        }

        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle(
                $this->escapeHtml($title)
            );
        }

        return parent::_prepareLayout();
    }

    /**
     * Retrieve title
     * @return string
     */
    protected function _getTitle()
    {
        return __('Search "%1"', $this->getQuery());
    }

    /**
     * Retrieve post html
     * @param  \Magefan\Blog\Model\Post $post
     * @return string
     */
    public function getPostHtml($post)
    {
        return $this->getChildBlock('blog.posts.list.item')->setPost($post)->toHtml();
    }

    /**
     * Retrieve Toolbar Block
     * @return \Magefan\Blog\Block\Post\PostList\Toolbar
     */
    public function getToolbarBlock()
    {

        //$blockName = $this->getToolbarBlockName();
        $blockName = 'post_list_toolbar';

        if ($blockName) {
            $block = $this->getLayout()->getBlock($blockName);
            if ($block) {
                return $block;
            }
        }
        $block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, uniqid(microtime()));
        return $block;
    }

    /**
     * Retrieve Toolbar Html
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    /**
     * Before block to html
     *
     * @return \Magefan\Blog\Block\Post\PostList
     */
    protected function _beforeToHtml()
    {
        $toolbar = $this->getToolbarBlock();

        // called prepare sortable parameters
        $collection = $this->getPostCollection();

        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);
        $this->setChild('toolbar', $toolbar);

        return parent::_beforeToHtml();
    }

}
