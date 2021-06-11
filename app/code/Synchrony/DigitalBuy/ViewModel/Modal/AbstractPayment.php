<?php

namespace Synchrony\DigitalBuy\ViewModel\Modal;

use Magento\Directory\Model\RegionFactory;
use Synchrony\DigitalBuy\Model\Session as SynchronySession;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Synchrony\DigitalBuy\Gateway\Config\AbstractPaymentConfig as SynchronyConfig;
use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Framework\Registry;
use Synchrony\DigitalBuy\Model\Sales\Order\Payment\AdditionalInfoManager as PaymentAdditionalInfoManager;

class AbstractPayment extends AbstractModal
{
    /**
     * @var RegionFactory
     */
    protected $regionFactory;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var PaymentAdditionalInfoManager
     */
    protected $paymentAdditionalInfoManager;

    /**
     * Payment constructor.
     * @param SynchronyConfig $synchronyConfig
     * @param Registry $coreRegistry
     * @param DateTime $dateTime
     * @param RegionFactory $regionFactory
     * @param CheckoutSession $checkoutSession
     * @param PaymentAdditionalInfoManager $paymentAdditionalInfoManager
     */
    public function __construct(
        SynchronyConfig $synchronyConfig,
        Registry $coreRegistry,
        DateTime $dateTime,
        RegionFactory $regionFactory,
        CheckoutSession $checkoutSession,
        PaymentAdditionalInfoManager $paymentAdditionalInfoManager
    ) {
        $this->regionFactory = $regionFactory;
        $this->checkoutSession = $checkoutSession;
        $this->paymentAdditionalInfoManager = $paymentAdditionalInfoManager;
        parent::__construct($synchronyConfig, $coreRegistry, $dateTime);
    }

    /**
     * Retrieve store id
     *
     * @return null|int
     */
    protected function getStoreId()
    {
        return $this->getCurrentOrder()->getStoreId();
    }

    /**
     * Generate clientTransId modal param
     *
     * @return string
     */
    public function getDigitalBuyClientTransId()
    {
        return 'M2_' . $this->synchronyConfig->getModuleVersion() . '_' . $this->getCurrentOrder()->getQuoteId();
    }

    /**
     * Retrieve Order address for modal initialtion
     *
     * @return AbstractAddress
     */
    public function getAddress()
    {
        $order = $this->getCurrentOrder();
        $addresType = $this->synchronyConfig->getAddressTypeToPass($order->getStoreId());
        if ($addresType == AbstractAddress::TYPE_SHIPPING && !$order->getIsVirtual()) {
            $address = $order->getShippingAddress();
        } else {
            $address = $order->getBillingAddress();
        }

        return $address;
    }

    /**
     * Retrieve RegionCode
     * @param int
     *
     * @return string
     */
    public function getRegionCode($regionId)
    {
        $region = $this->regionFactory->create()->load($regionId);
        return $region->getCode();
    }

    /**
     * Retrieve Customer Email
     *
     * @return string
     */
    public function getCustomerEmail()
    {
        return $this->getCurrentOrder()->getCustomerEmail();
    }

    /**
     * Retrieve Current Order object
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getCurrentOrder()
    {
        return $this->checkoutSession->getLastRealOrder();
    }
}
