<?php

namespace Mancini\Checkout\Observer;

use Magento\Framework\Event\ObserverInterface;

class OrderObserver implements ObserverInterface
{
    /**
     * Order Model
     *
     * @var \Magento\Sales\Model\Order $order
     */
    protected $order;

    /**
     * @var \Mancini\Frog\Helper\ApiCall 
     */
    protected $_apiCallHelper;

    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $regionFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Mancini\ShippingZone\Helper\Data
     */
    protected $shippingzoneHelper;

     public function __construct(
        \Magento\Sales\Model\Order $order,
        \Mancini\Frog\Helper\ApiCall $apiCallHelper,
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Customer\Model\Session  $customerSession,
        \Mancini\ShippingZone\Helper\Data $shippingzoneHelper
    )
    {
        $this->order = $order;
        $this->_apiCallHelper = $apiCallHelper;
        $this->quoteRepository = $quoteRepository;
        $this->regionFactory = $regionFactory;
        $this->customerSession = $customerSession;
        $this->shippingzoneHelper = $shippingzoneHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
            
        try {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/deltype.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);

            $orderId = $observer->getEvent()->getOrderIds();
            $order = $this->order->load($orderId);

            $payment = $order->getPayment();
                //Get Payment Info
            $paymentMethodCode = $payment->getMethodInstance()->getCode();
            $paymentMethodTitle = $payment->getMethodInstance()->getTitle();
        
            if($paymentMethodCode == 'rootways_authorizecim_option' || $paymentMethodCode  == 'synchrony_digitalbuy' ){
                //Get card type
             
                $cardType = $payment->getData('cc_type');
                $cardExpMonth = $order->getPayment()->getCcExpMonth(); 
                $cardExpYear = $order->getPayment()->getCcExpYear();
                $cardFourDigits = $order->getPayment()->getCcLast4();

                if($paymentMethodCode == 'rootways_authorizecim_option'){
                $paymentCCTypes = array("AE"=>"American Express", "VI"=>"Visa", "MC"=>"MasterCard", "DI"=>"Discover", "JCB"=>"JCB");
                
                    foreach ( $paymentCCTypes as $key=>$value ) {
                        if ( $key === $cardType) {
                            $ccType = $value;
                        }
                    }
                $cardType = strtoupper($ccType);
                }

                if(!empty($cardExpMonth) && !empty($cardExpYear)){
                    $cardExpDate = $cardExpMonth.$cardExpYear;
                }else{
                    $cardExpDate = '';
                }
       
                $billingAddress = $order->getBillingAddress();
                $shippingAddress = $order->getShippingAddress();
                $quote = $this->quoteRepository->get($order->getQuoteId());
                    //$items = $quote->getAllItems();
                
               
                if($quote->getShippingAddress()->getShippingMethod() == "zonerate_instore"){
                        $delType = 2;
                }else{
                    $excludeKeys = array('entity_id', 'customer_address_id', 'quote_address_id', 'region_id', 'customer_id', 'address_type');
                    $oBillingAddress = $order->getBillingAddress()->getData();
                    $oShippingAddress = $order->getShippingAddress()->getData();
                    $oBillingAddressFiltered = array_diff_key($oBillingAddress, array_flip($excludeKeys));
                    $oShippingAddressFiltered = array_diff_key($oShippingAddress, array_flip($excludeKeys));

                    $addressDiff = array_diff($oBillingAddressFiltered, $oShippingAddressFiltered);

                    if( $addressDiff ) { // billing and shipping addresses are different
                        $delType = 3;
                    }else{
                        $delType = 1;
                    }
               }

               

                //get Order All Item
                $itemCollection = $order->getItemsCollection();
                $customer = $order->getCustomerId(); // using this id you can get customer name
                $orderids = $observer->getEvent()->getOrderIds();
    
        
                foreach ($itemCollection as $item) {
                    $product = $item->getProduct();//if you need it
                    //your magic here.
                    
                    $sku  = $product->getSku();
                    $qty  = $item->getQtyOrdered();
                    $cart[] = array('skunumber'=> $sku, 'qty'=> $qty);
                    
                }

                $virtualItems = array("zonerate_standard"=>"MAN-DELIVERY", "zonerate_premium"=>"MAN-PREMIUM-DELIVERY", "freeshipping_freeshipping"=>"MAN-DISCOUNT");
                foreach ( $virtualItems as $key=>$value ) {
                    if ( $key === $quote->getShippingAddress()->getShippingMethod()) {
                        $virtualDeliveryProduct = $value;
                    }else{
                        $virtualDeliveryProduct = '';
                    }
                }
            
                if($virtualDeliveryProduct){
                    $logger->info($virtualDeliveryProduct);
                    $params['item'] = $virtualDeliveryProduct;

                    $result = $this->_apiCallHelper->inetWebsiteItemDetail (\Zend_Http_Client::POST, $params);

                    $itemDetails = json_decode($result->getBody(),true);
                    if($virtualDeliveryProduct == "MAN-DISCOUNT"){
                        $zone = $this->shippingzoneHelper->getShippingZoneByZipcode($shippingAddress['postcode']);
                        $freeDelPrice= '';
                        $logger->info($shippingAddress['postcode']);
                        if($zone){
                            $logger->info($zone['id']);
                            $freeDelPrice = -$zone['standard_shipping_cost'];
                        
                        $cart[] = array('skunumber'=>  $itemDetails['sku'], 'qty'=> 1, 'retail'=> $freeDelPrice );//$itemDetails['inventoryrec']['retail'] );
                        }
                    }else{
                        $cart[] = array('skunumber'=>  $itemDetails['sku'], 'qty'=> 1, 'retail'=> $order['shipping_amount'] );//$itemDetails['inventoryrec']['retail'] );
                    }    
                    
                }
               
                if($order->getDiscountAmount() != 0){
                    $params['item'] = 'MAN-DISCOUNT';

                    $result = $this->_apiCallHelper->inetWebsiteItemDetail (\Zend_Http_Client::POST, $params);

                    $itemDetails = json_decode($result->getBody(),true);
                    $cart[] = array('skunumber'=>  $itemDetails['sku'], 'qty'=> 1, 'retail'=> $order['discount_amount'] );
            
                }
                
                if($order->getFee() > 0){
                    $params['item'] = 'MAN-RECYCLE-FEE';

                    $result = $this->_apiCallHelper->inetWebsiteItemDetail (\Zend_Http_Client::POST, $params);

                    $itemDetails = json_decode($result->getBody(),true);
                    $cart[] = array('skunumber'=>  $itemDetails['sku'], 'qty'=> 1, 'retail'=> $order['fee'] );
            
                }
                
                $logger->info($quote->getShippingAddress()->getShippingMethod());
                
                $logger->info($delType);
                //$logger->info(print_r($itemDetails, true));

                $billingRegion = $this->regionFactory->create()->load($billingAddress['region_id']);
                $billingRegionCode = $billingRegion->getCode();

                $shippingRegion = $this->regionFactory->create()->load($shippingAddress['region_id']);
                $shippingRegionCode = $shippingRegion->getCode();
            
                //$customerData = $this->customerSession->getCustomer()->getData(); //get all data of customerData
                //$customerId = $this->customerSession->getCustomer()->getId();
                $customerMobile = $this->customerSession->getCustomer()->getMobile();
                //$customerEmail = $this->customerSession->getCustomer()->getEmail();
                $customerLoginToken = $this->customerSession->getCustomerToken();
               
                $data['user_name'] = preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3",  $customerMobile);
                $data['token'] =  $customerLoginToken;
                $data['cotype'] = "both";
                $data['name']   = $order['customer_firstname']." ".$order['customer_lastname'];
                $data['address']  = $billingAddress['street'];
                $data['zip']  = $billingAddress['postcode'];
                $data['city']  = $billingAddress['city'];
                $data['state']  = $billingRegionCode;
                $data['phone']  = preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $billingAddress['telephone']);
                $data['shiptofirst']  = $shippingAddress['firstname'];
                $data['shiptolast']  = $shippingAddress['lastname'];
                $data['shiptoaddr1']  = $shippingAddress['street'];
                $data['shiptocity']  = $shippingAddress['city'];
                $data['shiptostate']  = $shippingRegionCode;
                $data['shiptozip']  = $shippingAddress['postcode'];
                $data['shiptophone']  = preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $shippingAddress['telephone']);
                $data['deltype']    = $delType;
                $data['cart']  =  json_encode($cart);
                $data['email']  = $order['customer_email'];

                $logger->info( $customerMobile);
                $logger->info($data['user_name']);
                $logger->info($customerLoginToken);
                $logger->info(print_r($this->customerSession->getData(), true));
                $logger->info(print_r($this->customerSession->getCustomer()->getData(), true));
        
                $checkoutResponse = $this->_apiCallHelper->inetWebsiteCheckoutDetail($data);
                $checkoutFrogError = $checkoutResponse['errors'];
                $this->checkoutAPILog($paymentMethodCode, $paymentMethodTitle, $cardType, $cardExpMonth, $cardExpYear, $cardFourDigits, $data, $checkoutResponse);
                
                if($checkoutFrogError == "") {
                    
                }
                else {
                
                    return "Technical error.";
                }
            
                if($checkoutResponse['token']){
                    $orderData['user_name'] = preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $order['customer_mobile']);
                    $orderData['token'] = $checkoutResponse['token'];
                    $orderData['trantype'] = 'sale';
                    $orderData['cart']  = json_encode($cart); 
                    $orderData['delcharge'] = $order['shipping_amount'];
                    $orderData['tax'] =  $order['tax_amount'];
                    $orderData['amount'] = $order['grand_total'];
                    $orderData['cardtype'] = $cardType;
                    $orderData['cardno'] = $cardFourDigits;
                    if($cardExpDate){
                    $orderData['expdate'] = $cardExpDate;
                    }
                    $submitOrderResponse = $this->_apiCallHelper->inetWebsiteSubmitOrder($orderData);
                    $submitOrderFrogError = $submitOrderResponse['errors'];
                    $this->submitOrderAPILog($orderData, $submitOrderResponse);
                    if($submitOrderFrogError == "") {
                    
                    }
                    else {
                        return "Technical error.";
                    }
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            // Error logic
            //$message = $e->getMessage();
            //$this->_getSession()->addError($message);
            return "Technical error.";
        } catch (\Exception $e) {
            // Generic error logic
            //$message = $e->getMessage();
            //$this->_getSession()->addError($message);
            return "Technical error.";
        }


    }

    public function checkoutAPILog($paymentMethodCode, $paymentMethodTitle, $cardType, $cardExpMonth, $cardExpYear, $cardFourDigits, $data, $checkoutResponse){
        
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/checkoutAPI.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($paymentMethodCode);
        $logger->info($paymentMethodTitle);
        $logger->info($cardType);
        $logger->info($cardExpMonth);
        $logger->info($cardExpYear);
        $logger->info($cardFourDigits);
        //$logger->info(print_r($payment->getData(), true));
        //$logger->info($customer);
        //$logger->info(print_r($itemCollection->getData(), true));
        //$logger->info(print_r($quote->getData(), true));
        //$logger->info(print_r($order->getData(), true));
        //$logger->info(print_r($billingAddress->getData(), true));
        //$logger->info(print_r($shippingAddress->getData(), true));
        $logger->info(print_r($data, true));
        $logger->info(print_r(json_encode($data), true));
        $logger->info(print_r($checkoutResponse, true));
        $logger->info(print_r(json_encode($checkoutResponse), true));
        if($checkoutResponse['token']){
        $logger->info(print_r($checkoutResponse['token'], true));
        }

    }

   
    
    public function submitOrderAPILog($orderData, $submitOrderResponse){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/submitOrderAPI.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(print_r($orderData, true));
        $logger->info(print_r(json_encode($orderData), true));
        $logger->info(print_r($submitOrderResponse, true));
        $logger->info(print_r(json_encode($submitOrderResponse), true));
    }
 
   

}