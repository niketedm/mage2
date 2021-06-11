<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mancini\Frog\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Framework\HTTP\Client\Curl;

class ApiCall extends AbstractHelper
{
    const GET_LOGIN_DETAILS     = 'inet_website_login';
    const GET_DELIVERY_DATE     = 'inet_website_set_delivery';
    const GET_PRODUCT_DETAIL    = 'inet_website_item_detail';
    const MAGENTO_BANDAID_API   = 'inet_magento_bandaid';
    const GET_CHECKOUT_DETAILS  = 'inet_website_checkout';
    const SUBMIT_ORDER          = 'inet_website_submit_order';
    const CUSTOMER_RESET_PASSWORD   = 'inet_website_customer_update';
    const CUSTOMER_ADDRESS_UPDATE   = 'inet_website_customer_update';
    const CUSTOMER_INVOICES = 'inet_website_customer_invoices';
    const CUSTOMER_INVOICE_DETAIL = 'inet_website_invoice_detail';


    /**
    * @var \Magento\Framework\App\Config\ScopeConfigInterface
    */
    protected $scopeConfig;

    /**
    * @var \Magento\Framework\HTTP\ZendClientFactory
    */
    protected $_httpClient;

    /**
     * @var Curl
     */
    protected $curlClient;

    /**
     * @param Curl $curl
     * @param \Magento\Framework\App\Helper\Context $context
     * @param  ZendClientFactory $httpClient
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        ZendClientFactory $httpClient,
        Curl $curl
    ) {
        parent::__construct($context);
        $this->_httpClient = $httpClient;
        $this->scopeConfig = $scopeConfig;
        $this->curlClient  = $curl;
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
     * get FROG API url
     * @return string
     */
    public function getAPIUrl(){
        return $this->getConfig('frog/general/api_url');
    }

    /**
     * returns FROG API token
     * @return string
     */
    public function getAPIkey(){
        return $this->getConfig('frog/general/api_key');
    }

    /**
     * Function to call the FROG API
     * @return string
     */
    public function callFrogAPI ($url, $method, $params) {
        $header = $this->getAPIkey();
        try {
            $apiCaller = $this->_httpClient->create();
            $apiCaller->setUri($url);
            $apiCaller->setMethod($method);

            $apiCaller->setHeaders([
                'Content-Type'=> 'application/json',
                'Accept' =>  'application/json',
                'Authorization' => $header,
            ]);

            $apiCaller->setParameterPost($params); //or parameter get

            return $apiCaller->request();
        } catch (\Zend\Http\Exception\RuntimeException $runtimeException) {
            return "error";
        }
    }
    /**
     * Function to call the FROG API for inet_website_set_delivery
     * @return string
     */
    public function inetWebsiteSetDeliveryDates($method, $params) {
        $url      =     $this->getAPIUrl().self::GET_DELIVERY_DATE;
        $response =     $this->callFrogAPI($url, $method, $params);

        return $response;
    }

    /**
     * Function for Item Details
     * @return string
     */

    public function inetWebsiteItemDetail($method, $params)
    {
        $url        =   $this->getAPIUrl().self::GET_PRODUCT_DETAIL;
        $response   =   $this->callFrogAPI($url, $method, $params);

        return $response;
    }

    public function inetProductSyncUp()
    {
        $url             =   $this->getAPIUrl().self::MAGENTO_BANDAID_API;
        $params['event'] =   'products';
        $method          =   \Zend_Http_Client::POST;
        $response        =   $this->callFrogAPI($url, $method, $params);

        return $response;
    }

    public function inetWebsiteCreateAccountDetail($details)
    {
        $url = $this->getAPIUrl().self::MAGENTO_BANDAID_API;
        $token = "Bearer ".$this->getAPIkey();

        $data = array(
            "event"=>"register",
            "user_email"=>$details['email'],
            "user_phone"=>$details['mobile'],
            "user_pw"=>$details['password'],
            "firstname"=>$details['firstname'],
            "lastname"=>$details['lastname'],
        );

        //set curl options
      //  $this->curl->setOption(CURLOPT_USERPWD, $username . ":" . $password);
        $this->curlClient->setOption(CURLOPT_HEADER, 0);
        $this->curlClient->setOption(CURLOPT_TIMEOUT, 60);
        $this->curlClient->setOption(CURLOPT_RETURNTRANSFER, true);
        //set curl header
        $this->curlClient->addHeader("Content-Type", "application/json");
        $this->curlClient->addHeader("Authorization", $token);
        //post request with url and data
        $this->curlClient->post($url, $data);
        //read response

        $response = json_decode($this->curlClient->getBody(), true);
        return $response;
    }

    /**
     * Function for Login Details
     * @return string
     */
    public function inetWebsiteLoginDetail($method, $params)
    {
        $url        =   $this->getAPIUrl() . self::MAGENTO_BANDAID_API;
        $response   =   $this->callFrogAPI($url, $method, $params);

        return $response;
    }

     /**
     * Function for Checkout Details
     * @return string
     */
    public function inetWebsiteCheckoutDetail($data)
    {
        $url = $this->getAPIUrl() . self::GET_CHECKOUT_DETAILS;
        $token = "Bearer ".$this->getAPIkey();

        /*$data = array(
            "cotype"=> "both",
            "name" => "Muthu Kumar",
            "address"=> "2324 N HWY 16",
            "zip"=> "95628",
            "city"=> "DENVER",
            "state"=> "CA",
            "phone"=> "910-865-5165",
            "shiptofirst"=>"Muthu",
            "shiptolast"=>"Kumar",
            "shiptoaddr1"=>"2324 N HWY 16",
            "shiptocity"=>"DENVER",
            "shiptostate"=>"CA",
            "shiptozip"=>"95628",
            "shiptophone"=> "910-865-5165",
            "cart"=> '[{"skunumber":"TEM-ADJ-ERG2-SB-3XB-20","qty":"1"},{"skunumber":"ALB-ANG-3M-LF","qty":"2"},{"skunumber":"CLA-INF-12-5M-CF","qty":"2"}]',
            "email"=> "muthukumar@example.com"
        );*/

            //set curl options
        //  $this->curl->setOption(CURLOPT_USERPWD, $username . ":" . $password);
        $this->curlClient->setOption(CURLOPT_HEADER, 0);
        $this->curlClient->setOption(CURLOPT_TIMEOUT, 60);
        $this->curlClient->setOption(CURLOPT_RETURNTRANSFER, true);
        //set curl header
        $this->curlClient->addHeader("Content-Type", "application/json");
        $this->curlClient->addHeader("Authorization", $token);
        //post request with url and data
        $this->curlClient->post($url, $data);
        //read response
        $response = json_decode($this->curlClient->getBody(), true);
        return $response;

    }


    /**
     * Function for Submit Order
     * @return string
     */
    public function inetWebsiteSubmitOrder($data)
    {
        $url = $this->getAPIUrl() . self::SUBMIT_ORDER;

        $token = "Bearer ".$this->getAPIkey();

        /*$data = array(
            "user_name"=> "910-865-5165",
            "token"=> "C45240B06D504EC78D38BF0CD531D9C1",//"17C68107A9D645E68A1C944A95EAEA43",//$this->getCheckoutAPIToken(),
            "trantype"=>"sale",
            "cart"=> '[{"skunumber":"TEM-ADJ-ERG2-SB-3XB-20","qty":"1"}]',
            "delcharge"=>50.00,
            "tax"=>10.00,
            "amount"=>100.00,
            "cardtype"=>"VISA",
            "cardno"=>2222,
            "expdate"=>"0122"


        );*/


     //set curl options
        //  $this->curl->setOption(CURLOPT_USERPWD, $username . ":" . $password);
        $this->curlClient->setOption(CURLOPT_HEADER, 0);
        $this->curlClient->setOption(CURLOPT_TIMEOUT, 60);
        $this->curlClient->setOption(CURLOPT_RETURNTRANSFER, true);
        //set curl header
        $this->curlClient->addHeader("Content-Type", "application/json");
        $this->curlClient->addHeader("Authorization", $token);
        //post request with url and data
        $this->curlClient->post($url, $data);
        //read response
        $response = json_decode($this->curlClient->getBody(), true);
        return $response;
    }

    public function getCheckoutAPIToken(){
        return '17C68107A9D645E68A1C944A95EAEA43';
    }


    /**
     * Function for RESET password api
     * @return string
     */
    public function inetWebsiteResetPassword($method, $params)
    {
        $url = $this->getAPIUrl() . self::CUSTOMER_RESET_PASSWORD;
        $response   =   $this->callFrogAPI($url, $method, $params);

        return $response;
    }

    /**
     * Function for customer orders
     * @return string
     */
    public function inetWebsiteCustomerOrders($method, $params)
    {
        $url        =   $this->getAPIUrl() . self::CUSTOMER_INVOICES;
        $response   =   $this->callFrogAPI($url, $method, $params);

        return $response;
    }

    /**
     * Function for order details
     * @return string
     */
    public function inetWebsiteOrderDetails($method, $params)
    {
        $url        =   $this->getAPIUrl() . self::CUSTOMER_INVOICE_DETAIL;
        $response   =   $this->callFrogAPI($url, $method, $params);

        return $response;
    }

    /**
     * Function to update customer billing address
     * @return string
     */
    public function inetWebsiteUpdateBilling($method, $billing_params)
    {
        $url        =   $this->getAPIUrl() . self::CUSTOMER_ADDRESS_UPDATE;
        $response   =   $this->callFrogAPI($url, $method, $billing_params);

        return $response;
    }

    /**
     * Function to update customer shipping address
     * @return string
     */
    public function inetWebsiteUpdateShipping($method, $shipping_params)
    {
        $url        =   $this->getAPIUrl() . self::CUSTOMER_ADDRESS_UPDATE;
        $response   =   $this->callFrogAPI($url, $method, $shipping_params);

        return $response;
    }

}
