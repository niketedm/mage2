<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Magefan\BlogSearch\Helper;

use Magento\Framework\App\Action\Action;
use Magento\Store\Model\ScopeInterface;

/**
 * Magefan Blog Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Registry $coreRegistry
    ) {

        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }
    /**
     * Retrieve category instance
     *
     * @return \Magefan\Blog\Model\Category
     */
    public function getCategory()
    {
        if ($this->_coreRegistry->registry('current_blog_category') !== null) {
            return $this->_coreRegistry->registry('current_blog_category')->getCategoryId();
        } else if ($this->_coreRegistry->registry('current_blog_post')) {
           // return false;
            return $this->_coreRegistry->registry('current_blog_post')->getParentCategory()->getCategoryId();
        } else {
            return false;
        }

    }
}
