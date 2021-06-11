<?php

namespace Synchrony\DigitalBuy\ViewModel\Modal\Revolving;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Synchrony\DigitalBuy\Gateway\Config\RevolvingConfig as SynchronyConfig;
use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\Registry;

/**
 * Class Modal
 * @package Synchrony\DigitalBuy\ViewModel\Checkout
 */
class Auth extends \Synchrony\DigitalBuy\ViewModel\Modal\AbstractModal
{
    /**
     * Registry key for successful authentication flag
     */
    const AUTH_SUCCESS_REGISTRY_KEY = 'synchrony_digitalbuy_auth_success';

    /**
     * Registry key for address data retrieved after successful authentication
     */
    const ADDRESS_DATA_REGISTRY_KEY = 'synchrony_digitalbuy_auth_address_data';

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var AccountManagementInterface
     */
    private $customerAccountManagement;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * Auth constructor.
     * @param SynchronyConfig $synchronyConfig
     * @param Registry $coreRegistry
     * @param DateTime $dateTime
     * @param CheckoutSession $checkoutSession
     * @param CustomerSession $customerSession
     * @param AccountManagementInterface $customerAccountManagement
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        SynchronyConfig $synchronyConfig,
        Registry $coreRegistry,
        DateTime $dateTime,
        CheckoutSession $checkoutSession,
        CustomerSession $customerSession,
        AccountManagementInterface $customerAccountManagement,
        UrlInterface $urlBuilder
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($synchronyConfig, $coreRegistry, $dateTime);
    }

    /**
     * Retrieve store id
     *
     * @return null|int
     */
    protected function getStoreId()
    {
        return $this->checkoutSession->getQuote()->getStoreId();
    }

    /**
     * Generate clientTransId modal param
     *
     * @return string
     */
    public function getDigitalBuyClientTransId()
    {
        return 'M2_' . $this->synchronyConfig->getModuleVersion() . '_' . $this->checkoutSession->getQuoteId();
    }

    /**
     * Retrieve address to use in modals initialization
     *
     * @return AbstractAddress
     */
    public function getAddress()
    {
        $quote = $this->checkoutSession->getQuote();
        $addresType = $this->synchronyConfig->getAddressTypeToPass($quote->getStoreId());
        $address = $addresType == AbstractAddress::TYPE_SHIPPING
            ? $quote->getShippingAddress() : $quote->getBillingAddress();

        // fallback to address book if none of necessary information set in quote
        if (!($address->getFirstname() || $address->getLastname() || $address->getPostcode())
            && $this->customerSession->isLoggedIn() && $quote->getCustomerId()) {
            $customerAddress = $addresType == AbstractAddress::TYPE_SHIPPING
                ? $this->customerAccountManagement->getDefaultShippingAddress($quote->getCustomerId())
                : $this->customerAccountManagement->getDefaultBillingAddress($quote->getCustomerId());
            if ($customerAddress) {
                $address = $customerAddress;
            }
        }

        return $address;
    }

    /**
     * Get URL where to send user once they've done with auth modal
     *
     * @return string
     */
    public function getAuthCompleteUrl()
    {
        return $this->urlBuilder->getUrl('digitalbuy/revolving/modal_auth_complete');
    }

    /**
     * Check if authentication was successful
     *
     * @return bool
     */
    public function isAuthSuccessful()
    {
        return (bool)$this->coreRegistry->registry(self::AUTH_SUCCESS_REGISTRY_KEY);
    }

    /**
     * Get checkout URL
     *
     * @return string
     */
    public function getCheckoutUrl()
    {
        return $this->urlBuilder->getUrl('checkout');
    }

    /**
     * Get cart URL
     *
     * @return string
     */
    public function getCartUrl()
    {
        return $this->urlBuilder->getUrl('checkout/cart');
    }

    /**
     * Retrrive Synchrony DigitalBuy payment method code
     *
     * @return string
     */
    public function getPaymentMethodCode()
    {
        return SynchronyConfig::METHOD_CODE;
    }

    /**
     * Get address data retrieved after successful authentication
     *
     * @return array
     */
    public function getAuthAddressData()
    {
        $data = $this->coreRegistry->registry(self::ADDRESS_DATA_REGISTRY_KEY);
        if ($data) {
            if (isset($data['street']) && is_array($data['street'])) {
                $data['street'] = (object) $data['street'];
            }
        } else {
            $data = [];
        }
        return $data;
    }

    /**
     * Check if customer has addresses in address book, thus push address as new
     *
     * @return bool
     */
    public function getCreateAddressAsNew()
    {
        $quote = $this->checkoutSession->getQuote();
        return $quote->getCustomerId() && $quote->getCustomer()->getDefaultBilling();
    }

    /**
     * Check if quote is virtual
     *
     * @return bool
     */
    public function isQuoteVirtual()
    {
        return $this->checkoutSession->getQuote()->isVirtual();
    }
}
