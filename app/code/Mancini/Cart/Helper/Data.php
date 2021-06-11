<?php
/**
 * Mancini_Cart
 * Helper to provide data for cart page
 */
declare(strict_types=1);

namespace Mancini\Cart\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    /**
     * @var  \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $_productRepository; 

     /**
     * @var \Mancini\Protectionplan\Model\Product
     */
    protected $_customLinked;

    /**
     * @var \Mancini\Frog\Helper\ApiCall 
     */
    protected $_apiCallHelper;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $_priceHelper;
    
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Mancini\Protectionplan\Model\Product $customLinked
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param  \Mancini\Frog\Helper\ApiCall $apiCallHelper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Mancini\Protectionplan\Model\Product $customLinked,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Mancini\Frog\Helper\ApiCall $apiCallHelper
    ){
        $this->_productRepository   =   $productRepository;
        $this->_customLinked        =   $customLinked;
        $this->_priceHelper         =   $priceHelper;
        $this->_apiCallHelper       =   $apiCallHelper;
        parent::__construct($context);

    }

    /**
     * function for getting product details of cart
     * @return array
     */
    public function getItemDetails($sku){
        $item           =  $this->getProductBySku($sku);
        $itemArray      =   array();
            if($item->getSkuSerial()){
                $itemArray['sku_serial']        =    $item->getSkuSerial();
                $itemArray['delivery_date']     =    $this->getDeliveryDates($itemArray['sku_serial']);
            }

            if($item->getBrand()){
               $itemArray['brand']              = $item->getResource()->getAttribute('brand')->getFrontend()->getValue($item);
            }

            if($item->getFurnitureBrand()){
                $itemArray['furniture_brand']   = $item->getResource()->getAttribute('furniture_brand')->getFrontend()->getValue($item);
            }

            if($item->getSpecialPrice()){
                $itemArray['special_price']             = $item->getSpecialPrice();
            }

            if($item->getLength()){
                $itemArray['length']             = $item->getLength();
            }

            if($item->getWidth()){
                $itemArray['width']             =  $item->getWidth();
            }

        return $itemArray;
    }


    /**
      * function for getting delivery date
      * @return string
     */
    public function getDeliveryDates($skuSerial){
        
            //$params['item']= $skuSerial;
            $params['item']= '841230062976';
            $result     =   $this->_apiCallHelper->inetWebsiteItemDetail (\Zend_Http_Client::POST, $params);
            $details    =   json_decode($result->getBody(),true);
            $itemStatus =   $details['inventoryrec'];
            $status     =   $itemStatus['status'];

            if($status  == 'CR') {
                $DeliveryDate   =   date('M d', strtotime(' +1 day'));
            return $DeliveryDate;
            }
            else if ($status == 'CO' || $status == 'SPO') {
                $availableDate  =   $itemDetails['availability'];
                $DeliveryDate   =   date_format($availableDate,"M d");
            return $DeliveryDate;
            }
            else {
                return null;
            }
    }

       /**
     * Function for calculating discount ptice
     * @return array
     */
    public function calculateDiscoutPrice($sku , $qty , $baseTotal){

        $item           =  $this->getProductBySku($sku);
        $mainPrice      = $baseTotal;
        $priceArray     = array();
        if($item->getSpecialPrice()){
            $price=  $item->getPrice();
            $totalBasePrice = $price * $qty;
            $discountVal = (100 - round(($mainPrice*100/$totalBasePrice) , 2));
            if ($discountVal > 0){
                $priceArray['discount']     =  $this->_priceHelper->currency($discountVal, true, false);
            }

        }
        if($item->getPrice()){
            $priceArray['price']             =  $this->_priceHelper->currency($item->getPrice() * $qty, true, false);
        }

        return $priceArray;
    }

    /**
     * Function to get the protectionplan products 
     */
    public function getProtectionProducts ($productSku)
    {    
        $associatePrd = array();
        $product = $this->_productRepository->get($productSku);
        $productId  =   $product->getId();
        //Load product by product id
        $customLinkedProducts = $this->_customLinked->getCustomlinkedProductsNew($productId);

        if (!empty($customLinkedProducts)) {
            foreach ($customLinkedProducts as $customLinkedProduct) {
                $relatedPrd = $this->_productRepository->getById($customLinkedProduct['linked_product_id']);
               
                $associatePrd[$relatedPrd->getId()] =   $relatedPrd->getName();
            }
        }

        return $associatePrd; 
    }

    /**
     * Function to get the price of the product
     * @param int
     */
    public function getProductDetails($productId){
        return $this->_productRepository->getById($productId);
    }

    /**
     * Function to get the product by sku
     */
    public function getProductBySku($sku){
        return $this->_productRepository->get($sku);
    }

    /**
     * Function for price formatter
     */
    public function getPriceFormat($price){
        return $this->_priceHelper->currency($price, true, false);
    }
}
