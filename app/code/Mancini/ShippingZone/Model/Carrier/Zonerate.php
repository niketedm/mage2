<?php

namespace Mancini\ShippingZone\Model\Carrier;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\ResultFactory;
use Psr\Log\LoggerInterface;
use Mancini\ShippingZone\Helper\Data;

class Zonerate extends AbstractCarrier implements CarrierInterface
{
    /** @var string */
    protected $_code = 'zonerate';

    /** @var bool */
    protected $_isFixed = true;

    /** @var ResultFactory */
    protected $_rateResultFactory;

    /** @var MethodFactory */
    protected $_rateMethodFactory;

    /** @var Data */
    protected $helper;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        Data $helper,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->helper = $helper;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    // Applying Business rule to show available delivery methods
    /**
     * @param RateRequest $request
     * @return Result|bool
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }
                     
        /** @var Result $result */
        $result = $this->_rateResultFactory->create();
        $zone = $this->helper->getShippingZoneByZipcode($request->getDestPostcode());
        $allowedMethods = [];
        if (!$zone) {
            //return false;
        }else{
       
        $matressAttributeSetId   = $this->getConfigData('matress_attribute_set');
        $furnitureAttributeSetId = $this->getConfigData('furniture_attribute_set');
        $matressGreaterAmount    = (int)$this->getConfigData('matress_greater_amount');
        $bedInBoxCategory        = $this->getConfigData('bed_in_box_cateid');
        $beddingCategory        = $this->getConfigData('bedding_cateid');
      
        if ($request->getAllItems()) {
            
            foreach ($request->getAllItems() as $item) {
                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    continue;
                }
                $item->setData('product', null);
                $product = $item->getProduct();
                $productAttributeId = $product->getAttributeSetId();
                $price   = $product->getPrice();
                $productCategoryIds = $product->getCategoryIds();
 
                if ($productAttributeId == $matressAttributeSetId && $price >= $matressGreaterAmount  && !in_array($bedInBoxCategory,$productCategoryIds) && !in_array($beddingCategory,$productCategoryIds) &&  $zone['id'] == 1) {
                
                    $addMethods = ['standard' => __('Standard'), 'premium' => __('Premium')];
                    if(!in_array($addMethods, $allowedMethods, true)){
                        
                        $allowedMethods = $addMethods ;
                    }   
                }  elseif ($productAttributeId == $furnitureAttributeSetId  && !in_array($bedInBoxCategory,$productCategoryIds) && !in_array($beddingCategory,$productCategoryIds) && $zone['id'] == 1) {
                
                    $addMethods = ['standard' => __('Standard'), 'premium' => __('Premium')];
                    if(!in_array($addMethods, $allowedMethods, true)){
                        $allowedMethods = $addMethods ;
                    }  
                }  elseif (($productAttributeId == $matressAttributeSetId || $productAttributeId == $furnitureAttributeSetId) && !in_array($bedInBoxCategory,$productCategoryIds) && !in_array($beddingCategory,$productCategoryIds) &&  $zone['id'] == 2 ) {
                
                    $addMethods = ['standard' => __('Standard')];
                    if(!in_array($addMethods, $allowedMethods, true)){
                        $allowedMethods = $addMethods ;
                    }
                }  elseif (in_array($bedInBoxCategory,$productCategoryIds) && ( $zone['id'] == 1 ||  $zone['id'] == 2)) {
                            
                }  elseif (in_array($beddingCategory,$productCategoryIds)) {
                
                }   else{
                
                }
  
            }
        }
            foreach ($allowedMethods as $methodCode => $title) {
                if ($methodCode == 'premium') {
                    if ($zone['premium_shipping_cost'] == 0) {
                        continue;
                    }
                }
                $method = $this->_rateMethodFactory->create();

                $method->setCarrier($this->_code);
                $method->setCarrierTitle($this->getConfigData('title'));

                $method->setMethod($methodCode);
                $method->setMethodTitle($title);

                $method->setPrice($zone[$methodCode . '_shipping_cost']);
                $method->setCost($zone[$methodCode . '_shipping_cost']);

                $result->append($method);
            }

           
        }
            //adding warehouse pickup for all zipcodes
            $method = $this->_rateMethodFactory->create();

            $method->setCarrier($this->_code);
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod('instore');
            $method->setMethodTitle('Warehouse Pickup');

            $method->setPrice('0.00');
            $method->setCost('0.00');
            $result->append($method);
   
        

        return $result;
    }


    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return ['standard' => __('Standard'), 'premium' => __('Premium')];
    }
}
