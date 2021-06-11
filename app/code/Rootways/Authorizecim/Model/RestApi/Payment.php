<?php
/**
 * Authorize.net Payment Module.
 *
 * @category  Payment Integration
 * @package   Rootways_Authorizecim
 * @author    Developer RootwaysInc <developer@rootways.com>
 * @copyright 2021 Rootways Inc. (https://www.rootways.com)
 * @license   Rootways Custom License
 * @link      https://www.rootways.com/pub/media/extension_doc/license_agreement.pdf
 */

namespace Rootways\Authorizecim\Model\RestApi;

use Rootways\Authorizecim\Api\PaymentInterface as RwPaymentInterface;

use Magento\Framework\Exception\LocalizedException;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

class Payment implements RwPaymentInterface
{
    
     public function __construct(
         \Magento\Checkout\Model\Cart $cart,
         StoreManagerInterface $storeManager,
         ProductRepositoryInterface $productRepository,
         \Magento\Framework\Event\ManagerInterface $eventManager,
         \Rootways\Authorizecim\Helper\Data $customHelper
     )
     {
         $this->cart = $cart;
         $this->storeManager = $storeManager;
         $this->productRepository = $productRepository;
         $this->eventManager = $eventManager;
         $this->customHelper = $customHelper;
     }
    
    public function addtocart($request, $shipping_id = null)
    {
        $params = [];
        parse_str($request, $params);

        $productId = $params['product'];
        $related = $params['related_product'];

        if (isset($params['qty'])) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $filter = new \Zend_Filter_LocalizedToNormalized(
                ['locale' => $objectManager->create('Magento\Framework\Locale\ResolverInterface')->getLocale()]
            );
            $params['qty'] = $filter->filter($params['qty']);
        }

        $quote = $this->cart->getQuote();

        try {
            // Get Product
            $storeId = $this->storeManager->getStore()->getId();
            $product = $this->productRepository->getById($productId, false, $storeId);

            $this->eventManager->dispatch(
                'rwauthorize_payments_applepay_before_add_to_cart',
                ['product' => $product, 'request' => $request]
            );

            // Check is update required
            $isUpdated = false;
            foreach ($quote->getAllItems() as $item) {
                if ($item->getProductId() == $productId) {
                    $item = $this->cart->updateItem($item->getId(), $params);
                    if ($item->getHasError()) {
                        throw new LocalizedException(__($item->getMessage()));
                    }

                    $isUpdated = true;
                    break;
                }
            }

            // Add Product to Cart
            if (!$isUpdated) {
                $item = $this->cart->addProduct($product, $params);
                if ($item->getHasError()) {
                    throw new LocalizedException(__($item->getMessage()));
                }

                if (!empty($related)) {
                    $this->cart->addProductsByIds(explode(',', $related));
                }
            }

            $this->cart->save();

            if ($shipping_id) {
                // Set Shipping Method
                if (!$quote->isVirtual()) {
                    // Set Shipping Method
                    $quote->getShippingAddress()->setShippingMethod($shipping_id)
                             ->setCollectShippingRates(true)
                             ->collectShippingRates();
                }
            }

            // Update totals
            $quote->setTotalsCollectedFlag(false);
            $quote->collectTotals();
            $quote->save();

            $result = $this->customHelper->getQuote($quote);
            return \Zend_Json::encode([
                //"paymentIntent" => $this->paymentIntent->create()->getClientSecret(),
                //"results" => $result
                "total" => $quote->getGrandTotal(),
                "quoteid" => $quote->getId(),
            ]);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()), $e);
        }
    }
}