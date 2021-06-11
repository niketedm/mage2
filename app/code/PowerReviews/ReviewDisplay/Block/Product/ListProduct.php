<?php
    /**
    * Rewrite Product ListProduct Block
    * @category    PowerReviews
    * @package     PowerReviews_ReviewDisplay
    * @author      Michal Walkowiak
    */

    namespace PowerReviews\ReviewDisplay\Block\Product;

    class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
    {
        public function getProductDetailsHtml(\Magento\Catalog\Model\Product $product){
            $html = $this->getLayout()->createBlock('Magento\Framework\View\Element\Template')->setProduct($product)->setTemplate('PowerReviews_ReviewDisplay::productlist.phtml')->toHtml();
            $renderer = $this->getDetailsRenderer($product->getTypeId());
            if ($renderer) {
                $renderer->setProduct($product);
                return $html.$renderer->toHtml();
            }
            return '';
        }
    }
