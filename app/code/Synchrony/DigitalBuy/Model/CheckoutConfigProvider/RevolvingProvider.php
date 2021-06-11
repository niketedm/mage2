<?php

namespace Synchrony\DigitalBuy\Model\CheckoutConfigProvider;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\UrlInterface;
use Magento\Payment\Model\MethodInterface;
use Synchrony\DigitalBuy\Gateway\Config\AbstractPaymentConfig as GatewayConfig;
use Synchrony\DigitalBuy\Helper\DynamicMarketing;
use Synchrony\DigitalBuy\Model\Session as SynchronySession;
use \Synchrony\DigitalBuy\Helper\PaymentMarketing;

/**
 * Class CheckoutConfigProvider
 * @package Synchrony\DigitalBuy\Model
 */
class RevolvingProvider extends AbstractProvider
{
    /**
     * @var PaymentMarketing
     */
    protected $marketingHelper;

    /**
     * RevolvingProvider constructor.
     * @param PaymentMarketing $marketingHelper
     * @param SynchronySession $synchronySession
     * @param MethodInterface $paymentMethod
     * @param UrlInterface $urlBuilder
     * @param CheckoutSession $checkoutSession
     * @param GatewayConfig $config
     */
    public function __construct(
        PaymentMarketing $marketingHelper,
        SynchronySession $synchronySession,
        MethodInterface $paymentMethod,
        UrlInterface $urlBuilder,
        CheckoutSession $checkoutSession,
        GatewayConfig $config
    ) {
        $this->marketingHelper = $marketingHelper;
        parent::__construct($synchronySession, $paymentMethod, $urlBuilder, $checkoutSession, $config);
    }
    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $config = parent::getConfig();

        $quote = $this->checkoutSession->getQuote();
        $paymentMethod = $this->paymentMethod;

        if ($paymentMethod->isAvailable($quote)) {
            $paymentCode = $this->paymentMethod->getCode();

            if ($this->marketingHelper->isPaymentMarketingEnabled() &&
                $this->marketingHelper->canShowBlockInCheckout()) {
                $config['payment'][$paymentCode]['marketingBlockConfig'] = $this->getMarketingBlockConfig();
            }
            $canShowAddressNote = $this->config->getCanShowAddressMatchNote();
            $config['payment'][$paymentCode]['canShowAddressMatchNote'] = $canShowAddressNote;
            if ($canShowAddressNote) {
                $config['payment'][$paymentCode]['addressMatchNote'] = $this->getAddressMatchNote();
            }

            $config['payment'][$paymentCode]['accountNumber'] = $this->synchronySession->getAccountNumber();
        }

        return $config;
    }

    /**
     * Get Promotion Block config
     *
     * @return array
     */
    protected function getMarketingBlockConfig() {
        $config = [];
        if ($this->marketingHelper->isDisplayModeStatic()) {
            $config['contentHtml'] = $this->marketingHelper->getStaticBlockHtml();
            $config['static'] = true;
        } else {
            $config = $this->marketingHelper->getPromotionBlockConfig();
            $config['dynamic'] = true;
        }
        return $config;
    }

    /**
     * Retrieve payment completion modal page URL
     *
     * @return string
     */
    protected function getRedirectUrl()
    {
        return $this->urlBuilder->getUrl('digitalbuy/revolving/modal_payment', ['_secure' => true]);
    }
}
