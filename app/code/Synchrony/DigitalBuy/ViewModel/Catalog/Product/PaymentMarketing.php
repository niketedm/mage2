<?php

namespace Synchrony\DigitalBuy\ViewModel\Catalog\Product;

use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Payment\Model\MethodInterface;
use Synchrony\DigitalBuy\Gateway\Config\RevolvingConfig as PaymentGatewayConfig;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Payment\Model\Checks\SpecificationFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Registry;

class PaymentMarketing implements \Magento\Framework\View\Element\Block\ArgumentInterface
{

    /**
     * @var \Synchrony\DigitalBuy\Helper\PaymentMarketing
     */
    protected $marketingHelper;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var SpecificationFactory
     */
    protected $specificationFactory;

    /**
     * @var PaymentGatewayConfig
     */
    protected $config;

    /**
     * @var MethodInterface
     */
    protected $paymentMethod;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricingHelper;

    /**
     * @var Product
     */
    protected $product = null;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * @var Json
     */
    protected $serializer;

    /**
     * PaymentMarketing constructor.
     * @param \Synchrony\DigitalBuy\Helper\PaymentMarketing $marketingHelper
     * @param CheckoutSession $checkoutSession
     * @param SpecificationFactory $specificationFactory
     * @param PaymentGatewayConfig $config
     * @param MethodInterface $paymentMethod
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param Registry $registry
     * @param Json $serializer
     */
    public function __construct(
        \Synchrony\DigitalBuy\Helper\PaymentMarketing $marketingHelper,
        CheckoutSession $checkoutSession,
        SpecificationFactory $specificationFactory,
        PaymentGatewayConfig $config,
        MethodInterface $paymentMethod,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        Registry $registry,
        Json $serializer
    ) {
        $this->marketingHelper = $marketingHelper;
        $this->checkoutSession = $checkoutSession;
        $this->specificationFactory = $specificationFactory;
        $this->config = $config;
        $this->paymentMethod = $paymentMethod;
        $this->pricingHelper = $pricingHelper;
        $this->coreRegistry = $registry;
        $this->serializer = $serializer;
    }

    /**
     * Check if Payment marketing block can be shown in PDP
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function canDisplay()
    {
        if ($this->isRevolvingMethodAvailable() &&
             !$this->isProductAttributeSetProhibited() &&
             $this->marketingHelper->isPaymentMarketingEnabled()
             && $this->marketingHelper->canShowBlockInPDP()) {
             return true;
        }
         return false;
    }

    /**
     * Check if Revolving Method is Available
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isRevolvingMethodAvailable()
    {
        $methodInstance = $this->paymentMethod;
        $quote = $this->checkoutSession->getQuote();

        //Validated with quote currency && other validation associated with payment method.
        if ($methodInstance->isAvailable($quote) && $this->_canUseMethod($quote, $methodInstance)) {
            return true;
        }

        return false;
    }

    /**
     * @param $methodInstance
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _canUseMethod($quote, $methodInstance)
    {
        return $this->specificationFactory->create(
            [
                AbstractMethod::CHECK_USE_CHECKOUT,
                AbstractMethod::CHECK_USE_FOR_COUNTRY,
                AbstractMethod::CHECK_USE_FOR_CURRENCY
            ]
        )->isApplicable(
            $methodInstance,
            $quote
        );
    }

    /**
     * Check if Product Attribute set is prohibited
     *
     * @return bool
     */
    public function isProductAttributeSetProhibited()
    {
        $prohibitedAttributeSets = $this->config->getProhibitedProductAttributeSets();
        if (in_array($this->getProduct()->getAttributeSetId(), $prohibitedAttributeSets)) {
            return true;
        }
        return false;
    }

    /**
     * Get Promotion Marketing Helper
     *
     * @return \Synchrony\DigitalBuy\Helper\PaymentMarketing
     */
    public function getMarketingHelper()
    {
        return $this->marketingHelper;
    }

    /**
     * Get Product final price
     *
     * @return float
     */
    public function getProductPrice()
    {
        $price = 0;
        if ($this->getProduct()->getTypeId() == \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE) {
            $childProducts = $this->getProduct()->getTypeInstance()->getAssociatedProducts($this->getProduct());
            foreach ($childProducts as $product) {
                if ($product->isSaleable()) {
                    $priceInfo = $product->getPriceInfo();
                    $price += $priceInfo->getPrice('final_price')->getValue();
                }
            }
        } else {
            $priceInfo = $this->getProduct()->getPriceInfo();
            $price = $priceInfo->getPrice('final_price')->getValue();
        }
        return $price;
    }

    /**
     * Get Current Product
     *
     * @return Product
     */
    public function getProduct()
    {
        if (!$this->product) {
            $this->product = $this->coreRegistry->registry('product');
        }
        return $this->product;
    }

    /**
     * Get Revolving Payment Method Title
     *
     * @return string
     */
    public function getPaymentMethodTitle()
    {
        return $this->paymentMethod->getTitle();
    }

    /**
     * Get Serialized Promotion Block Config
     *
     * @return bool|false|string
     */
    public function serializedBlockConfig()
    {
        return $this->serialize($this->getMarketingHelper()->getPromotionBlockConfig());
    }

    /**
     * JSON Serialize array to string
     *
     * @param $value
     * @return bool|false|string
     */
    public function serialize($value)
    {
        return $this->serializer->serialize($value);
    }
}
