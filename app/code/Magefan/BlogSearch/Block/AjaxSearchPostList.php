<?php

namespace Magefan\BlogSearch\Block;

class AjaxSearchPostList extends \Magefan\Blog\Block\Search\PostList
{
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
        if ($this->getRequest()->getPost('category_id') !== false) {
            $this->_postCollection->addCategoryFilter(
                $this->getCategory()
            );
        }
        $this->_postCollection->setOrder(
            self::POSTS_SORT_FIELD_BY_PUBLISH_TIME,
            \Magento\Framework\Api\SortOrder::SORT_DESC
        );
    }

     /**
     * Init category
     *
     * @return \Magefan\Blog\Model\category || false
     */
    protected function getCategory()
    {
        $id = (int)$this->getRequest()->getPost('category_id');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $category = $objectManager->create(\Magefan\Blog\Model\Category::class)->load($id);

        return $category;
    }
    /**
     * Retrieve query
     * @return string
     */
    public function getQuery()
    {
        return (string)urldecode($this->getRequest()->getPost('search'));
    }

}
