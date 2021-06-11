<?php

namespace Mancini\CustomerSignIn\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Customer\Model\Customer;

class Data extends AbstractHelper
{
    /**
     * Name of Cookie that holds private content version
     */
    CONST COOKIE_NAME = 'rememberme';

    /**
     * Cookie life time
     */
    CONST COOKIE_LIFE = 604800;

    /**
     * @var CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var CookieMetadataFactory
     */
    protected $cookieMetadataFactory;

    /**
     * @var $scopeConfigInterface
     */
    private $scopeConfigInterface;

    /**
     * @var SessionManagerInterface
     */
    protected $sessionManager;

    /**
     * @var Customer
     */
    protected $customerModel;

    public function __construct(
        ScopeConfigInterface $scopeConfigInterface,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        SessionManagerInterface $sessionManager,
        Customer $customerModel
    ){
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->sessionManager = $sessionManager;
        $this->customerModel = $customerModel;
    }

    /**
     * Get data from cookie set in remote address
     *
     * @return value
     */
    public function get($name)
    {
        return $this->cookieManager->getCookie($name);
    }

    /**
     * Set data to cookie in remote address
     *
     * @param [string] $value    [value of cookie]
     * @param integer $duration [duration for cookie] 7 Days
     *
     * @return void
     */
    public function set($value, $duration = 604800)
    {
        $metadata = $this->cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setDuration($duration)
            ->setPath($this->sessionManager->getCookiePath())
            ->setDomain($this->sessionManager->getCookieDomain());

        $this->cookieManager->setPublicCookie(self::COOKIE_NAME, $value, $metadata);

    }

    /**
     * delete cookie remote address
     *
     * @return void
     */
    public function delete($name)
    {
        $this->cookieManager->deleteCookie(
            $name,
            $this->cookieMetadataFactory
                ->createCookieMetadata()
                ->setPath($this->sessionManager->getCookiePath())
                ->setDomain($this->sessionManager->getCookieDomain())
        );
    }

    /**
     * @return Data
     */
    public function getCookieloginName()
    {
        $name = json_decode($this->get(self::COOKIE_NAME));
        if($name)
            return $name->username ? $name->username : '';
    }

    /**
     * @return Data
     */
    public function getCookieloginPwd()
    {
        $pwd = json_decode($this->get(self::COOKIE_NAME));
        if($pwd)
            return $pwd->password ? $pwd->password : '';
    }

    /**
     * @return Data
     */
    public function getCookieloginChk()
    {
        $chk = json_decode($this->get(self::COOKIE_NAME));
        if($chk)
            return $chk->rememberme ? true : '';
    }

    /**
     * @return var
     */
    public function getCookielifetime()
    {
        return self::COOKIE_LIFE;
    }

    /**
     * Retrieve Customer id from email by website level
     *
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMobile($email)
    {
        try {
            $this->customerModel->setWebsiteId(1); //Here 1 means Store ID**
            $customer = $this->customerModel->loadByEmail($email);
        } catch (Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__("The customer email isn't defined."));
        }

        return $customer->getMobile();
    }
}
