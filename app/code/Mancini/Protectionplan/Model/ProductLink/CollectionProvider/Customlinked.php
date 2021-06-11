<?php
namespace Mancini\Protectionplan\Model\ProductLink\CollectionProvider;

class Customlinked{
    /**
     * @var \Magento\Framework\Registry
    */
    protected $_registry;

    /**
     * Customlinked constructor.
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\Registry $registry
    ) {
        $this->_registry = $registry;
    }
    public function getLinkedProducts($product) {
        $linktype = $this->_registry->registry('linktype');
        if($linktype == 'custom') {
            return $product->getCustomlinkedProducts();
        }
        if($linktype == 'related') {
            return $product->getRelatedProducts();
        }
       return $product->getCustomlinkedProducts();
    }
}
