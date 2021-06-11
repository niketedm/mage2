<?php
namespace PowerReviews\ReviewDisplay\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SalesOrderPlaceAfter implements ObserverInterface
{
	/**
   * @var \PowerReviews\ReviewDisplay\Helper\Data
	 */
  protected $_helper;

  /**
   * @var \Magento\Framework\ObjectManagerInterface
   */
  protected $_objectManager;

	protected $_responseFactory;
  protected $_url;

	/**
   * @param \PowerReviews\ReviewDisplay\Helper\Data $helper
   */
	public function __construct(
		\PowerReviews\ReviewDisplay\Helper\Data $helper,
    \Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Framework\App\ResponseFactory $responseFactory,
    \Magento\Framework\UrlInterface $url
	){
		$this->_helper = $helper;
    $this->_objectManager = $objectManager;
		$this->_responseFactory = $responseFactory;
		$this->_url = $url;
	}

	/**
	 * This is the method that fires when the event runs.
	 *
	 * @param Observer $observer
	 */
	public function execute(Observer $observer) {
		$order = $observer->getOrder();

		$this->_makeBeaconRequest($order);

		$event = $observer->getEvent();
    $redirectUrl= $this->_url->getUrl('checkout/onepage/success/');
    $this->_responseFactory->create()->setRedirect($redirectUrl)->sendResponse();
	}

	private function _makeBeaconRequest($order) {
    	$url = 'http://t.powerreviews.com/t/v1.gif';

			$helper = $this->_helper;

			$ts = time();

			$orderData = $order->getData();
			$orderItems = $order->getAllVisibleItems();

			$params = array(
		    'e' => 'c',
				'id' => $ts,
				't' => $ts,
				'uid' => $orderData['customer_email'],
		    'mgid' => $helper->getMerchantGroupId(),
		    'mid' => $helper->getMerchantId(),
		    'mgid' => $helper->getMerchantGroupId(),
		    'p' => $this->_getStoreUrl(),
		    'l' => $helper->getLocale(),
		    'oid' => $orderData['increment_id'],
		    'ue' => $orderData['customer_email'],
		    'uf' => $orderData['customer_firstname'],
		    'ul' => $orderData['customer_lastname'],
		    'os' => $orderData['base_subtotal'],
		    'on' => $orderData['total_qty_ordered'],
		    'oi' => $this->_getOrderItemList($orderItems)
			);

			$paramString = http_build_query($params);

		 	//open connection
			$ch = curl_init();

			//set the url, number of POST vars, POST data
			curl_setopt($ch, CURLOPT_URL, $url . '?' . $paramString);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // for redirects

			//execute beacon request
			$result = curl_exec($ch);

			$lastUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL); // get last effective URL

			//close connection
			curl_close($ch);
	}

  private function _getStoreUrl() {
    return $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getBaseUrl();
  }

	private function _getOrderItemList($orderItems) {
		$stringifiedItems = array();

		foreach ($orderItems as $item) {
			$productId = $item->getProductId();
			$itemData = $item->getData();


			$stringifiedItems[] = implode(',', array(
				$productId,
				$itemData['quote_item_id'],
				$itemData['name'],
				$itemData['qty_ordered'],
				$itemData['price']
			));
		}

		return implode(';', $stringifiedItems);
	}
}
