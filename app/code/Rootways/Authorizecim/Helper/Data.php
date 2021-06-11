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

namespace Rootways\Authorizecim\Helper;

use Magento\Payment\Model\Config as PaymentConfig;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\UrlInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\State as appState;
use Magento\Backend\Helper\Data as BackendHelper;
use Magento\Framework\App\ResourceConnection;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     *
     * @var \Magento\Customer\Model\SessionFactory
     */
    public $customerSession;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
    */
    protected $storeManager;
    
    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
    */
    protected $_encryptor;
    
    /**
     * @var \Magento\Config\Model\ResourceModel\Config
    */
    protected $resourceConfig;
    
    /**
     * @var \Magento\Directory\Model\RegionFactory
    */
    protected $regionFactory;
    
    /**
     * @var \Magento\Directory\Model\CountryFactory
    */
    protected $countryFactory;
    
    /**
     *
     * @var Magento\Vault\Model\ResourceModel\PaymentToken\CollectionFactory
     */
    public $vaultCollectionFactory;
    
    /**
     * @var PaymentConfig
     */
    protected $paymentConfig;
    
     /**
     * @var RemoteAddress
     */
    protected $remoteAddress;
    
    /**
     * @var UrlInterface
     */
    protected $urlInterface;
    
    /**
     * @var Json
     */
    protected $json;
    
    /**
     * @var AppState
     */
    protected $appState;
    
    /**
     * @var BackendHelper
     */
    protected $backendHelper;
    
    /**
     * @var \Magento\Framework\Module\Dir\Reader
    */
    protected $moduleReader;
    
    /**
     * @param Magento\Framework\App\Helper\Context $context
     * @param Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param Magento\Config\Model\ResourceModel\Config $resourceConfig
     * @param Magento\Directory\Model\RegionFactory $regionFactory
     * @param Magento\Directory\Model\CountryFactory $countryFactory
     * @param Magento\Vault\Model\ResourceModel\PaymentToken\CollectionFactory $vaultCollectionFactory
     * @param \Magento\Framework\Module\Dir\Reader $moduleReader
     * @param BackendHelper $backendHelper
     * @param PaymentConfig $paymentConfig
     * @param RemoteAddress $remoteAddress
     * @param UrlInterface $urlInterface
     * @param Json $json
     * @param AppState $appState
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Vault\Model\ResourceModel\PaymentToken\CollectionFactory $vaultCollectionFactory,
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        BackendHelper $backendHelper,
        PaymentConfig $paymentConfig,
        RemoteAddress $remoteAddress,
        UrlInterface $urlInterface,
        Json $json,
        AppState $appState,
        ResourceConnection $resourceConnection
    ) {
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession->create();
        $this->_encryptor = $encryptor;
        $this->_customresourceConfig = $resourceConfig;
        $this->_regionFactory = $regionFactory;
        $this->_countryFactory = $countryFactory;
        $this->vaultCollectionFactory = $vaultCollectionFactory;
        $this->moduleReader = $moduleReader;
        $this->backendHelper = $backendHelper;
        $this->paymentConfig = $paymentConfig;
        $this->remoteAddress = $remoteAddress;
        $this->_urlInterface = $urlInterface;
        $this->_json = $json;
        $this->appState = $appState;
        $this->resourceConnection = $resourceConnection;
        parent::__construct($context);
    }
    
    public function getConfig($config_path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId
        );
    }
    
    /**
     * Authorize.net payment gateway environment.
    */ 
    public function getEnvironment($storeId = null)
    {
        return $this->getConfig('payment/rootways_authorizecim_basic/environment', $storeId);
    }
    
    /**
     * Authorize.net payment gateway URL.
     *
     * @return string
    */ 
    public function getGatewayUrl()
    {
        if ($this->getEnvironment() == "production") {
            if ($this->getConfig('payment/rootways_authorizecim_basic/gateway_url') != '') {
                $url = $this->getConfig('payment/rootways_authorizecim_basic/gateway_url');
            } else {
                $url = "https://api.authorize.net/xml/v1/request.api";
            }
        } else {
            $url = 'https://apitest.authorize.net/xml/v1/request.api';
        }
        return $url;
    }
    
    /**
     * Authorize.net Hosted payment gateway URL.
     *
     * @return string
    */ 
    public function getHostedGatewayUrl()
    {
        if ($this->getEnvironment() == "production") {
            $url = "https://accept.authorize.net/payment/payment";
        } else {
            $url = "https://test.authorize.net/payment/payment";
        }
        return $url;
    }
    
    /**
     * Get API Login ID
    */
    public function getApiLoginId($storeId = null)
    {
        return $this->getConfig('payment/rootways_authorizecim_basic/api_login_id', $storeId);
    }
    
    /**
     * Get API Transaction Key
    */
    public function getTransactionKey($storeId = null)
    {
        $transaction_key = $this->getConfig('payment/rootways_authorizecim_basic/api_trans_key', $storeId);
        return $this->_encryptor->decrypt($transaction_key);
    }
    
    /**
     * Get API Client Key
    */
    public function getApiClientKey($storeId = null)
    {
        return $this->getConfig('payment/rootways_authorizecim_basic/api_client_key', $storeId);
    }
    
    /**
     * Authorize.net payment action type.
    */ 
    public function getPaymentAction()
    {
        $action = 'authCaptureTransaction';
        if ($this->getConfig('payment/rootways_authorizecim_option/payment_action') == "authorize") {
            $action = 'authOnlyTransaction';
        }
        return $action;
    }
    
    /**
     * Authorize.net Hosted Method Payment Action Type.
    */ 
    public function getHostedPaymentAction()
    {
        $action = 'authCaptureTransaction';
        if ($this->getConfig('payment/rootways_authorizecim_option_hosted/payment_action') == "authorize") {
            $action = 'authOnlyTransaction';
        }
        return $action;
    }
    
    /**
     * Check deos Accept.js is enabled or not
    */
    public function enableAcceptjs()
    {
        $val = 0;
        if ($this->getConfig('payment/rootways_authorizecim_option/acceptjs') && $this->checkAdmin() != 'adminhtml') {
            $val = 1;
        }
        return $val;
    }
    
    /**
     * Check deos hosted form enabled or not
    */
    public function enableHostedForm()
    {
        $val = 0;
        if ($this->getConfig('payment/rootways_authorizecim_option/enabled_hostedform') && $this->checkAdmin() != 'adminhtml') {
            $val = 1;
        }
        return $val;
    }
    
    /**
     * Get text of hosted form submit button
    */
    public function hostedFormOrderBtnTxt()
    {
        $payBtnTxt = 'Pay';
        if ($this->getConfig('payment/rootways_authorizecim_option_hosted/hosted_form_order_btn_txt') == '') {
            $payBtnTxt = $this->getConfig('payment/rootways_authorizecim_option_hosted/hosted_form_order_btn_txt');
        }
        return $payBtnTxt;
    }
    
    /**
     * Get text of hosted form header.
    */
    public function hostedFormHeader()
    {
        return $this->getConfig('payment/rootways_authorizecim_option_hosted/hosted_form_header');
    }
    
    /**
     * Get VISA CHECKOUT API Key
     *
     * @return string
    */
    public function getVisaCheckoutApiKey()
    {
        $val = $this->getConfig('payment/rootways_authorizecim_option_hosted/visacheckout_api_key');
        return $this->_encryptor->decrypt($val);
    }
    
    /**
     * Get VISA CHECKOUT API Key
     *
     * @return string
    */
    public function getVisaCheckoutMethodApiKey()
    {
         $this->getStoreId();
        $val = $this->getConfig('payment/rootways_authorizecim_option_visa/visacheckout_api_key', $this->getStoreId());
        return $this->_encryptor->decrypt($val);
    }
    
    /**
     * Get VISA Accepted Currency
     *
     * @return string
    */
    public function getVisaCheckoutCurrency()
    {
        return $this->getConfig('payment/rootways_authorizecim_option_visa/currency', $this->getStoreId());
    }
    
    /**
     * VISA Checkout payment action type.
    */ 
    public function getVisaCheckoutPaymentAction()
    {
        $action = 'authCaptureTransaction';
        if ($this->getConfig('payment/rootways_authorizecim_option_visa/payment_action') == "authorize") {
            $action = 'authOnlyTransaction';
        }
        return $action;
    }
    
    /**
     * VISA Checkout Popup Language.
    */ 
    public function getVisaCheckoutLocale()
    {
        return $this->getConfig('payment/rootways_authorizecim_option_visa/locale', $this->getStoreId());
    }
    
    /**
     * VISA Checkout Top Message.
    */ 
    public function getVisaTopNote()
    {
        return $this->getConfig('payment/rootways_authorizecim_option_visa/msg');
    }
    
    /**
     * Credit Card Payment Method Top Message.
    */ 
    public function getCCMethodTopNote()
    {
        return $this->getConfig('payment/rootways_authorizecim_option/msg');
    }
    
    /**
     * Hosted Form Top Message.
    */ 
    public function getHostedMethodTopNote()
    {
        return $this->getConfig('payment/rootways_authorizecim_option_hosted/msg');
    }
    
    /**
     * Get does vault enabled or not
    */
    public function vaultEnable()
    {
        return $this->getConfig('payment/rootways_authorizecim_option_cc_vault/active');
    }
    
    /**
     * Get does billing data is in request or not
    */
    public function sendBilling()
    {
        return $this->getConfig('payment/rootways_authorizecim_option/send_billing_address');
    }
    
    /**
     * Get does shipping data is in request or not
    */
    public function sendShipping()
    {
        return $this->getConfig('payment/rootways_authorizecim_option/send_shipping_address');
    }
    
    /**
     * Get does cart item data is in request or not
    */
    public function sendCartItem()
    {
        return $this->getConfig('payment/rootways_authorizecim_option/send_cart_items');
    }
    
    /**
     * Get does cart item data is in hosted form request or not
    */
    public function sendCartItemHosted()
    {
        return $this->getConfig('payment/rootways_authorizecim_option_hosted/send_cart_items');
    }
        
    /**
     * Get value of licence key from admin
    */
    public function licencekey()
    {
        return $this->getConfig('rootways_authorizecim/general/licencekey');
    }
    
    /**
     * Get store base URL.
    */
    public function getStoreBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }
    
    public function getStoreCode()
    {
        return $this->_storeManager->getStore()->getCode();
    }
    
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }
    
    public function getDefaultFormat()
    {
        return $this->getConfigData('customer/address_templates/html', $this->_storeId);
    }
    
    /**
     * Get value of licence key from admin
    */
    public function act()
    {
        $today = date("Y-m-d");
        $dt_db_blank = $this->getConfig('rootways_authorizecim/general/lcstatus');
        if ($dt_db_blank == '') {
            $isMultiStore =  $this->getConfig('rootways_authorizecim/general/ismultistore');
            $u = $this->_storeManager->getStore()->getBaseUrl();
            if ($isMultiStore == 1)  {
                $u = $this->backendHelper->getHomePageUrl();
            }
            $l = $this->getConfig('rootways_authorizecim/general/licencekey');
            $surl = base64_decode($this->surl());
            $url= $surl."?u=".$u."&l=".$l."&extname=m2_authorizecim";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            $result=curl_exec($ch);
            curl_close($ch);
            $act_data = json_decode($result, true);
            if (isset($act_data['status']) && $act_data['status'] == '0') {
                return "SXNzdWUgd2l0aCB5b3VyIFJvb3R3YXlzIGV4dGVuc2lvbiBsaWNlbnNlIGtleSwgcGxlYXNlIGNvbnRhY3QgPGEgaHJlZj0ibWFpbHRvOmhlbHBAcm9vdHdheXMuY29tIj5oZWxwQHJvb3R3YXlzLmNvbTwvYT4=";
            } else {
                $this->_customresourceConfig->saveConfig('rootways_authorizecim/general/lcstatus', $l, 'default', 0);
            }
        }
    }
    
    /**
     * return area code.
    */
    public function checkAdmin()
    {
        return $this->appState->getAreaCode();
    }
    
    /**
     * Get value of secure URL from admin
    */
    public function surl()
    {
        return "aHR0cHM6Ly93d3cucm9vdHdheXMuY29tL20ydmVyaWZ5bGljLnBocA==";
    }
    
    /**
     * Get value of Region Code.
    */
    public function getRegionCode($shipperRegionId)
    {
        $shipperRegion = $this->_regionFactory->create()->load($shipperRegionId );
        return $shipperRegion->getCode();
    }
    
    /**
     * Get value of Country ID.
    */
    public function getCountryName($countryCode)
    {
        $country = $this->_countryFactory->create()->loadByCode($countryCode);
        return $country->getName();
    }
    
    /**
     * Get credit card code by name.
    */ 
    public function getCcCodeByName($name)
    {
        $types = $this->paymentConfig->getCcTypes();
        $key = array_search(strtolower($name), array_map('strtolower', $types));
        
        return $key;
    }
    
    /**
     * Retrieve credit card name by code.
    */ 
    public function getCcTypeNameByCode($code)
    {
        $ccTypes = $this->paymentConfig->getCcTypes();
        if (isset($ccTypes[$code])) {
            return $ccTypes[$code];
        } else {
            return false;
        }
    }
    
    /**
     * Retrieve credit card code by name.
    */ 
    public function getCcTypeCodeByName($name)
    {
        $ccTypes = $this->paymentConfig->getCcTypes();
        $key = array_search(strtolower($name), array_map('strtolower', $ccTypes));
        if ($key != '') {
            return $key;
        } else {
            return false;
        }
    }
    
    /**
     * Retrieve credit card types.
    */ 
    public function getCcTypes()
    {
        $ccTypes = $this->paymentConfig->getCcTypes();
        if (is_array($ccTypes)) {
            return $ccTypes;
        } else {
            return false;
        }
    }
    
    /**
     * Retrieve selected credit card from config.
    */ 
    public function getCcAvailableCardTypes($country = null)
    {
        $cType = $this->getConfig('payment/rootways_authorizecim_option/cctypes');
        $types = array_flip(explode(',', $cType));
        $mergedArray = [];

        if (is_array($types)) {
            foreach (array_keys($types) as $type) {
                $types[$type] = $this->getCcTypeNameByCode($type);
            }
        }

        //preserve the same credit card order
        $allTypes = $this->getCcTypes();
        if (is_array($allTypes)) {
            foreach ($allTypes as $ccTypeCode => $ccTypeName) {
                if (array_key_exists($ccTypeCode, $types)) {
                    $mergedArray[$ccTypeCode] = $ccTypeName;
                }
            }
        }

        return $mergedArray;
    }
    
    /**
     * Get customer saved credit card collection
    */ 
    public function getSavedCreditCard()
    {
        $customerId = '';
        if ($this->_customerSession->isLoggedIn()) {
            $customerId = $this->_customerSession->getId();
        }
        $cardcollection = $this->vaultCollectionFactory->create()
            ->addFieldToFilter('customer_id', $customerId);
            //->addFieldToFilter('is_active', 1)
            //->addFieldToFilter('is_visible', 1)
        
        return $cardcollection;
    }
    
    /**
     * Get logged in customer ID.
     *
     * @return string.
     */  
    public function getCustomerId()
    {
        $customerId = '';
        if ($this->_customerSession->isLoggedIn()) {
            $customerId = $this->_customerSession->getId();
        }
        
        return $customerId;
    }
    
    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        return (bool) $this->_customerSession->isLoggedIn();
    }
    
    /**
     *  Send customer ID in proper formate
    */ 
    public function formatedCustomerId($id)
    {
        return substr($id, 0, strpos($id, "_"));
    }
    
    /**
     *  Request field validation
    */ 
    public function subStrFun($val, $len)
    {
        if (strlen($val) >= $len) {
            $val = substr($val, 0, $len);
        }
        
        return $val;
    }
    
    /**
     * Get payment ID using payment token
    */ 
    public function getPaymentIdByToken($paymentToken)
    {
        $tokenDetails = json_decode($paymentToken->getTokenDetails(), true);
        $paymentId = '';
        if (isset($tokenDetails['paymentProfileId'])) {
            $paymentId = $tokenDetails['paymentProfileId'];
        }
        
        return $paymentId;
    }
    
    public function getQuote($quote)
    {
        // Get Currency and Amount
        $amount = $quote->getGrandTotal();
        $currency = $quote->getQuoteCurrencyCode();
        // Below code is not used anywhere in this module
        return [
            'currency' => 'usd',
            'total' => [
                'label' => 'Order by vish v',
                'amount' => $amount,
                'pending' => false
            ],
            'displayItems' => [
                'label' => __('TEST BY VISH'),
                'amount' => $amount
            ]
        ];
    }
    
    public function getCustomerIp()
    {
        return $this->subStrFun($this->remoteAddress->getRemoteAddress(), 15);
    }
    
    /**
     * @return boolean
     */
    public function getEnableCaptcha()
    {
        return $this->getConfig('payment/rootways_authorizecim_option/enable_captcha');
    }
    
    /**
     * @return string
     */
    public function getCaptchaSiteKey()
    {
        if ($this->getEnableCaptcha() == 3) {
            $val = $this->getConfig('payment/rootways_authorizecim_option/v3_site_key');
        } else {
            $val = $this->getConfig('payment/rootways_authorizecim_option/site_key');
        }
        return $this->_encryptor->decrypt($val);
    }
    
    /**
     * @return string
     */
    public function getCaptchaSecretKey()
    {
        if ($this->getEnableCaptcha() == 3) {
            $val = $this->getConfig('payment/rootways_authorizecim_option/v3_secret_key');
        } else {
            $val = $this->getConfig('payment/rootways_authorizecim_option/secret_key');
        }
        return $this->_encryptor->decrypt($val);
    }
    
    public function verifyGC($gCaptcha)
    {
        $isWrongCaptcha = 1;
        if (!empty($gCaptcha)) {
            $post_data = http_build_query(
                 array(
                    'secret' => $this->getCaptchaSecretKey(),
                    'response' => $gCaptcha,
                    'remoteip' => $this->getCustomerIp()
                )
            );

            $opts = array('http' =>
                array(
                    'method'  => 'POST',
                    'header'  => 'Content-type: application/x-www-form-urlencoded',
                    'content' => $post_data
                )
            );

            $context  = stream_context_create($opts);
            $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
            $result = json_decode($response);
            
            if (isset($result->success)) {
                if ($this->getEnableCaptcha() == 3) {
                    if (isset($result->score)) {
                        $captchaMinScore = 0.5;
                        $captchaScore = $this->getConfig('payment/rootways_authorizecim_option/c_minimum_score');
                        if ($captchaScore != '') {
                            $captchaMinScore = $captchaScore;
                        }
                        if ($result->success == 1 && (float)$result->score >= (float)$captchaMinScore) {
                            $isWrongCaptcha = 0;
                        }
                    }
                } else {
                    if ($result->success == 1) {
                        $isWrongCaptcha = 0;
                    }
                }
            }
        }
        
        $captchaErrorMsg = '';
        if ($isWrongCaptcha == 1) {
            $captchaErrorMsg = __('reCAPTCHA verification failed.');
            $v2Msg = $this->getConfig('payment/rootways_authorizecim_option/v2_validation_failure_message');
            $v3Msg = $this->getConfig('payment/rootways_authorizecim_option/v3_validation_failure_message');
            if ($this->getEnableCaptcha() == 3) {
                $captchaErrorMsg = __('You cannot proceed with such operation, your reCAPTCHA reputation is too low.');
                if ($v3Msg != '') {
                    $captchaErrorMsg = $v3Msg;
                }
            } else {
                $captchaErrorMsg = __('reCAPTCHA verification failed.');
                if ($v2Msg != '') {
                    $captchaErrorMsg = $v2Msg;
                }
            }
        }
        
        return $captchaErrorMsg;
    }
    
    /**
     * @param $data
     * @return bool|false|string
     */
    public function getJsonEncode($data)
    {
        return $this->_json->serialize($data);
    }
    
    /**
     * @param $data
     * @return array|bool|float|int|mixed|string|null
     */
    public function getJsonDecode($data)
    {
        return $this->_json->unserialize($data);
    }
    
    public function getIframeCommunicatorUrl()
    {
        return $this->_urlInterface->getUrl('rootways_authorizecim/hostedform/iframecommunicator');
    }
    
    /**
     * @return string
     */
    public function getRwModuleDir()
    {
        $etcDir = $this->moduleReader->getModuleDir(
            \Magento\Framework\Module\Dir::MODULE_ETC_DIR,
            'Rootways_Authorizecim'
        );
        
        return $etcDir;
    }

    /**
     * Select query to fetch records
     *
     * @return array
     */
    public function getAagreementCollection()
    {
        $tableName = $this->resourceConnection->getTableName('checkout_agreement');
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from(
                ['c' => $tableName],
                ['*']
            )
            ->where(
                "c.is_active = 1"
            )->where(
                "c.mode = 1"
            );
        $records = $connection->fetchAll($select);
 
        return $records;
    }
}
