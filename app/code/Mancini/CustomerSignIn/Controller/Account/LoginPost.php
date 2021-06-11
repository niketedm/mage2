<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mancini\CustomerSignIn\Controller\Account;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Controller\AbstractAccount;
use Magento\Framework\Phrase;
use Mancini\CustomerSignIn\Helper\Data as CustomCooike;

/**
 * Post login customer action.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class LoginPost extends AbstractAccount implements CsrfAwareActionInterface, HttpPostActionInterface
{
    /**
     * @var \Magento\Customer\Api\AccountManagementInterface
     */
    protected $customerAccountManagement;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $formKeyValidator;

    /**
     * @var AccountRedirect
     */
    protected $accountRedirect;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    private $cookieMetadataManager;

    /**
     * @var CustomerUrl
     */
    private $customerUrl;

    /**
     * @var \Mancini\Frog\Helper\ApiCall
     */
    protected $_apiCallHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Mancini\CustomersignIn\Helper\Data
     */
    private $getCookiedata;

    /**
     * @var \Mancini\Storelocator\Helper\Data
     */
    protected $_storeLocHelper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlModel;
    /**
     * @param Context $context
     * @param Session $customerSession
     * @param AccountManagementInterface $customerAccountManagement
     * @param CustomerUrl $customerHelperData
     * @param Validator $formKeyValidator
     * @param AccountRedirect $accountRedirect
     * @param \Mancini\Frog\Helper\ApiCall $apiCallHelper
     * @param \Magento\Framework\Registry $registry
     * @param \Mancini\CustomersignIn\Helper\Data $getCookiedata
     * @param \Mancini\Storelocator\Helper\Data $storeLocHelper
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        AccountManagementInterface $customerAccountManagement,
        CustomerUrl $customerHelperData,
        Validator $formKeyValidator,
        AccountRedirect $accountRedirect,
        \Mancini\Frog\Helper\ApiCall $apiCallHelper,
        \Magento\Framework\Registry $registry,
        \Mancini\Storelocator\Helper\Data $storeLocHelper,
        CustomCooike $getCookiedata,
        \Magento\Framework\UrlInterface $urlModel
    ) {
        $this->session                      = $customerSession;
        $this->customerAccountManagement    = $customerAccountManagement;
        $this->customerUrl                  = $customerHelperData;
        $this->formKeyValidator             = $formKeyValidator;
        $this->accountRedirect              = $accountRedirect;
        $this->_registry                    = $registry;
        $this->_apiCallHelper               = $apiCallHelper;
        $this->getCookiedata                = $getCookiedata;
        $this->_storeLocHelper              = $storeLocHelper;
        $this->urlModel                     = $urlModel;
        parent::__construct($context);
    }

    /**
     * Get scope config
     *
     * @return ScopeConfigInterface
     * @deprecated 100.0.10
     */
    private function getScopeConfig()
    {
        if (!($this->scopeConfig instanceof \Magento\Framework\App\Config\ScopeConfigInterface)) {
            return \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\App\Config\ScopeConfigInterface::class
            );
        } else {
            return $this->scopeConfig;
        }
    }

    /**
     * Retrieve cookie manager
     *
     * @deprecated 100.1.0
     * @return \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    private function getCookieManager()
    {
        if (!$this->cookieMetadataManager) {
            $this->cookieMetadataManager = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\Stdlib\Cookie\PhpCookieManager::class
            );
        }
        return $this->cookieMetadataManager;
    }

    /**
     * Retrieve cookie metadata factory
     *
     * @deprecated 100.1.0
     * @return \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    private function getCookieMetadataFactory()
    {
        if (!$this->cookieMetadataFactory) {
            $this->cookieMetadataFactory = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory::class
            );
        }
        return $this->cookieMetadataFactory;
    }

    /**
     * @inheritDoc
     */
    public function createCsrfValidationException(
        RequestInterface $request
    ): ?InvalidRequestException {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/*/');

        return new InvalidRequestException(
            $resultRedirect,
            [new Phrase('Invalid Form Key. Please refresh the page.')]
        );
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return null;
    }

    /**
     * Login post action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        if ($this->session->isLoggedIn() || !$this->formKeyValidator->validate($this->getRequest())) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }

        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');

            if (!empty($login['username']) && !empty($login['password'])) {
                try {

                    $frogResult     =   $this->getLoginDetail($login);

                    //if error parameter is not empty of json response then only will enter inside if{}
                    //condition for checking user exists in frog or not

                        // RemeberMe
                        if (array_key_exists('rememberme', $login)) {
                            $logindetails = array('username' => $login['username'], 'password' => $login['password'], 'rememberme' => true);
                            $logindetails = json_encode($logindetails);
                            $this->getCookiedata->set($logindetails, $this->getCookiedata->getCookielifetime());
                        } else {
                            $this->getCookiedata->delete('rememberme');
                        }

                        $customer = $this->customerAccountManagement->authenticate($login['username'], $login['password']);
                        $this->session->setCustomerDataAsLoggedIn($customer);
                        $this->session->setCustomerToken($frogResult['token']);

                        /*====Code for setting the nearest store in cookie===*/
                            //Check already cookie set
                            $nearestStore  = $this->_storeLocHelper->getCookie("custloc");

                            if($nearestStore == 'NA' || $nearestStore == ''){
                                //Check customer have zipcode in address
                                $zipcode = $this->_storeLocHelper->getCustomerZipcode();
                                if($zipcode != ''){
                                    $storeloc = $this->_storeLocHelper->getZipNearestStore($zipcode);
                                    if($storeloc != ''){
                                        $storeArray  =   json_decode($storeloc,true);

                                        //Set the new address location to the Cookie
                                        $this->_storeLocHelper->deleteCookie("zipcode");
                                        $this->_storeLocHelper->deleteCookie("custloc");
                                        $this->_storeLocHelper->setCookie("zipcode",$storeArray['state']." ".$storeArray['zip']);
                                        $this->_storeLocHelper->setCookie("custloc",$storeArray['address'].",".$storeArray['state']);
                                    }
                                }
                            }
                        /*==== End of code for setting nearest store in cookie ===*/

                        if ($this->getCookieManager()->getCookie('mage-cache-sessid')) {
                            $metadata = $this->getCookieMetadataFactory()->createCookieMetadata();
                            $metadata->setPath('/');
                            $this->getCookieManager()->deleteCookie('mage-cache-sessid', $metadata);
                        }
                        $redirectUrl = $this->accountRedirect->getRedirectCookie();

                        if($this->_storeLocHelper->getCookie("checkoutlogin")==1){
                           $resultRedirect = $this->resultRedirectFactory->create();
                            // URL is checked to be internal in $this->_redirect->success()
                           $url = $this->urlModel->getUrl('checkout', ['_secure' => true]);
                           $resultRedirect->setUrl($url);
                           $this->_storeLocHelper->deleteCookie("checkoutlogin");
                            return $resultRedirect;
                        }

                        if (!$this->getScopeConfig()->getValue('customer/startup/redirect_dashboard') && $redirectUrl) {
                            $this->accountRedirect->clearRedirectCookie();
                            $resultRedirect = $this->resultRedirectFactory->create();
                            // URL is checked to be internal in $this->_redirect->success()
                            $resultRedirect->setUrl($this->_redirect->success($redirectUrl));
                            return $resultRedirect;
                        }

                } catch (EmailNotConfirmedException $e) {
                    $this->messageManager->addComplexErrorMessage(
                        'confirmAccountErrorMessage',
                        ['url' => $this->customerUrl->getEmailConfirmationUrl($login['username'])]
                    );
                    $this->session->setUsername($login['username']);
                } catch (AuthenticationException $e) {
                    $message = __(
                        'Invalid username or Password.'
                    );
                } catch (LocalizedException $e) {
                    $message = $e->getMessage();
                } catch (\Exception $e) {
                    // PA DSS violation: throwing or logging an exception here can disclose customer password
                    $this->messageManager->addErrorMessage(
                        __('An unspecified error occurred. Please contact us for assistance.')
                    );
                } finally {
                    if (isset($message)) {
                        $this->messageManager->addErrorMessage($message);
                        $this->session->setUsername($login['username']);
                    }
                }
            } else {
                $this->messageManager->addErrorMessage(__('A login and a password are required.'));
            }
        }

        return $this->accountRedirect->getRedirect();
    }


    /**
     * Function to retrieve the Login Details from FROG
     * @return string
     */

    public function getLoginDetail($login)
    {
        $mobile                   =    '';
        $mobile                   =    $this->getCookiedata->getMobile($login['username']);
        $loginData                =    array();
        $params ['event']         =    "login" ;
        $params ['user_phone']    =    $mobile;
        $params ['user_pw']       =    $login['password'];
        $params ['user_email']     =    $login['username'];

        $result     =   $this->_apiCallHelper->inetWebsiteLoginDetail(\Zend_Http_Client::POST, $params);

        $response   =   $result->getBody();

        $details     =   $this->resolveQuotes ($response);

        foreach($details as $key=>$value){
            $key = str_replace('"','',$key);
            $value = str_replace('"','',$value);
            if(trim($key) == "errors"){
                $loginData['errors']     =  $value;
            }
            if(trim($key) == "token"){
                $loginData['token']     =  $value;
            }
        }

        return $loginData;
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
