<?php

namespace Synchrony\DigitalBuy\ViewModel\Checkout\Revolving;

use Magento\Framework\UrlInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Payment\Model\Method\Adapter;
use Magento\Payment\Model\Checks\SpecificationFactory;
use Magento\Payment\Model\MethodInterface;
use Magento\Payment\Model\Method\AbstractMethod;
use Synchrony\DigitalBuy\Gateway\Config\RevolvingConfig as SynchronyConfig;

/**
 * Class Link
 * @package Synchrony\DigitalBuy\ViewModel\Checkout
 */
class Link implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var SynchronyConfig
     */
    private $synchronyConfig;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var MethodInterface
     */
    private $paymentMethod;

    /**
     * @var SpecificationFactory
     */
    private $specificationFactory;

    /**
     * Link constructor.
     * @param SynchronyConfig $synchronyConfig
     * @param UrlInterface $urlBuilder
     * @param CheckoutSession $checkoutSession
     * @param MethodInterface $paymentMethod
     * @param SpecificationFactory $specificationFactory
     */
    public function __construct(
        SynchronyConfig $synchronyConfig,
        UrlInterface $urlBuilder,
        CheckoutSession $checkoutSession,
        MethodInterface $paymentMethod,
        SpecificationFactory $specificationFactory
    ) {
        $this->synchronyConfig = $synchronyConfig;
        $this->urlBuilder = $urlBuilder;
        $this->checkoutSession = $checkoutSession;
        $this->paymentMethod = $paymentMethod;
        $this->specificationFactory = $specificationFactory;
    }

    /**
     * Check should we render the block.
     * @return mixed
     */
    public function canRenderBlock()
    {
        // Check all required things configured to render block on cart.
        if ($this->isMethodAvailable() && $this->getIsCartButtonEnabled() && $this->getCartButtonImageUrl() != "") {
            return true;
        }
        return false;
    }

    /**
     * Get cart button enabled
     * @return mixed
     */
    public function getIsCartButtonEnabled()
    {
        return $this->synchronyConfig->getValue('cart_button_enabled');
    }

    /**
     * Get the cart button image URL
     * @return mixed
     */
    public function getCartButtonImageUrl()
    {
        return $this->synchronyConfig->getValue('cart_button_image_url');
    }

    /**
     * Get the cart button Image Alt text
     * @return mixed
     */
    public function getCartButtonImageAlt()
    {
        return $this->synchronyConfig->getValue('cart_button_image_alt');
    }

    /**
     * Get Image href
     * @return string
     */
    public function getCartButtonImageHref()
    {
        return $this->urlBuilder->getUrl('digitalbuy/revolving/modal_auth', ['_secure' => true]);
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isMethodAvailable()
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
                AbstractMethod::CHECK_USE_FOR_CURRENCY,
                AbstractMethod::CHECK_ORDER_TOTAL_MIN_MAX
            ]
        )->isApplicable(
            $methodInstance,
            $quote
        );
    }
}
