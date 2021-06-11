<?php
/**
 * @author Michal Walkowiak
 * @copyright Copyright (c) 2017 PowerReviews (http://www.powerreviews.com)
 * @package PowerReviews_ReviewDisplay
 */
namespace PowerReviews\ReviewDisplay\Block\Catalog\Product;

class ReviewDisplay extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \PowerReviews\ReviewDisplay\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \PowerReviews\ReviewDisplay\Helper\Data $helper,
        array $data = [],
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        parent::__construct($context, $data);

        $this->_helper = $helper;
        $this->_objectManager = $objectManager;
    }

    public function getBaseUrl(){
        return $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getBaseUrl();
    }

    public function getApiKey(){
        return $this->_helper->getApiKey();
    }

    public function getLocale(){
        return $this->_helper->getLocale() ? $this->_helper->getLocale() : 'en_US';
    }

    public function getMerchantId(){
        return $this->_helper->getMerchantId();
    }

    public function getMerchantGroupId(){
        return $this->_helper->getMerchantGroupId();
    }

    public function getProductPageReviewSnippet(){
        return $this->_helper->getProductPageReviewSnippet();
    }

    public function getProductPageReviewDisplay(){
        return $this->_helper->getProductPageReviewDisplay();
    }

    public function getProductPageQuestionSnippet(){
        return $this->_helper->getProductPageQuestionSnippet();
    }

    public function getProductPageQuestionDisplay(){
        return $this->_helper->getProductPageQuestionDisplay();
    }

    public function getProductStockStatus(){
        $StockState = $this->_objectManager->get('\Magento\CatalogInventory\Api\StockStateInterface');
        $product = $this->getCurrentProduct();
        $stockState = $StockState->getStockQty($product->getId(), $product->getStore()->getWebsiteId());

        return $stockState > 0 ? '1' : '0';
    }

    public function getCurrentProduct(){
        $registry = $this->_objectManager->get('\Magento\Framework\Registry');

        return $registry->registry('current_product');
    }

    public function getCurrentCategory(){
        $registry = $this->_objectManager->get('\Magento\Framework\Registry');
        $category = $registry->registry('current_category');

        return $registry->registry('current_category');
    }


    protected function _toHtml()
    {
       if ($this->_helper->getEnable()){
            // return '<div>Our widget code here</div>';
            return parent::_toHtml();
       }
        else {
            return '';
        }
    }

    public function getCollection()
    {
        $model = $this->_objectManager->create('PowerReviews\ReviewDisplay\Model\ReviewDisplay');
        $collection = $model->getCollection();

        return $collection;
    }

}
