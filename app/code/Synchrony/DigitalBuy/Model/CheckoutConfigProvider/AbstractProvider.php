<?php

namespace Synchrony\DigitalBuy\Model\CheckoutConfigProvider;

use Magento\Checkout\Model\ConfigProviderInterface;
use Synchrony\DigitalBuy\Model\Session as SynchronySession;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\UrlInterface;
use Magento\Payment\Model\MethodInterface;
use Synchrony\DigitalBuy\Gateway\Config\AbstractPaymentConfig as GatewayConfig;
use Magento\Customer\Model\Address\AbstractAddress;

/**
 * Class CheckoutConfigProvider
 * @package Synchrony\DigitalBuy\Model
 */
abstract class AbstractProvider implements ConfigProviderInterface
{
    /**
     * @var SynchronySession
     */
    protected $synchronySession;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var MethodInterface
     */
    protected $paymentMethod;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var GatewayConfig
     */
    protected $config;

    /**
     * @param SynchronySession $synchronySession
     * @param MethodInterface $paymentMethod
     * @param UrlInterface $urlBuilder
     * @param CheckoutSession $checkoutSession
     * @param GatewayConfig $config
     */
    public function __construct(
        SynchronySession $synchronySession,
        MethodInterface $paymentMethod,
        UrlInterface $urlBuilder,
        CheckoutSession $checkoutSession,
        GatewayConfig $config
    ) {
        $this->synchronySession = $synchronySession;
        $this->paymentMethod = $paymentMethod;
        $this->urlBuilder = $urlBuilder;
        $this->checkoutSession = $checkoutSession;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $config = [];
        $quote = $this->checkoutSession->getQuote();
        $paymentMethod = $this->paymentMethod;
        if ($paymentMethod->isAvailable($quote)) {
            $paymentConfig = [
                'redirectUrl' => $this->getRedirectUrl()
            ];

            $isCartValid = $paymentMethod->canUseForCart($quote);
            $paymentConfig['isCartValid'] = $isCartValid;
            if (!$isCartValid) {
                $paymentConfig['cartValidationMsg'] = $paymentMethod->getLastCartValidationMsg()
                    . ' ' . __('Please update your cart to use this payment option.');
            }

            $config['payment'][$this->paymentMethod->getCode()] = $paymentConfig;
        }

        return $config;
    }

    /**
     * Get Address match note
     *
     * @return string
     */
    protected function getAddressMatchNote()
    {
        $quote = $this->checkoutSession->getQuote();
        $addressTypeToPass = $this->config->getAddressTypeToPass($quote->getStoreId());
        if ($addressTypeToPass == AbstractAddress::TYPE_SHIPPING && !$quote->isVirtual()) {
            $addressTypeLabel = __('Shipping');
        } else {
            $addressTypeLabel = __('Billing');
        }

        return __('%1 address must match address associated with your bank account.', $addressTypeLabel);
    }

    /**
     * Retrieve payment completion modal page URL
     *
     * @return string
     */
    abstract protected function getRedirectUrl();
}
