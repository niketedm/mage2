<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Mancini\Checkout\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;

class Index extends  \Magento\Checkout\Controller\Index\Index
{
    /**
     * Checkout page
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

       //print_r('From Mancini Module');
       //exit;
       /* $checkoutData = $this->getCheckoutDetail();
        print_r(json_encode($checkoutData));
        exit;*/
        /*
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/index1.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('index1');*/
        

        /** @var \Magento\Checkout\Helper\Data $checkoutHelper */
        $checkoutHelper = $this->_objectManager->get(\Magento\Checkout\Helper\Data::class);
        if (!$checkoutHelper->canOnepageCheckout()) {
            $this->messageManager->addErrorMessage(__('One-page checkout is turned off.'));
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }

        $quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError() || !$quote->validateMinimumAmount()) {
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }

        if (!$this->_customerSession->isLoggedIn() && !$checkoutHelper->isAllowedGuestCheckout($quote)) {
            $this->messageManager->addErrorMessage(__('Guest checkout is disabled.'));
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }

        // generate session ID only if connection is unsecure according to issues in session_regenerate_id function.
        // @see http://php.net/manual/en/function.session-regenerate-id.php
        if (!$this->isSecureRequest()) {
            $this->_customerSession->regenerateId();
        }
        $this->_objectManager->get(\Magento\Checkout\Model\Session::class)->setCartWasUpdated(false);
        $this->getOnepage()->initCheckout();
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Checkout'));
        return $resultPage;
    }

    /**
     * Checks if current request uses SSL and referer also is secure.
     *
     * @return bool
     */
    private function isSecureRequest(): bool
    {
        $request = $this->getRequest();

        $referrer = $request->getHeader('referer');
        $secure = false;

        if ($referrer) {
            $scheme = parse_url($referrer, PHP_URL_SCHEME);
            $secure = $scheme === 'https';
        }

        return $secure && $request->isSecure();
    }


    /**
     * Function to retrieve the Checkout Details from FROG
     * @return string
     */

    public function getCheckoutDetail()
    {

         /** @var \Magento\Checkout\Helper\Data $checkoutHelper */
        $apiCallHelper = $this->_objectManager->get(\Mancini\Frog\Helper\ApiCall::class);

       /* $loginData     = array();
        $params ['user_name']     =   "vetri@example.com";
        $params ['user_pw']       =    "Test@1234";

        $result     =   $apiCallHelper->inetWebsiteLoginDetail(\Zend_Http_Client::POST, $params);

        $response   =   $result->getBody();
        print_r($response);
        exit;*/
        /*$params ['item'] = '4155976';

        $result = $apiCallHelper->inetWebsiteItemDetail (\Zend_Http_Client::POST, $params);
        $itemDetails = json_decode($result->getBody(),true);
        return $itemDetails;*/

        $checkoutData     = array();
        /*$params ['cotype']        =    'initialize';
        $params ['deltype']       =     1;
        $params ['deldate']       =     "";
        $cart                     = array("skuserial"=>"4155976", "qty"=>"1");
        $params ['cart']          =  $cart;//json_encode($cart);

        

        $params['user_name']           = "vetri@example.com";
        $params['token']               = "88F2B8CC81B6415FAF7017AF2482F5F7";
        $params['cotype']              = "both";
        $params['name']                =  "vetri vel";
        $params['address']             = "2324 N HWY 16";
        $params['zip']                 = "28037";
        $params['city']                = "DENVER";
        $params['state']               = "NC";
        $params['phone']               = 999-484-9341;
        $params['email']               = "vetri@example.com";
        $params['password']            = "Test@1234";
        $params['shiptoname']          = "vetri vel";
        $params['shiptoaddr1']         = "2324 N HWY 16";
        $params['shiptozip']           = "28037";
        $params['shiptocity']          = "DENVER";
        $params['shiptostate']         = "NC";
        $params['shiptophone']         = 999-484-9341;
        $params['cart']                = array("skuserial"=>"4155976", "qty"=>"1");*/
        //return $params;
        //$result     =   $apiCallHelper->inetWebsiteCheckoutDetail(\Zend_Http_Client::POST, $params);
        $result     =   $apiCallHelper->inetWebsiteSubmitOrder();
       //$result     =   $apiCallHelper->inetWebsiteCheckoutDetail();
        //$response   =   $result->getBody();
        return $result;

        $details     =   $this->resolveQuotes ($response);
        
        foreach($details as $key=>$value){
            $key = str_replace('"','',$key);
            $value = str_replace('"','',$value);
            if(trim($key) == "errors"){
                $checkoutData['errors']     =  $value;
            }
            if(trim($key) == "token"){
                $checkoutData['token']     =  $value;
            }
        }
        return $checkoutData;
    }

   

     /**
     * Function to resolve the double quotes in API response
     * @param string
     * @return string
     */
    public function resolveQuotes ($response) 
    {
        $finalArr = array();
        $response = str_replace("{", " ", $response);
        $response = str_replace("}", " ", $response);

        $reArr = explode("cusinfo", $response);   
        $errToken = explode(",",$reArr[0]);
        foreach($errToken as $key=>$value){
            $eachString = explode(":",$value);   
            $keyValue = trim($eachString[0]);
            if(strlen($keyValue) >2  && $keyValue != ""){
                $finalArr[trim($eachString[0])] = trim($eachString[count($eachString)-1]);
            }
        }

        return $finalArr;
    }
}
