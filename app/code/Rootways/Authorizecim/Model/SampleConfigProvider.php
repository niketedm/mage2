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

namespace Rootways\Authorizecim\Model;

use Magento\Payment\Model\CcGenericConfigProvider;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Payment\Model\CcConfig;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\App\RequestInterface;

/**
 * Class SampleConfigProvider
*/
class SampleConfigProvider extends CcGenericConfigProvider
{
    const CODE = 'rootways_authorizecim_option';
    const CC_VAULT_CODE = 'rootways_authorizecim_option_cc_vault';
    
    /** @var int */
    const DUPLICATE_WINDOW = 5;
    
    protected $methodCodes = [
        self::CODE
    ];
     
    protected $cards;

    /**
     * @var \Magento\Framework\Encryption\Encryptor
     */
    protected $encryptor;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Rootways\Authorizecim\Helper\Data
     */
    protected $customHelper;

    /**
     * @var \Magento\Framework\Url
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $sessionquote;

    /**
     * @var \Magento\Payment\Model\Config
     */
    protected $_paymentConfig;
    
    /**
     * @var Repository
     */
    protected $assetRepo;

    /**
     * @var RequestInterface
     */
    protected $request;
    
    /**
     * @param CcConfig $ccConfig
     * @param PaymentHelper $paymentHelper
     * @param Magento\Checkout\Model\Session $checkoutSession
     * @param Magento\Customer\Model\Session $customerSession
     * @param Magento\Framework\Url $urlBuilder
     * @param Magento\Payment\Model\Config $paymentConfig
     * @param Magento\Backend\Model\Session\Quote $sessionquote
     * @param Rootways\Authorizecim\Helper\Data $customHelper
     * @param Magento\Framework\Encryption\Encryptor $encryptor
     * @param Repository $assetRepo
     * @param RequestInterface $request
     */
    public function __construct(
        CcConfig $ccConfig,
        PaymentHelper $paymentHelper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Url $urlBuilder,
        \Magento\Payment\Model\Config $paymentConfig,
        \Magento\Backend\Model\Session\Quote $sessionquote,
        \Rootways\Authorizecim\Helper\Data $customHelper,
        \Magento\Framework\Encryption\Encryptor $encryptor,
        Repository $assetRepo,
        RequestInterface $request,
        array $methodCodes = []
    ) {
        parent::__construct($ccConfig, $paymentHelper, $methodCodes);
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->urlBuilder = $urlBuilder;
        $this->_paymentConfig = $paymentConfig;
        $this->dataHelper = $customHelper;
        $this->encryptor = $encryptor;
        $this->sessionquote = $sessionquote;
        $this->assetRepo = $assetRepo;
        $this->request = $request;
    }
    
    /**
     * @return string
     */
    public function getCcMonths()
    {
        return $this->_paymentConfig->getMonths();
    }
    
    /**
     * @return string
     */
    public function getCcYears()
    {
        $years = [];
        $years = $this->_paymentConfig->getYears();
        return $years;
    }
    
    /**
     * @return array
     */
    protected function getCcAvailableCcTypes()
    {
        return $this->dataHelper->getCcAvailableCardTypes();
    }
    
    /**
     * @return string
     */
    protected function getEnvironment()
    {
        return $this->dataHelper->getEnvironment();
    }
    
    /**
     * @return boolean
     */
    protected function enableAcceptjs()
    {
        return (bool)$this->dataHelper->enableAcceptjs();
    }
    
    /**
     * @return string
     */
    protected function getApiLoginId()
    {
        $storeId = $this->dataHelper->getStoreId();
        return $this->dataHelper->getApiLoginId($storeId);
    }
    
    /**
     * @return string
     */
    protected function getApiClientKey()
    {
        $storeId = $this->dataHelper->getStoreId();
        return $this->dataHelper->getApiClientKey($storeId);
    }
    
    /**
     * @return boolean
     */
    protected function getEnableCaptcha()
    {
        return $this->dataHelper->getEnableCaptcha();
    }
    
    /**
     * @return string
     */
    protected function getCaptchaSiteKey()
    {
        return $this->dataHelper->getCaptchaSiteKey();
    }
    
    /**
     * Retrieve config object
     */
    public function getConfig()
    {
        $config = parent::getConfig();
        $config = array_merge_recursive($config, [
            'payment' => [
                'rootways_authorizecim_option' => [
                    'storedCards' => '',
                    'isCustLoggedIn' => $this->customerSession->isLoggedIn(),
                    'availableCardTypes' => $this->getCcAvailableCcTypes(),
                    'ccMonths' => $this->getCcMonths(),
                    'ccYears' => $this->getCcYears(),
                    'ccVaultCode' => self::CC_VAULT_CODE,
                    'environment' => $this->getEnvironment(),
                    'enableAcceptjs' => $this->enableAcceptjs(),
                    'apiLoginId' => $this->getApiLoginId(),
                    'apiClientKey' => $this->getApiClientKey(),
                    'iscaptchaenable' => $this->getEnableCaptcha(),
                    'captchasitekey' => $this->getCaptchaSiteKey(),
                    'topNote' => $this->dataHelper->getCCMethodTopNote(),
                    'cclogolocation' => (bool)$this->dataHelper->getConfig('payment/rootways_authorizecim_option/cclogo_location')
                ],
            ],
        ]);
        return $config;
     }
}
