<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_SpinToWin
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\SpinToWin\Helper;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * Constructor
     *
     * @param \Webkul\SpinToWin\Logger\Logger $logger
     * @param \Webkul\SpinToWin\Model\SegmentsFactory $segmentsFactory
     * @param \Webkul\SpinToWin\Model\WheelFactory $wheelFactory
     * @param \Webkul\SpinToWin\Model\InfoFactory $infoFactory
     * @param \Webkul\SpinToWin\Model\ReportsFactory $reportsFactory
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $store
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Framework\Filesystem\Io\File $io
     * @param \Magento\Framework\Filesystem\DirectoryList $directoryList
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase
     * @param \Magento\Framework\Filesystem $filesystem
     * @param CustomerSession $customerSession
     * @param CheckoutSession $checkoutSession
     * @param CookieMetadataFactory $cookieMetadata
     * @param CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Json\Helper\Data $jsonData
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\SalesRule\Model\CouponFactory $couponFactory
     * @param \Magento\SalesRule\Model\RuleFactory $saleRuleFactory
     */
    public function __construct(
        \Webkul\SpinToWin\Logger\Logger $logger,
        \Webkul\SpinToWin\Model\SegmentsFactory $segmentsFactory,
        \Webkul\SpinToWin\Model\WheelFactory $wheelFactory,
        \Webkul\SpinToWin\Model\InfoFactory $infoFactory,
        \Webkul\SpinToWin\Model\ReportsFactory $reportsFactory,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $store,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Filesystem\Io\File $io,
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase,
        \Magento\Framework\Filesystem $filesystem,
        CustomerSession $customerSession,
        CheckoutSession $checkoutSession,
        CookieMetadataFactory $cookieMetadata,
        CookieManagerInterface $cookieManager,
        \Magento\Framework\Json\Helper\Data $jsonData,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\SalesRule\Model\CouponFactory $couponFactory,
        \Magento\SalesRule\Model\RuleFactory $saleRuleFactory
    ) {
        $this->logger = $logger;
        $this->segmentsFactory = $segmentsFactory;
        $this->wheelFactory = $wheelFactory;
        $this->infoFactory = $infoFactory;
        $this->reportsFactory = $reportsFactory;
        $this->store = $store;
        $this->timezone = $timezone;
        $this->jsonHelper = $jsonHelper;
        $this->cacheTypeList = $cacheTypeList;
        $this->directoryList = $directoryList;
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->cookieMetadata = $cookieMetadata;
        $this->cookieManager = $cookieManager;
        $this->jsonData = $jsonData;
        $this->couponFactory = $couponFactory;
        $this->saleRuleFactory = $saleRuleFactory;
        $this->coreFileStorageDatabase = $coreFileStorageDatabase;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        parent::__construct($context);
    }

    /**
     * getLayoutWheelView
     *
     * @return array
     */
    public function getLayoutWheelView()
    {
        $layoutWheelView = [
            'full' => __('Full View'),
            'split' => __('Split View')
        ];
        return $layoutWheelView;
    }

    /**
     * getLayoutTriggerButtonPosition
     *
     * @return array
     */
    public function getLayoutTriggerButtonPosition()
    {
        $layoutTriggerButtonPosition = [
            'bottom-right' => __('Bottom Right'),
            'top-right' => __('Top Right'),
            'bottom-left' => __('Bottom Left'),
            'top-left' => __('Top Left'),
            'middle-left' => __('Middle Left'),
            'middle-right' => __('Middle Right')
        ];
        return $layoutTriggerButtonPosition;
    }

    /**
     * getLayoutPosition
     *
     * @return array
     */
    public function getLayoutPosition()
    {
        $layoutPosition = [
            'left' => __('Left'),
            'right' => __('Right')
        ];
        return $layoutPosition;
    }

    /**
     * getPages
     *
     * @return array
     */
    public function getPages()
    {
        $pages = [
            'cms-index-index' => __('Home Page'),
            'customer-account-login' => __('Login Page'),
            'customer-account-create' => __('Registration Page'),
            'catalog-product-view' => __('Product View Page'),
            'catalog-category-view' => __('Category Page'),
            'catalogsearch-result-index' => __('Product Search Page'),
            'checkout-cart-index' => __('Cart Page'),
            'checkout-index-index' => __('Checkout Page'),
            'other' => __('Other pages')
        ];
        return $pages;
    }

    /**
     * getEvents
     *
     * @return array
     */
    public function getEvents()
    {
        $events = [
            'after' => __('After x seconds'),
            'immediate' => __('Immediately'),
            'scroll' => __('When scroll to x %'),
            'exit' => __('When Exit')
        ];
        return $events;
    }

    /**
     * getAllow
     *
     * @return array
     */
    public function getAllow()
    {
        $allow = [
            '0' => __("All"),
            '1' => __('Select Specifics')
        ];
        return $allow;
    }

    /**
     * getSpintowinButtonImage
     *
     * @return array
     */
    public function getSpintowinButtonImage()
    {
        $spintowinButtonImage = [
            'red' => 'spintowin/image/red.png',
            'yellow' => 'spintowin/image/yellow.png',
            'green' => 'spintowin/image/green.png',
            'purple' => 'spintowin/image/purple.png',
            'blue' => 'spintowin/image/blue.png',
        ];
        return $spintowinButtonImage;
    }

    /**
     * getSpintowinPinImage
     *
     * @return array
     */
    public function getSpintowinPinImage()
    {
        $spintowinPinImage = [
            'red' => 'spintowin/image/red_pin.png',
            'green' => 'spintowin/image/green_pin.png',
            'yellow' => 'spintowin/image/yellow_pin.png',
            'purple' => 'spintowin/image/purple_pin.png',
        ];
        return $spintowinPinImage;
    }

    /**
     * getLayoutView
     *
     * @return array
     */
    public function getLayoutView()
    {
        $layoutView = [
            'popup' => __('Pop Up Dialog'),
            'slide' => __('Sidebar Slide')
        ];
        return $layoutView;
    }

    /**
     * getBackgroundRepeatProperties
     *
     * @return array
     */
    public function getBackgroundRepeatProperties()
    {
        $backgroundRepeatProperties = [
            'repeat' => __('Repeat'),
            'repeat-x' => __('Repeat Horizontal'),
            'repeat-y' => __('Repeat Vertical'),
            'no-repeat' => __('No Repeat'),
            'space' => __('Space'),
            'round' => __('Round'),
            'initial' => __('Initial'),
        ];
        return $backgroundRepeatProperties;
    }

    /**
     * Media Directory path
     *
     * @return string
     */
    public function getMediaDirectory()
    {
        return $this->store->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }


    /**
     * Current Date Time
     *
     * @return datetime
     */
    public function getCurrentDateTime()
    {
        return $this->timezone->date()->format('Y-m-d H:i:s');
    }

    /**
     * Flush Cache
     */
    public function cacheFlush()
    {
        $types = ['full_page'];
        foreach ($types as $type) {
            $this->cacheTypeList->cleanType($type);
        }
    }

    /**
     * Check coupon validity
     *
     * @param string $couponCode
     * @return array
     */
    public function checkValidCoupon($couponCode)
    {
        $msg = '';
        $success = true;
        $couponCode = trim($couponCode);
        if ($couponCode) {
            $ruleId =  $this->couponFactory->create()->loadByCode($couponCode)->getRuleId();
            $rule = $this->saleRuleFactory->create()->load($ruleId);
            if ($rule->getSegmentId()) {
                $this->checkoutSession->setWkSpinRule($ruleId);
                if ($this->scopeConfig->getValue('spintowin/general/email_validation')) {
                    if ($this->customerSession->isLoggedIn()) {
                        $email = $this->customerSession->getCustomer()->getEmail();
                        $reports = $this->reportsFactory->create()
                                            ->getCollection()
                                            ->addFieldToFilter('coupon', $couponCode);
                        if ($reports->getSize()) {
                            $report = $reports->getLastItem();
                            if ($report->getEmail() != $email) {
                                $success = false;
                                $msg = __('You are not authorised to use this Coupon.');
                            }
                        }
                    } else {
                        $success = false;
                        $msg = __('You need to log in to use this Coupon.');
                    }
                }
            }
        }
        return ['success'=>$success, 'msg'=>$msg];
    }

    /**
     * Save File
     *
     * @param string $fileName
     * @return string
     */
    public function saveFile($fileName)
    {
        $newFileName = substr_replace($fileName, time(), strrpos($fileName, '.'), 0);
        $baseTmpImagePath = $this->getFilePath($this->directoryList->getPath('media')
                    .'/tmp/catalog/product', $fileName);
        $baseImagePath = $this->getFilePath($this->directoryList->getPath('media')
                    .'/spintowin', $newFileName);
        $this->coreFileStorageDatabase->copyFile(
            $baseTmpImagePath,
            $baseImagePath
        );
        $this->mediaDirectory->renameFile(
            $baseTmpImagePath,
            $baseImagePath
        );
        return $newFileName;
    }

    /**
     * Get Path
     *
     * @param string $path
     * @param string $imageName
     * @return void
     */
    public function getFilePath($path, $imageName)
    {
        return rtrim($path, '/') . '/' . ltrim($imageName, '/');
    }

    /**
     * Get Wheel Data
     *
     * @param int $spinId
     * @return \Webkul\SpinToWin\Model\WheelFactory
     */
    public function getWheelData($spinId)
    {
        $wheel = $this->wheelFactory->create()->load($spinId, 'spin_id');
        $segmentsLabel = $this->segmentsFactory->create()
                                        ->getCollection()
                                        ->addFieldToFilter('spin_id', $spinId)
                                        ->setOrder('position', 'ASC')
                                        ->getColumnValues('label');
        $wheel->setSegmentsLabel($segmentsLabel);
        $wheel->setMediaUrl($this->getMediaDirectory());
        return $wheel;
    }

    /**
     * Get Spin To Show
     *
     * @return \Webkul\SpinToWin\Model\InfoFactory
     */
    public function getSpin()
    {
        $notInclude = $this->jsonData->jsonDecode(
            $this->cookieManager->getCookie('spintowin_spins') ?
                    $this->cookieManager->getCookie('spintowin_spins'): "[0]"
        );
        
        if ($this->customerSession->isLoggedIn()) {
            $email = $this->customerSession->getCustomer()->getEmail();
            $tempnotInclude = $this->reportsFactory->create()
                                ->getCollection()
                                ->addFieldToFilter('email', $email)
                                ->getColumnValues('spin_id');
            $notInclude = array_merge($notInclude, $tempnotInclude);
        }
        if (empty($notInclude)) {
            $notInclude = [0];
        }
        $currentDateTime = $this->getCurrentDateTime();
        $spin = $this->infoFactory->create();
        $collection = $this->infoFactory->create()->getCollection()
                                    ->addFieldToFilter(
                                        ['start_date', 'start_date'],
                                        [
                                            ['null' => true],
                                            ['lteq' => $currentDateTime]
                                        ]
                                    )
                                    ->addFieldToFilter(
                                        ['end_date', 'end_date'],
                                        [
                                            ['null' => true],
                                            ['gt' => $currentDateTime]
                                        ]
                                    )
                                    ->addFieldToFilter('status', 1)
                                    ->addFieldToFilter('entity_id', ['nin'=>$notInclude])
                                    ->setOrder('priority', 'DESC');
        foreach ($collection as $spinModel) {
            if (in_array($this->store->getStore()->getWebsiteId(), explode(',', $spinModel->getWebsiteIds()))) {
                if ($spinModel->getSegments()->getSize()) {
                    $spin = $spinModel;
                    break;
                }
            }
        }
        return $spin;
    }

    /**
     * Get Spin Ids
     *
     * @return array
     */
    public function getSpinIds()
    {
        $spinIds = [];
        $notInclude = [];
        if ($this->customerSession->isLoggedIn()) {
            $email = $this->customerSession->getCustomer()->getEmail();
            $notInclude = $this->reportsFactory->create()
                                ->getCollection()
                                ->addFieldToFilter('email', $email)
                                ->getColumnValues('spin_id');
        }
        if (empty($notInclude)) {
            $notInclude = [0];
        }
        $currentDateTime = $this->getCurrentDateTime();
        $collection = $this->infoFactory->create()->getCollection()
                                    ->addFieldToFilter(
                                        ['start_date', 'start_date'],
                                        [
                                            ['null' => true],
                                            ['lteq' => $currentDateTime]
                                        ]
                                    )
                                    ->addFieldToFilter(
                                        ['end_date', 'end_date'],
                                        [
                                            ['null' => true],
                                            ['gt' => $currentDateTime]
                                        ]
                                    )
                                    ->addFieldToFilter('status', 1)
                                    ->addFieldToFilter('entity_id', ['nin'=>$notInclude])
                                    ->setOrder('priority', 'DESC');
        foreach ($collection as $spinModel) {
            if (in_array($this->store->getStore()->getWebsiteId(), explode(',', $spinModel->getWebsiteIds()))) {
                if ($spinModel->getSegments()->getSize()) {
                    $spinIds[] = $spinModel->getId();
                }
            }
        }
        return $spinIds;
    }
}
