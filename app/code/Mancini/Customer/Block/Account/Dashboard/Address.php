<?php

namespace Mancini\Customer\Block\Account\Dashboard;

class Address extends \Magento\Customer\Block\Account\Dashboard\Address {

  /**
   * @var \Magento\Customer\Helper\Session\CurrentCustomer
   */
  protected $currentCustomer;

  /**
   * @var \Magento\Customer\Helper\Session\CurrentCustomerAddress
   */
  protected $currentCustomerAddress;

  /**
   * @var \Magento\Customer\Model\Address\Config
   */
  protected $_addressConfig;

  /**
   * @var \Magento\Customer\Model\Address\Mapper
   */
  protected $addressMapper;

  public function __construct(
      \Magento\Framework\View\Element\Template\Context $context,
      \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
      \Magento\Customer\Helper\Session\CurrentCustomerAddress $currentCustomerAddress,
      \Magento\Customer\Model\Address\Config $addressConfig,
      \Magento\Customer\Model\Address\Mapper $addressMapper,
      array $data = []
  ) {
      $this->currentCustomer = $currentCustomer;
      $this->currentCustomerAddress = $currentCustomerAddress;
      $this->_addressConfig = $addressConfig;
      $this->addressMapper = $addressMapper;
      parent::__construct($context, $currentCustomer, $currentCustomerAddress, $addressConfig, $addressMapper, $data);
  }

  /**
   * Shipping Address extracted from the core HTML
   *
   * @return \Magento\Framework\Phrase|string
   */
  public function getPrimaryShippingAddressHtml()
  {
      try {
          $address = $this->currentCustomerAddress->getDefaultShippingAddress();
      } catch (NoSuchEntityException $e) {
          return __('You have not set a default shipping address.');
      }

      if ($address) {
          return $address;
      } else {
          return __('You have not set a default shipping address.');
      }
  }

  /**
   * Billing Address extracted from the core HTML
   *
   * @return \Magento\Framework\Phrase|string
   */
  public function getPrimaryBillingAddressHtml()
  {
      try {
          $address = $this->currentCustomerAddress->getDefaultBillingAddress();

      } catch (NoSuchEntityException $e) {
          return __('You have not set a default billing address.');
      }

      if ($address) {
          return $address;
      } else {
          return __('You have not set a default billing address.');
      }
  }
}
