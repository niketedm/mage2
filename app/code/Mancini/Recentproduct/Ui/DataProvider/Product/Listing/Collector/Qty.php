<?php
namespace Mancini\Recentproduct\Ui\DataProvider\Product\Listing\Collector;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductRenderExtensionFactory;
use Magento\Catalog\Api\Data\ProductRenderInterface;
use Magento\Catalog\Ui\DataProvider\Product\ProductRenderCollectorInterface;

class Qty implements ProductRenderCollectorInterface
{
    /** SKU html key */
    const KEY = "qty";

    /**
     * @var \Mancini\Frog\Helper\ApiCall 
     */
    protected $_apiCallHelper;

    /**
     * @var ProductRenderExtensionFactory
     */
    private $productRenderExtensionFactory;

    /**
     * @param \Mancini\Frog\Helper\ApiCall $apiCallHelper
     * @param ProductRenderExtensionFactory $productRenderExtensionFactory
     */
    public function __construct(
        \Mancini\Frog\Helper\ApiCall $apiCallHelper,
        ProductRenderExtensionFactory $productRenderExtensionFactory
    ) {
        $this->_apiCallHelper = $apiCallHelper;
        $this->productRenderExtensionFactory = $productRenderExtensionFactory;
    }

    /**
     * @inheritdoc
     */
    public function collect(ProductInterface $product, ProductRenderInterface $productRender)
    {
        $extensionAttributes = $productRender->getExtensionAttributes();

        if (!$extensionAttributes) {
            $extensionAttributes = $this->productRenderExtensionFactory->create();
        }

       // $skuSerial  =  $product->getSkuSerial();
        $skuSerial       =  '4155976';
        $availableQty    =  $this->getQuantityDetail ($skuSerial);
        if($availableQty){
            if($availableQty >= 11) {
               $qtyLabel = "In Stock";
            } else {
                $qtyLabel = $availableQty. " Items Left";
            }
            $extensionAttributes->setQty($qtyLabel);
        }
        
        $productRender->setExtensionAttributes($extensionAttributes);
    }

    /**
     * Function to get quantity of the product
     */
    public function getQuantityDetail ($skuSerial) {
        $params['item'] = $skuSerial;
        $result = $this->_apiCallHelper->inetWebsiteItemDetail(\Zend_Http_Client::POST, $params);
        $details = json_decode($result->getBody(), true);

        $availableQty   =   $details['availabletosell'];

        return $availableQty;
    }
}