<?php

namespace Mancini\Productdetail\Block;

use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Session\SessionManagerInterface as CoreSession;
use Magento\Store\Model\ScopeInterface;

class Deliverto extends \Magento\Framework\View\Element\Template
{
    /**
     * @var helper
     */
    protected $helper;

    /**
     * @var CustomerSession
     */
    private $_customerSession;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     *  @var ShippingZone
     */
    protected $_shippingModel;

    /**
     * @var \Mancini\Productdetail\Helper\Data 
     */
    protected $prdHelper;

    /** 
     * @var  \Amasty\Storelocator\Block\Location $locationBlock
     */
    protected $_locationBlock;

    /**
     * @var \Mancini\Frog\Helper\ApiCall 
     */
    protected $_apiCallHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    public $_coreSession;

    /**
     * @var \Mancini\Storelocator\Helper\Data
     */
    public $_storeHelper;

    /**
     *  @param \Magento\Framework\App\Helper\Context $context
     *  @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
     *  @param \Magento\Customer\Model\CustomerFactory $customerFactory
     *  @param \Mancini\ShippingZone\Helper\Data $helperData
     *  @param \Magento\Customer\Model\Session $customerSession
     *  @param \Mancini\ShippingZone\Model\ShippingZone $shippingModel
     *  @param \Mancini\Productdetail\Helper\Data $prdHelper
     *  @param \Mancini\Storelocator\Helper\Data $_storeHelper
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Mancini\Shippingzone\Helper\Data $helperData,
        \Mancini\Productdetail\Helper\Data $prdHelper,
        \Mancini\ShippingZone\Model\ShippingZone $shippingModel,
        \Magento\Customer\Model\Session $customerSession,
        \Amasty\Storelocator\Block\Location $locationBlock,
        StoreManagerInterface $storeManager,
        CustomerFactory $customerFactory,
        \Mancini\Storelocator\Helper\Data $_storeHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        CoreSession $coreSession,\Mancini\Frog\Helper\ApiCall $apiCallHelper,
        \Magento\Framework\Registry $registry
    ) {
        $this->storeManager     = $storeManager;
        $this->customerFactory  = $customerFactory;
        $this->helperData       = $helperData;
        $this->prdHelper        = $prdHelper;
        $this->_customerSession = $customerSession;
        $this->_shippingModel   = $shippingModel;
        $this->_locationBlock   = $locationBlock;
        $this->scopeConfig      = $scopeConfig;
        $this->_storeHelper     = $_storeHelper;
        $this->_coreSession     = $coreSession;
        $this->_registry        = $registry;
        $this->_apiCallHelper   = $apiCallHelper;
        parent::__construct($context);
    }

    /**
     * Function to get user's data from customer session
     * @return int
     */
    public function getCustomer()
    {
        return $this->_customerSession->getCustomer()->getId();
    }

    /**
     * Function to get user's address using customer id
     * @return string
     */
    public function getCustomerAddress()
    {
        $deliverTo =  [];
        if ($this->_customerSession->isLoggedIn()) {
            $customerId     = $this->getcustomer();
            $customer       = $this->customerFactory->create();
            $websiteId      = $this->storeManager->getStore()->getWebsiteId();
            $customer->setWebsiteId($websiteId);
            $customerModel  = $customer->load($customerId);
            $customerAddress = [];

            if ($customerModel->getAddresses() != null) {
                foreach ($customerModel->getAddresses() as $address) {
                    $customerAddress[] = $address->toArray();
                }
                foreach ($customerAddress as $customerAddres) {
                    $city       = $customerAddres['city'];
                    $region     = $customerAddres['region'];
                    $postcode   = $customerAddres['postcode'];
                }
                $deliverTo['zipcode']   = $city . " , " . $region . " - " . $postcode;
                if(($this->getAvailabilty($postcode)) == 1){
                    $deliverTo['error'] = 1;
                }else{
                    $deliverTo['error']         = "Please call";
                    $deliverTo['contact']       = "800-647-5337";
                    $deliverTo['next_error']    = "for delivery options to this Zipcode.";
                }
            }
            $deliverTo['zipcode']       = "NA";
            $deliverTo['error']         = "Please call";
            $deliverTo['contact']       = "800-647-5337";
            $deliverTo['next_error']    = "for delivery options to this Zipcode.";
            return $deliverTo;
            //Check if the zipcode is deliverable       
        } else {
            if ($this->_storeHelper->getCookie("zipcodedeliverto") || $this->_storeHelper->getCookie("zipcodedeliverto")) {
                $nearestStore   =   $this->_storeHelper->getCookie("zipcodedeliverto");
                $nearestCity    =   $this->_storeHelper->getCookie("custlocdeliverto");
            } else {
                $nearestStore   =   $this->_storeHelper->getCookie("zipcode");
                $nearestCity    =   $this->_storeHelper->getCookie("custloc");
            }

            if ($nearestStore || $nearestCity) {
                $storeData      =   explode(" ", $nearestStore);
                $storecity      =   explode(",", $nearestCity);

                $region         =    $storeData[0];
                $zipcode        =    $storeData[1];
                $city           =    $storecity[0];

                if($city  != "NA"){
                    $deliverTo['zipcode']     = $city . " , " . $region . " - " . $zipcode;
                }else{
                    $deliverTo['zipcode']     = "NA";
                }
                if(($this->getAvailabilty($zipcode )) == 1){
                    $deliverTo['error'] = 1;
                }else{
                    $deliverTo['error']         = "Please call";
                    $deliverTo['contact']       = "800-647-5337";
                    $deliverTo['next_error']    = "for delivery options to this Zipcode.";
                }
            } else {
                $details    =   $this->_coreSession->getCustomerLocation();
                if (!empty($details)) {
                    $zipcode    =    $details['zip'];
                    $city       =    $details['locality'];
                    $region     =    $details['state'];

                    $deliverTo['zipcode']   = $city . " , " . $region . " - " . $zipcode;
                    if(($this->getAvailabilty($zipcode)) == 1){
                        $deliverTo['error'] = 1;
                    }else{
                        $deliverTo['error']         = "Please call";
                        $deliverTo['contact']       = "800-647-5337";
                        $deliverTo['next_error']    = "for delivery options to this Zipcode.";
                    }
                } else {
                    $deliverTo['zipcode'] = "NA";
                    $deliverTo['error']         = "Please call";
                    $deliverTo['contact']       = "800-647-5337";
                    $deliverTo['next_error']    ="for delivery options to this Zipcode.";
                   
                }
            }


            return $deliverTo;
        }
    }

    /**
     * Function to get user's zipcode
     * @return string
     */
    public function getAddress()
    {
        $addressData = $this->getCustomerAddress();
        foreach ($addressData as $customerAddres) {
            $postcode = $customerAddres['postcode'];
        }
        return $postcode;
    }

    /**
     * Function to check whether the zipcode is in Zone 1 or Zone 2
     * @return string
     */
    public function getAvailabilty($zipcode)
    {
        $zone_id = $this->helperData->getShippingZoneByZipcode($zipcode);
        if ($zone_id == null) {
            return "0";
        }
        return '1';
    }

    /**
     * Function to check whether customer logged in
     * @return string
     */
    public function isCustomerLoggedIn()
    {
        return $this->_customerSession->isLoggedIn();
    }


         /**
     * Function to get the current product
     * @return string
     */

    public function getCurrentProduct(){        
        return $this->_registry->registry('current_product');
    } 

    /**
     * Function to retrieve the Item Details from FROG
     * @return string
     */

    public function getDeliveryDates(){
        $skuSerial       = $this->getCurrentProduct()->getSkuSerial();
        $params ['item'] = '4155976';
        $result          =   $this->_apiCallHelper->inetWebsiteItemDetail (\Zend_Http_Client::POST, $params);
        $details         =   json_decode($result->getBody(),true);
        $itemStatus      =   $details['inventoryrec'];
   	    $status          =   $itemStatus['status'];

           $nearestCity1 =   $this->_storeHelper->getCookie("custlocdeliverto");
           $nearestCity  =   $this->_storeHelper->getCookie("custloc");

        if($status  == 'CR') {
            $DeliveryDate   =   date('M d', strtotime(' +1 day'));
          return $DeliveryDate;
        }
        else if ($status == 'CO' || $status == 'SPO') {
            $availableDate  =   $itemDetails['availability'];
            $DeliveryDate   =   date_format($availableDate,"M d");
          return $DeliveryDate;
        }
        else {
          return null;
        }
 
    }
}
