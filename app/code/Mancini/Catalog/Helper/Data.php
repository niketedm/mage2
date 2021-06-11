<?php

namespace Mancini\Catalog\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper {

  /**
  * @var \Magento\Framework\App\Config\ScopeConfigInterface
  */
  protected $scopeConfig;

  public function __construct(
      \Magento\Framework\App\Helper\Context $context,
      \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
      \Magento\Framework\View\Layout $layout,
      \Magento\Framework\View\Result\Page $pageResult
  ) {
      parent::__construct($context);
      $this->scopeConfig = $scopeConfig;
      $this->_layout = $layout;
      $this->_pageResult = $pageResult;
  }

  /**
   * Fucntion to get the config value
   * @return string
   */
  public function getConfig($config_path)
  {
      return $this->scopeConfig->getValue(
              $config_path,
              \Magento\Store\Model\ScopeInterface::SCOPE_STORE
              );
  }

  /**
   * get On Sale Label
   * @return string
   */
  public function getSaleLabel() {
      return $this->getConfig('mancini_catalog/general/on_sale');
  }

  /**
   * get Free Delivery Label
   * @return string
   */
  public function getDeliveryLabel() {
      return $this->getConfig('mancini_catalog/general/free_delivery');
  }

  /**
  * get current page layout
  * @return string
  */
  public function getCurrentPageLayout() {
    $currentPageLayout = $this->_pageResult->getConfig()->getPageLayout();

    if (is_null($currentPageLayout)) {
        return $this->_layout->getUpdate()->getPageLayout();
    }
    return $currentPageLayout;
  }
}
