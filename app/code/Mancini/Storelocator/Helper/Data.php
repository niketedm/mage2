<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mancini\Storelocator\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use \Magento\Directory\Model\RegionFactory;
use \Magento\Framework\Session\SessionManagerInterface as CoreSession;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\CustomerFactory;
use Amasty\Storelocator\Model\ConfigProvider;
use Amasty\Storelocator\Model\ResourceModel\Location\CollectionFactory;
use Amasty\Storelocator\Model\ResourceModel\Attribute\Collection as AttributeCollection;
use Magento\Framework\App\PageCache\Version;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;

class Data extends AbstractHelper
{
    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $_regionFactory;

    /**
     * @var Magento\Framework\Session\SessionManagerInterface
     */
    protected $_coreSession;
     /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var CustomerSession
     */
    private $_customerSession;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Amasty\Storelocator\Model\ConfigProvider
     */
    protected $_amastyconfigProvider;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $cookieMetadataFactory;

    /**
     * @var \Amasty\Storelocator\Model\ResourceModel\Location\CollectionFactory
     */
    protected $_amastyCollectionFactory;
    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected $_curl;

    /**
     * @var AttributeCollection
     */
    protected $attributeCollection;

    protected $cacheTypeList;
    protected $cacheFrontendPool;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Amasty\Storelocator\Model\ResourceModel\Location\CollectionFactory $amastyCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Framework\Session\SessionManagerInterface $coreSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Amasty\Storelocator\Model\ConfigProvider $amastyConfigProvider
     * @param CookieManagerInterface $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Customer\Model\Session $customerSession,
        CoreSession $coreSession,
        StoreManagerInterface $storeManager,
        CustomerFactory $customerFactory,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        ConfigProvider $amastyConfigProvider,
        CollectionFactory $amastyCollectionFactory,
        RegionFactory $regionFactory, 
        TypeListInterface $cacheTypeList, 
        Pool $cacheFrontendPool,
        AttributeCollection $attributeCollection
    ) {
        parent::__construct($context);
        $this->customerFactory           =   $customerFactory;
        $this->cookieManager             =   $cookieManager;
        $this->cookieMetadataFactory     =   $cookieMetadataFactory;
        $this->_amastyCollectionFactory  =   $amastyCollectionFactory;
        $this->_curl                     =   $curl;
        $this->_coreSession              =   $coreSession;
        $this->_customerSession          =   $customerSession;
        $this->_regionFactory            =   $regionFactory;
        $this->storeManager              =   $storeManager;
        $this->_amastyconfigProvider     =   $amastyConfigProvider;
        $this->attributeCollection       =   $attributeCollection;
        $this->cacheTypeList             =   $cacheTypeList;
        $this->cacheFrontendPool         =   $cacheFrontendPool;
    }

    /**
    * Gets the region data from state ID.
    * @param $state - numbers state ID
    *
    * @return string [code]
    */
    public function getRegionDataByName($state) {
        try {
            $region   = $this->_regionFactory->create();
            $regionData = $region->load($state);
            if($regionData->getId()){
                $shipperRegionCode = $regionData->getCode();
            }
            return $shipperRegionCode;
        }
        catch(\Exception $e) {
            return false;
        }
    }
/**
     * Set the nearest location in session 
     */
    public function setNearestStore ($storeDetails) {
        $this->_coreSession->setNearestStore($storeDetails);
    }

    /**
     * get the nearest location from session 
     */
    public function getNearestStore () {
        return $this->_coreSession->getNearestStore();
    }

    /**
     * Function to set the store location
     */
    public function setZipcode(){
        $this->_coreSession->setZipcode(1);
    }
    
     /**
     * Function to set the store location
     */
    public function getZipcode(){
       return $this->_coreSession->getZipcode();
    }
    /**
     * Function to get user's data from customer session
     * @return int
     */
    public function getCustomer() {        
        return $this->_customerSession->getCustomer()->getId();
    }
    /**
     * Get data from cookie set in remote address
     *
     * @return value
     */
    public function getCookie($name)
    {
        return $this->cookieManager->getCookie($name);
    }

    /** Delete custom Cookie */
    public function deleteCookie($name)
    {
        if ($this->cookieManager->getCookie($name)) {
            $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata();
            $metadata->setPath('/');

            return $this->cookieManager->deleteCookie(
               $name,$metadata);
        }
    }

    /**
     * @param string $value
     * @param int $duration
     * @return void
     */
    public function setCookie($cookiename,$value, $duration = 3600)
    {
       $metadata = $this->cookieMetadataFactory
           ->createPublicCookieMetadata()
           ->setDuration($duration)
           ->setPath($this->_coreSession->getCookiePath())
           ->setDomain($this->_coreSession->getCookieDomain());

           $this->cookieManager->setPublicCookie(
               $cookiename,
               $value,
               $metadata
           );
    }

    /**
     * Get the customer zip if logged in
     */
    public function getCustomerZipcode(){
        
        if ($this->_customerSession->isLoggedIn()) 
        {
            $customerId= $this->getcustomer();
            $customer = $this->customerFactory->create();
            $websiteId = $this->storeManager->getStore()->getWebsiteId();
            $customer->setWebsiteId($websiteId);
            $customerModel = $customer->load($customerId);

            $customerAddress = [];
 
            if ($customerModel->getAddresses() != null) {
                foreach ($customerModel->getAddresses() as $address) {
                    $customerAddress[] = $address->toArray();
                }
                foreach ($customerAddress as $customerAddres){
                    $city       = $customerAddres['city'];
                    $region     = $this->getRegionDataByName($customerAddres['region_id']);
                    $postcode   = $customerAddres['postcode'];
                }   
                
                $this->deleteCookie("zipcode");
                $this->setCookie("zipcode",$region." ".$customerAddres['postcode']);

                return $postcode; 
            }
            return ''; 
        }
        return '';
    }

    /**
     * Function to get the nearest store with zipcode
     * @param string $zipcode
     */
    public function getZipNearestStore($zipcode) {
        //Check the Zipcode is valid
        $storeListArr   =   array();
        $response       =   $this->getLnt($zipcode);   

        if($response !=''){
            $newzipLat = $response['lat'];
            $newzipLng = $response['lng'];
 
            $collection = $this->_amastyCollectionFactory->create();    
            $select = $collection->getSelect();
            $select->where('main_table.status = 1');
            $select->having('distance < 40');
            $select->order("distance");
            $select->columns(
                [
                    'distance' => 'SQRT(POW(69.1 * (main_table.lat - ' . $newzipLat . '), 2) + '
                    . 'POW(69.1 * (' . $newzipLng . ' - main_table.lng) * COS(main_table.lat / 57.3), 2))'
                ]
            );
            $select->limit(1);
             
            if($collection->getData()){
                foreach ($collection->getData() as $location):
                    $stateCode                 =   $this->getRegionDataByName( $location['state'] );
                    $storeListArr['id']        =   $location['id'];
                    $storeListArr['name']      =   $location['name'];
                    $storeListArr['zip']       =   $location['zip'];
                    $storeListArr['address']   =   $location['address'];
                    $storeListArr['phone']     =   $location['phone'];
                    $storeListArr['state']     =   $stateCode;
                    $storeListArr['city']      =   $location['city'];
                    $storeListArr['url_key']   =   $location['url_key'];
                    $storeListArr['distance']  =   round($location['distance'],2);
                endforeach;

                $this->deleteCookie("nearestid");
                $this->setCookie('nearestid', $storeListArr['id']);
                $this->deleteCookie("distance");
                $this->setCookie('distance', $storeListArr['distance']);
                $this->deleteCookie("zipcode");
                $this->setCookie("zipcode",$storeListArr['state']." ".$storeListArr['zip']);

                return json_encode($storeListArr);
            }
                return '';
        }
        return '';

    }

     /**
     * Function to get the Lattitude and longitude with the Zipcode
     */
    public function getLnt($zip){
        // Get the API key from Amasty
        $apiKey = $this->_amastyconfigProvider->getApiKey();
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=".$zip."&sensor=false&key=".$apiKey;

        $resultString =  $this->_curl->get($url);
        $resultString = $this->_curl->getBody();

        $result = json_decode($resultString, true);
        $storeListArr   =   array();

        if(isset($result['results'][0])) {
            $result1[]  =   $result['results'][0];
            $result2[]  =   $result1[0]['geometry'];
            $result3[]  =   $result2[0]['location'];
            return $result3[0];
        } else {
            return '';
        }
        
    }


    /**
     * Function to get the store details
     * @param int $storeId
     */
    public function getStoreDetails ($storeId) {
        $collection = $this->_amastyCollectionFactory->create();    
        $select = $collection->getSelect();
        $select->where('main_table.id = '. $storeId);

        return $collection->getData();
    }

    
    /**
     * Function to get the store Hours
     */
    public function getStoreHours () {
        $collection =  $this->attributeCollection->preparedAttributes(true);    
     
        return $collection;
    }

    /**
     * media path 
     * @return string
     */
    public function getMediaPath(){
        $store      =   $this->storeManager->getStore();
        $mediaUrl   =   $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl;
    }

    public function flushCache()
    {
        $_types = [
                'block_html'
                ];
    
        foreach ($_types as $type) {
            $this->cacheTypeList->cleanType($type);
        }
        foreach ($this->cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }
    }
}