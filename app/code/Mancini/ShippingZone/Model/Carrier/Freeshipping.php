<?php

namespace Mancini\ShippingZone\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Mancini\ShippingZone\Helper\Data;

class Freeshipping extends \Magento\OfflineShipping\Model\Carrier\Freeshipping
{
    /**
     * @var string
     */
    protected $_code = 'freeshipping';

    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_rateMethodFactory;

    /** @var Data */
    protected $helper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;


    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        Data $helper,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->helper = $helper;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $rateResultFactory, $rateMethodFactory, $data);
    }

    // Applying Business rule to show free shipping according to the rules
    /**
     * FreeShipping Rates Collector
     *
     * @param RateRequest $request
     * @return \Magento\Shipping\Model\Rate\Result|bool
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }
       
        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();
		
		
		 $zone = $this->helper->getShippingZoneByZipcode($request->getDestPostcode());
         if (!$zone) {
            return false;
        }
		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $matressAttributeSetId   = $this->scopeConfig->getValue('carriers/zonerate/matress_attribute_set', $storeScope);
        $furnitureAttributeSetId = $this->scopeConfig->getValue('carriers/zonerate/furniture_attribute_set', $storeScope);
        $matressGreaterAmount    = (int)$this->scopeConfig->getValue('carriers/zonerate/matress_greater_amount', $storeScope);
        $bedInBoxCategory        = $this->scopeConfig->getValue('carriers/zonerate/bed_in_box_cateid', $storeScope);
        $beddingCategory        = $this->scopeConfig->getValue('carriers/zonerate/bedding_cateid', $storeScope);

        if ($request->getAllItems()) {
            $rule=array();
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
                    if(!in_array('Rule1', $rule, true)){
                    $rule['rule1'] = 'Rule1';
                    }
                } elseif ($productAttributeId == $furnitureAttributeSetId  && !in_array($bedInBoxCategory,$productCategoryIds) && !in_array($beddingCategory,$productCategoryIds) && $zone['id'] == 1) {
                    if(!in_array('Rule2', $rule, true)){
                    $rule['rule2'] = 'Rule2';
                    }
                    //return false;
                } elseif (($productAttributeId == $matressAttributeSetId || $productAttributeId == $furnitureAttributeSetId)  && !in_array($bedInBoxCategory,$productCategoryIds) && !in_array($beddingCategory,$productCategoryIds) &&  $zone['id'] == 2 ) {
                    if(!in_array('Rule3', $rule, true)){
                    $rule['rule3'] = 'Rule3';
                    }
                    //return false;
                } elseif (in_array($bedInBoxCategory,$productCategoryIds) && ( $zone['id'] == 1 ||  $zone['id'] == 2)) {
                    if(!in_array('Rule4', $rule, true)){
                    $rule['rule4'] = 'Rule4';
                    }
                } elseif (in_array($beddingCategory,$productCategoryIds)) {
                    if(!in_array('Rule5', $rule, true)){
                    $rule['rule5'] = 'Rule5';
                    }
                }    else{
               
                }
                

            }  
            if(!in_array('Rule1', $rule, true) && (in_array('Rule2', $rule, true) || in_array('Rule3', $rule, true)) ){
                return false;
            }
        }
        
        $this->_updateFreeMethodQuote($request);

        if ($request->getFreeShipping() || $this->isFreeShippingRequired($request)) {
            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
            $method = $this->_rateMethodFactory->create();

            $method->setCarrier('freeshipping');
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod('freeshipping');
            $method->setMethodTitle($this->getConfigData('name'));

            $method->setPrice('0.00');
            $method->setCost('0.00');

            $result->append($method);
        } elseif ($this->getConfigData('showmethod')) {
            $error = $this->_rateErrorFactory->create();
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('title'));
            $errorMsg = $this->getConfigData('specificerrmsg');
            $error->setErrorMessage(
                $errorMsg ? $errorMsg : __(
                    'Sorry, but we can\'t deliver to the destination country with this shipping module.'
                )
            );
            return $error;
        }
        return $result;
    }

    /**
     * Check subtotal for allowed free shipping
     *
     * @param RateRequest $request
     *
     * @return bool
     */
    private function isFreeShippingRequired(RateRequest $request): bool
    {
        $minSubtotal = $request->getPackageValueWithDiscount();
        if ($request->getBaseSubtotalWithDiscountInclTax()
            && $this->getConfigFlag('tax_including')) {
            $minSubtotal = $request->getBaseSubtotalWithDiscountInclTax();
        }

        return $minSubtotal >= $this->getConfigData('free_shipping_subtotal');
    }
    


}
