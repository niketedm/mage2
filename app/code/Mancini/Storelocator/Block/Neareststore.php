<?php
/**
 * Copyright ©  All rights reserved.
 * @category    Mancini
 * @package     Mancini_Storelocator
 * @author      Mancini
 *
 */
declare(strict_types=1);

namespace Mancini\Storelocator\Block;

use Amasty\Storelocator\Model\ResourceModel\Location\CollectionFactory;
use Amasty\Storelocator\Model\ConfigProvider;
use Mancini\Storelocator\Helper\Data;
use Amasty\Geoip\Model\Geolocation;
use Magento\Framework\HTTP\PhpEnvironment\Request;

class Neareststore extends \Magento\Framework\View\Element\Template
{

     /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Amasty\Storelocator\Model\ResourceModel\Location\CollectionFactory
     */
    protected $_amastyCollectionFactory;
    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected $_curl;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Amasty\Storelocator\Model\ConfigProvider
     */
    protected $_amastyconfigProvider;

    /**
     * @var Mancini\Storelocator\Helper\Data
     */
    protected $_storeHelper;

    /**
     * @var Request
     */
    protected $httpRequest;

    /**
     * @var Geolocation
     */
    private $geolocation;
    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Amasty\Storelocator\Model\ResourceModel\Location\CollectionFactory $amastyCollectionFactory
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \Amasty\Storelocator\Model\ConfigProvider $amastyConfigProvider
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Mancini\Storelocator\Helper\Data $storeHelper
     * @param \Amasty\Geoip\Model\Geolocation $geolocation
     * @param \Magento\Framework\HTTP\PhpEnvironment\Request $httpRequest
     * @param array $data
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,        
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\View\Element\Template\Context $context,
        ConfigProvider $amastyConfigProvider,
        CollectionFactory $amastyCollectionFactory,
        Data $storeHelper,
        Request $httpRequest,
        Geolocation $geolocation,
        array $data = []
    ) {
        $this->_customerSession          =   $customerSession;
        $this->_amastyCollectionFactory  =   $amastyCollectionFactory;
        $this->jsonHelper                =   $jsonHelper;
        $this->geolocation               =   $geolocation;
        $this->_curl                     =   $curl;
        $this->httpRequest               =   $httpRequest;
        $this->_amastyconfigProvider     =   $amastyConfigProvider;
        $this->_storeHelper              =   $storeHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getNeareststore() {
        return $this->_storeHelper->getNearestStore();
    }
    /**
     * Function to set the store location
     */
    public function setZipcode(){
        $this->_storeHelper->setZipcode(1);
    }
    
    /**
     * Function to set the store location
     */
    public function getZipcode(){
        return $this->_storeHelper->getZipcode();
    }
    
    
    /**
     * Function to get the store list
     */
    public function getNearestStoreLoc($zipCode){

        //Check the Zipcode is valid
        $storeListArr   =   array();
        $response       =   $this->getLnt($zipCode);

        if($response !=''){
            $newzipLat = $response['lat'];
            $newzipLng = $response['lng'];

            $collection = $this->_amastyCollectionFactory->create();    
            $select = $collection->getSelect();
            $select->where('main_table.status = 1');
            $select->having('distance < 40 OR zip ='. $zipCode);
            $select->order("distance");
            $select->columns(
                [
                    'distance' => 'SQRT(POW(69.1 * (main_table.lat - ' . $newzipLat . '), 2) + '
                    . 'POW(69.1 * (' . $newzipLng . ' - main_table.lng) * COS(main_table.lat / 57.3), 2))'
                ]
            );

            
            
            if($collection->getData()):
                $count  =   0;
                foreach ($collection->getData() as $location):
                    if($count < 5 ){
                        $stateCode                         =   $this->_storeHelper->getRegionDataByName( $location['state'] );
                        $storeListArr[$count]['id']        =   $location['id'];
                        $storeListArr[$count]['name']      =   $location['name'];
                        $storeListArr[$count]['zip']       =   $location['zip'];
                        $storeListArr[$count]['address']   =   $location['address'];
                        $storeListArr[$count]['phone']     =   $location['phone'];
                        $storeListArr[$count]['state']     =   $stateCode;
                        $storeListArr[$count]['city']      =   $location['city'];
                        $storeListArr[$count]['url_key']   =   $location['url_key'];
                        $storeListArr[$count]['distance']  =   round($location['distance'],2);
                    }
                    $count++;
                endforeach;
            endif;
        }
      
        return $this->jsonHelper->jsonEncode($storeListArr);
    }

    /**
     * Create json response
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }

    /**
     * Function to get the Lattitude and longitude with the Zipcode
     */
    public function getLnt($zip){
        // Get the API key from Amasty
        $apiKey = $this->_amastyconfigProvider->getApiKey();
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=".$zip."&sensor=true&key=".$apiKey;

        $resultString =  $this->_curl->get($url);
        $resultString = $this->_curl->getBody();

        $result = json_decode($resultString, true);
        if(isset($result['results'][0])) {
            $result1[]  =   $result['results'][0];
            $result2[]  =   $result1[0]['geometry'];
            $result3[]  =   $result2[0]['location'];
            return $result3[0];
        } else {
            return '';
        }
        
    }
   
}