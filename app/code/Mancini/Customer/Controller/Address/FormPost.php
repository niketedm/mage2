<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mancini\Customer\Controller\Address;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\RegionInterface;
use Magento\Customer\Api\Data\RegionInterfaceFactory;
use Magento\Customer\Model\Address\Mapper;
use Magento\Customer\Model\Metadata\FormFactory;
use Magento\Customer\Model\Session;
use Magento\Directory\Helper\Data as HelperData;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\View\Result\PageFactory;

/**
 * Customer Address Form Post Controller
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FormPost extends \Magento\Customer\Controller\Address\FormPost
{
  /**
   * @var RegionFactory
   */
  protected $regionFactory;

  /**
   * @var HelperData
   */
  protected $helperData;

  /**
   * @var Mapper
   */
  private $customerAddressMapper;

  /**
   * @param Context $context
   * @param Session $customerSession
   * @param FormKeyValidator $formKeyValidator
   * @param FormFactory $formFactory
   * @param AddressRepositoryInterface $addressRepository
   * @param AddressInterfaceFactory $addressDataFactory
   * @param RegionInterfaceFactory $regionDataFactory
   * @param DataObjectProcessor $dataProcessor
   * @param DataObjectHelper $dataObjectHelper
   * @param ForwardFactory $resultForwardFactory
   * @param PageFactory $resultPageFactory
   * @param RegionFactory $regionFactory
   * @param HelperData $helperData
   * @SuppressWarnings(PHPMD.ExcessiveParameterList)
   */
  public function __construct(
      Context $context,
      Session $customerSession,
      FormKeyValidator $formKeyValidator,
      FormFactory $formFactory,
      AddressRepositoryInterface $addressRepository,
      AddressInterfaceFactory $addressDataFactory,
      RegionInterfaceFactory $regionDataFactory,
      DataObjectProcessor $dataProcessor,
      DataObjectHelper $dataObjectHelper,
      ForwardFactory $resultForwardFactory,
      PageFactory $resultPageFactory,
      RegionFactory $regionFactory,
      HelperData $helperData,
      \Mancini\Frog\Helper\ApiCall $apiCallHelper
  ) {
      $this->regionFactory = $regionFactory;
      $this->helperData = $helperData;
      $this->customerSession = $customerSession;
      $this->_apiCallHelper = $apiCallHelper;
      parent::__construct(
          $context,
          $customerSession,
          $formKeyValidator,
          $formFactory,
          $addressRepository,
          $addressDataFactory,
          $regionDataFactory,
          $dataProcessor,
          $dataObjectHelper,
          $resultForwardFactory,
          $resultPageFactory,
          $regionFactory,
          $helperData
      );
  }
    /**
     * Process address form save
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
      $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/customer.log');
      $logger = new \Zend\Log\Logger();
      $logger->addWriter($writer);
      $logger->info("Customer address edit event called");

        $redirectUrl = null;
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        if (!$this->getRequest()->isPost()) {
            $this->_getSession()->setAddressFormData($this->getRequest()->getPostValue());
            return $this->resultRedirectFactory->create()->setUrl(
                $this->_redirect->error($this->_buildUrl('*/*/edit'))
            );
        }

        try {
            $address = $this->_extractAddress();
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/customer.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info("Phone: ".$address->getTelePhone());
            $logger->info("Name: ".$address->getFirstName()." ".$address->getLastName());
            $logger->info("First name: ".$address->getFirstName());
            $logger->info("Last name: ".$address->getLastName());
            $logger->info("Address: ".implode($address->getStreet()));
            $logger->info("City: ".$address->getCity());
            $region = $address->getRegion();
            $logger->info("State: ".$regionCode = $region->getRegionCode());
            $logger->info("Zip code: ".$address->getPostcode());
            $logger->info("Customer email: ".$this->customerSession->getCustomer()->getEmail());
            $logger->info("Default billing: ".$address->isDefaultBilling());
            $logger->info("Default shipping: ".$address->isDefaultShipping());
            $logger->info("Login token: ".$this->_customerSession->getCustomerToken());

            $billing_params['type'] = 'billing';
            $billing_params['user_name'] = $this->customerSession->getCustomer()->getEmail();
            $billing_params['token'] = $this->_customerSession->getCustomerToken();
            //$billing_params['billtoname'] = $address->getFirstName()." ".$address->getLastName();
            $billing_params['billtofirst'] = $address->getFirstName();
            $billing_params['billtolast'] = $address->getLastName();
            $billing_params['billtoaddress'] = implode($address->getStreet());
            $billing_params['billtocity'] = $address->getCity();
            $billing_params['billtostate'] = $region->getRegionCode();
            $billing_params['billtozip'] = $address->getPostcode();
            $billing_params['billtophone'] = $address->getTelePhone();

            $shipping_params['type'] = 'shipping';
            $shipping_params['user_name'] = $this->customerSession->getCustomer()->getEmail();
            $shipping_params['token'] = $this->_customerSession->getCustomerToken();
            //$shipping_params['billtoname'] = $address->getFirstName()." ".$address->getLastName();
            $shipping_params['shiptofirst'] = $address->getFirstName();
            $shipping_params['shiptolast'] = $address->getLastName();
            $shipping_params['shiptoaddress'] = implode($address->getStreet());
            $shipping_params['shiptocity'] = $address->getCity();
            $shipping_params['shiptostate'] = $region->getRegionCode();
            $shipping_params['shiptozip'] = $address->getPostcode();
            $shipping_params['shiptophone'] = $address->getTelePhone();


            if(($address->isDefaultBilling()) && ($address->isDefaultShipping())) {
              $logger->info("Both billing and shipping are default");

              $result = $this->_apiCallHelper->inetWebsiteUpdateBilling(\Zend_Http_Client::POST, $billing_params);

              $billingDetails = json_decode($result->getBody(),true);
              $billingError = $billingDetails['errors'];

              if($billingError == "") {
                $logger->info("Billing address updated");
              }
              else {
                return "Technical error.";
              }

              $result = $this->_apiCallHelper->inetWebsiteUpdateShipping(\Zend_Http_Client::POST, $shipping_params);

              $shippingDetails = json_decode($result->getBody(),true);
              $shippingError = $shippingDetails['errors'];

              if($shippingError == "") {
                $logger->info("Shipping address updated");
              }
              else {
                return "Technical error.";
              }
            }
            else if($address->isDefaultBilling()) {
              $logger->info("Billing is default");

              $result = $this->_apiCallHelper->inetWebsiteUpdateBilling(\Zend_Http_Client::POST, $billing_params);

              $billingDetails = json_decode($result->getBody(),true);
              $billingError = $billingDetails['errors'];

              if($billingError == "") {
                $logger->info("Billing address updated");
              }
              else {
                return "Technical error.";
              }
            }
            else if($address->isDefaultShipping()) {
              $logger->info("Shipping is default");

              $result = $this->_apiCallHelper->inetWebsiteUpdateShipping(\Zend_Http_Client::POST, $shipping_params);

              $shippingDetails = json_decode($result->getBody(),true);
              $shippingError = $shippingDetails['errors'];

              if($shippingError == "") {
                $logger->info("Shipping address updated");
              }
              else {
                return "Technical error.";
              }
            }
            else {
              $logger->info("None default");
            }

            $this->_addressRepository->save($address);
            $this->messageManager->addSuccessMessage(__('You saved the address.'));
            $url = $this->_buildUrl('*/*/index', ['_secure' => true]);
            return $this->resultRedirectFactory->create()->setUrl($this->_redirect->success($url));
        } catch (InputException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            foreach ($e->getErrors() as $error) {
                $this->messageManager->addErrorMessage($error->getMessage());
            }
        } catch (\Exception $e) {
            $redirectUrl = $this->_buildUrl('*/*/index');
            $this->messageManager->addExceptionMessage($e, __('We can\'t save the address.'));
        }

        $url = $redirectUrl;
        if (!$redirectUrl) {
            $this->_getSession()->setAddressFormData($this->getRequest()->getPostValue());
            $url = $this->_buildUrl('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }

        return $this->resultRedirectFactory->create()->setUrl($this->_redirect->error($url));
    }
}
