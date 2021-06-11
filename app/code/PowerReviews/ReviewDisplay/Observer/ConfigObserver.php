<?php
namespace PowerReviews\ReviewDisplay\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ConfigObserver implements ObserverInterface
{
  protected $_logger;

  /**
   * @var \PowerReviews\ReviewDisplay\Helper\Data
	 */
  protected $_helper;

  /**
   * @var \Magento\Framework\ObjectManagerInterface
   */
  protected $_objectManager;

	/**
   * @param \PowerReviews\ReviewDisplay\Helper\Data $helper
   */
	public function __construct(
      \Psr\Log\LoggerInterface $logger,
      \PowerReviews\ReviewDisplay\Helper\Data $helper,
      \Magento\Framework\ObjectManagerInterface $objectManager
	){
      $this->_logger = $logger;
      $this->_helper = $helper;
      $this->_objectManager = $objectManager;
	}

	/**
	 * This is the method that fires when the event runs.
	 *
	 * @param Observer $observer
	 */
	public function execute(Observer $observer) {
		$this->_updateWrapperUrl();
	}

  private function _updateWrapperUrl() {
    $url = $this->_buildUrl();
    $this->_logger->info('SharedServicesWrapperUrl: ' . $url);

    $data = array(
      "merchant_group_id" => $this->_helper->getMerchantGroupId(),
      "wrapper_url" => $url
    );

    $dataJson = json_encode($data);

    $ch = curl_init();

    $apiUrl = 'https://portal.powerreviews.com/api/v1/merchant_group/wrapper_url';

    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

    $response = curl_exec($ch);


    $this->_logger->info('SharedServicesWrapperUrlResponse: ', (array)json_decode($response, true));

    curl_close($ch);
  }

  private function _buildUrl() {
    $queryParams = array(
      'pr_page_id' => '___PAGE_ID___',
      'pr_page_id_variant' => '___VARIANT___',
      'appName' => 'ryp'
    );

    $url = $this->_getStoreUrl() . 'pages/write-a-review?' . http_build_query($queryParams);

    return $url;
  }

  private function _getStoreUrl() {
    return $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getBaseUrl();
  }
}
