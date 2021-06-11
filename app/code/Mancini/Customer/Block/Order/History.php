<?php

declare(strict_types=1);

namespace Mancini\Customer\Block\Order;

class History extends \Magento\Sales\Block\Order\History {

  /**
   * @var \Mancini\Frog\Helper\ApiCall
   */
   protected $_apiCallHelper;

  /**
   * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
   */
  protected $_orderCollectionFactory;

  /**
   * @var \Magento\Customer\Model\Session
   */
  protected $_customerSession;

  /**
   * @var \Magento\Sales\Model\Order\Config
   */
  protected $_orderConfig;

  /**
   * @var \Magento\Sales\Model\ResourceModel\Order\Collection
   */
  protected $orders;

  /**
   * @var CollectionFactoryInterface
   */
  private $orderCollectionFactory;

  public function __construct(
      \Mancini\Frog\Helper\ApiCall $apiCallHelper,
      \Magento\Catalog\Block\Product\Context $context,
      \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
      \Magento\Customer\Model\Session $customerSession,
      \Magento\Sales\Model\Order\Config $orderConfig,
      array $data = []
  ) {
      $this->_apiCallHelper = $apiCallHelper;
      $this->_orderCollectionFactory = $orderCollectionFactory;
      $this->_customerSession = $customerSession;
      $this->_orderConfig = $orderConfig;
      parent::__construct($context, $orderCollectionFactory, $customerSession, $orderConfig, $data);
  }

  /**
  * Function to retrieve the customer orders from the FROG
  * @return array
  */

  public function getCustomerOrders() {

    $customerEmail = $this->_customerSession->getCustomer()->getEmail();
    $loginToken = $this->_customerSession->getCustomerToken();

    $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/customer.log');
    $logger = new \Zend\Log\Logger();
    $logger->addWriter($writer);
    $logger->info("Email: ".$customerEmail." Token: ".$loginToken);

    $params['user_name'] = $customerEmail;
    $aprams['token'] = $loginToken;

    $result = $this->_apiCallHelper->inetWebsiteCustomerOrders(\Zend_Http_Client::POST, $params);

    $ordersDetails = json_decode($result->getBody(),true);
    $ordersError = $ordersDetails['errors'];

    If($ordersError == "") {
        $logger->info("If condition");
        return $ordersDetails;
    }
    else {
      $logger->info("Else condition");
      //return "Technical error.";
      return $ordersDetails;
    }
  }

  /**
  * Function to retrieve the order details from the FROG
  * @return array
  */

  public function getOrderDetails($invoice) {

    $customerEmail = $this->_customerSession->getCustomer()->getEmail();
    $loginToken = $this->_customerSession->getCustomerToken();

    $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/customer.log');
    $logger = new \Zend\Log\Logger();
    $logger->addWriter($writer);
    $logger->info("Email: ".$customerEmail." Token: ".$loginToken." Invoice: ".$invoice);


    $params['user_name'] = $customerEmail;
    $params['token'] = $loginToken;
    $params['invoice'] = $invoice;

    $result = $this->_apiCallHelper->inetWebsiteOrderDetails(\Zend_Http_Client::POST, $params);

    $orderDetails = json_decode($result->getBody(),true);
    $orderError = $orderDetails['errors'];

    if($orderError == "") {
      return $orderDetails;
    }
    else {
      return "Technical error.";
    }
  }
}
