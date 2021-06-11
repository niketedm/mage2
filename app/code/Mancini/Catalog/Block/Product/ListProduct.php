<?php

declare(strict_types=1);

namespace Mancini\Catalog\Block\Product;

class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
{

  /**
   * @var \Mancini\Frog\Helper\ApiCall
   */
  protected $_apiCallHelper;

  public function __construct(
    \Magento\Catalog\Block\Product\Context $context,
    \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
    \Magento\Catalog\Model\Layer\Resolver $layerResolver,
    \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
    \Magento\Framework\Url\Helper\Data $urlHelper,
    \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet,
    \Magento\Framework\Registry $registry,
    \Mancini\Frog\Helper\ApiCall $apiCallHelper,
    \Mancini\Catalog\Helper\Data $productLabelHelper,
    \Mancini\Storelocator\Helper\Data $nearestStoreHelper,
    \Mancini\ShippingZone\Helper\Data $shippingZoneHelper,
    \Mancini\PowerReviews\Helper\Data $powerReviewsHelper,
    array $data = []
  ) {
    parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);
    $this->_attributeSet = $attributeSet;
    $this->_registry = $registry;
    $this->_apiCallHelper = $apiCallHelper;
    $this->_productLabelHelper = $productLabelHelper;
    $this->_nearestStoreHelper = $nearestStoreHelper;
    $this->_shippingZoneHelper = $shippingZoneHelper;
    $this->_powerReviewsHelper = $powerReviewsHelper;
  }

  /**
   * Function to retrieve the sale label information from admin configuration
   * @return string
   */

  public function getOnSaleLabel($product)
  {
    $deal = $product->getResource()->getAttribute('deal')->getFrontend()->getValue($product);

    if($deal == "Yes") {
      return $this->_productLabelHelper->getSaleLabel();
    }
  }

  /**
   * Function to retrieve the free delivery label information from admin configuration
   * @return string
   */

  public function getFreeDeliveryLabel($product)
  {

    $attribute_set = $this->_attributeSet->get($product->getAttributeSetId());
    $set_name = $attribute_set->getAttributeSetName();
    $shipping_zone = $this->getShippingZone();
    $category = $this->getFirstLevelCategory();

    if ($product->getTypeId() == 'configurable') {
      $base_price = $product->getPriceInfo()->getPrice('regular_price');
      $config_price = $base_price->getMinRegularAmount()->getValue();
      if ($config_price > 299 && strtolower($set_name) == 'mattress' && $shipping_zone == 'zone 1') {
        return $this->_productLabelHelper->getDeliveryLabel();
      } else if (str_contains($category, 'bedding') && ($shipping_zone == 'zone 1' || $shipping_zone == 'zone 2')) {
        return $this->_productLabelHelper->getDeliveryLabel();
      }
    } else {
      $price = $product->getPriceInfo()->getPrice('regular_price')->getValue();
      if ($price > 299 && strtolower($set_name) == 'mattress' && $shipping_zone == 'zone 1') {
        return $this->_productLabelHelper->getDeliveryLabel();
      } else if (str_contains($category, 'bedding') && ($shipping_zone == 'zone 1' || $shipping_zone == 'zone 2')) {
        return $this->_productLabelHelper->getDeliveryLabel();
      }
    }
  }

  /**
   * Function to retrieve the parent category information
   * @return string
   */
  public function getFirstLevelCategory()
  {
    $parent_category = "";
    $category = $this->_registry->registry('current_category');
    if ($category->getParentCategories()) {
      foreach ($category->getParentCategories() as $parent) {
        if ($parent->getLevel() == 2) {
          $parent_category = $parent->getName();
        }
      }
    }
    return strtolower($parent_category);
  }
  /**
   * Function to retrieve the zone information from ShippingZone module
   * @return string
   */
  public function getShippingZone()
  {

    $customer_location = $this->_nearestStoreHelper->getCookie("zipcode");

    if (!empty($customer_location)) {
      $customerLocation = explode(" ", $customer_location);
      $customer_zipcode = $customerLocation[1];

      $zone_details = $this->_shippingZoneHelper->getShippingZoneByZipcode($customer_zipcode);

      if (!empty($zone_details)) {
        foreach ($zone_details as $zone) {
          return strtolower($zone['zone_name']);
        }
      } else {
        return null;
      }
    } else {
      return null;
    }
  }

  /**
   * Function to retrieve the average rating from the Power Reviews Helper
   * @return string
   */
  public function getProductRating($product)
  {

    $productSku = preg_replace("/[^a-zA-Z0-9-]/", "", $product->getData()['sku']);
    return $this->_powerReviewsHelper->getProductReviewSnippet($productSku);
  }

  /**
   * Function to retrieve the arrival date from FROG
   * @return string
   */
  public function getArrivalDate($product)
  {

    $skuSerial = $product->getResource()->getAttribute('sku_serial')->getFrontend()->getValue($product);

    $params['item'] = $skuSerial;

    $result = $this->_apiCallHelper->inetWebsiteItemDetail (\Zend_Http_Client::POST, $params);

    $itemDetails = json_decode($result->getBody(),true);
    $itemError = $itemDetails['errors'];
    if($itemError == "") {
      $itemStatus = $itemDetails['inventoryrec'];
      $status = $itemStatus['status'];

      if($status == 'CR') {
        $DeliveryDate = date('M d', strtotime(' +1 day'));
        return $DeliveryDate;
      }
      else if ($status == 'CO' || $status == 'SPO') {
        $availableDate = $itemDetails['availability'];
        $DeliveryDate = date_format($availableDate,"M d");
        return $DeliveryDate;
      }
      else {
        return null;
      }
    }
    else {
      return null;
    }
  }
}
