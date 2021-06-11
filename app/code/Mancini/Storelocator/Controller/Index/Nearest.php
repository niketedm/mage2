<?php
	/**
	 * Nearest Store AJAX Controller
	 *
	 * @category    Mancini
	 * @package     Mancini_Storelocator
	 * @author      Mancini
	 *
	 */
    declare(strict_types=1);

    namespace Mancini\Storelocator\Controller\Index;
    
    use Magento\Framework\App\Action\Action;
    use Magento\Framework\App\Action\Context;
    use Magento\Framework\Controller\Result\JsonFactory;
    use Magento\Framework\View\Result\PageFactory;
    use Magento\Customer\Model\Session;
    use Amasty\Storelocator\Model\Location;
    use Mancini\Storelocator\Helper\Data;
    use Amasty\Storelocator\Model\ConfigProvider;
    use Magento\Framework\HTTP\Client\Curl;
    use Amasty\Geoip\Model\Geolocation;
    use Magento\Framework\HTTP\PhpEnvironment\Request;
    use \Magento\Framework\Session\SessionManagerInterface as CoreSession;
    use Amasty\Storelocator\Model\ResourceModel\Location\CollectionFactory;

	class Nearest extends Action
	{    
        /**
         * @var Magento\Customer\Model\Session
         */
        protected $_customerSession;

        /**
         * @var \Amasty\Storelocator\Model\ConfigProvider
         */
        protected $_amastyconfigProvider;

        /**
         * @var PageFactory
         */
        protected $_resultPageFactory;

        /**
         * @var JsonFactory
         */
        protected $_resultJsonFactory;

        /**
         * @var \Amasty\Storelocator\Model\Location
         */
        protected $_amastyLocationModel;

        /**
         * @var Mancini\Storelocator\Helper\Data
         */
        protected $_storeHelper;
        
        /**
         * @var \Magento\Framework\HTTP\Client\Curl
         */
        protected $_curl;

        /**
         * @var Magento\Framework\Session\SessionManagerInterface
         */
        protected $_coreSession;

        /**
         * @var CollectionFactory
         */
        private $locationCollectionFactory;

        /**
         * @var Geolocation
         */
        private $geolocation;

        /**
         * @var Request
         */
        protected $httpRequest;
        
        /**
         * Constructor
         *
         * @param \Magento\Customer\Model\Session $customerSession
         * @param \Magento\Framework\App\Action\Context $context
         * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
         * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
         * @param \Amasty\Storelocator\Model\Location $amastyLocationModel
         * @param \Mancini\Storelocator\Helper\Data $storeHelper
         * @param \Magento\Framework\HTTP\Client\Curl $curl
         * @param \Magento\Framework\HTTP\PhpEnvironment\Request $httpRequest
         * @param \Amasty\Storelocator\Model\ConfigProvider $amastyconfigProvider
         * @param \Magento\Framework\Session\SessionManagerInterface $coreSession
         */
        public function __construct(
            Context $context,
            PageFactory $resultPageFactory,
            JsonFactory $resultJsonFactory,
            Session $customerSession,
            Data $storeHelper,
            Location $amastyLocationModel,
            Geolocation $geolocation,
            ConfigProvider $amastyconfigProvider,
            CollectionFactory $locationCollectionFactory,
            CoreSession $coreSession,
            Curl $curl,
            Request $httpRequest
        ) {
            $this->_resultPageFactory        =   $resultPageFactory;
            $this->_resultJsonFactory        =   $resultJsonFactory;
            $this->_customerSession          =   $customerSession;
            $this->_amastyLocationModel      =   $amastyLocationModel;
            $this->_storeHelper              =   $storeHelper;
            $this->_curl                     =   $curl;
            $this->geolocation               =   $geolocation;
            $this->_amastyconfigProvider     =   $amastyconfigProvider;
            $this->_coreSession              =   $coreSession;
            $this->httpRequest               =   $httpRequest;
            $this->locationCollectionFactory =   $locationCollectionFactory;
            parent::__construct($context);
        }

		/**
		 * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
		 */
		public function execute()
		{          
            $result       =   $this->_resultJsonFactory->create();
            $resultPage   =   $this->_resultPageFactory->create();
            $nearestStore =   array();
            $latitude     =   $this->getRequest()->getParam('latitude');
            $longitude    =   $this->getRequest()->getParam('longitude');

            if($latitude == null || $longitude == null){
                $ip = $this->httpRequest->getClientIp();

                $geodata = $this->geolocation->locate($ip);
                $latitude = $geodata->getLatitude();
                $longitude = $geodata->getLongitude();
            }

            $resultLat = $this->getLatLngUsingIP($latitude, $longitude);

            $nearestStore['nearestset']   =   0;
            $nearestStore['custzip']      =   $resultLat['zip'];
            $nearestStore['custstate']    =   $resultLat['state'];
            $nearestStore['locality']     =   $resultLat['locality'];

            $this->locationCollection = $this->locationCollectionFactory->create();
            if($latitude && $longitude) {
                $select = $this->locationCollection->getSelect();
                $select->where('main_table.status = 1');
                $select->having('distance < 40');
                $select->order("distance");
                $select->columns(
                    [
                        'distance' => 'SQRT(POW(69.1 * (main_table.lat - ' . $latitude . '), 2) + '
                        . 'POW(69.1 * (' . $longitude . ' - main_table.lng) * COS(main_table.lat / 57.3), 2))'
                    ]
                );

                if($this->locationCollection->getData()){
                    $nearestLocArray            =   $this->locationCollection->getData();
                    $nearestStore['nearestset'] =   1;
                    $nearestStore['address']    =   $nearestLocArray[0]['address'];
                    $nearestStore['state']      =   $this->_storeHelper->getRegionDataByName($nearestLocArray[0]['state']);
                    $nearestStore['zip']        =   $nearestLocArray[0]['zip'];

                    $this->_storeHelper->setCookie("zipcode",$nearestStore['state']." ".$nearestStore['zip']);
                    $this->_storeHelper->setCookie("custzipcode",$nearestStore['state']." ".$nearestStore['zip']);
                    $this->_storeHelper->setCookie("custloc",$nearestStore['address'].",".$nearestStore['state']); 
                    $this->_storeHelper->setCookie("nearestid",$nearestLocArray[0]['id']); 

                    $this->_storeHelper->deleteCookie("distance");
                    $this->_storeHelper->setCookie('distance', $nearestLocArray[0]['distance']);
    
                }
                
                $this->_coreSession->setNearestStore($nearestStore);
                if($nearestStore['nearestset'] == 0 ) {
                    $this->_storeHelper->setCookie("custzipcode",$nearestStore['custstate']." ".$nearestStore['custzip']);
                    $this->_storeHelper->setCookie("zipcode",$nearestStore['custstate']." ".$nearestStore['custzip']);
                    $this->_storeHelper->setCookie("custloc","NA");    
                }
            }
           
            $result->setData(['output'=>$nearestStore]);
           
            return $result;
        }

        /**
         * Function to get the nearest store using IP
         */
        public function getLatLngUsingIP ($lat, $lng){
            // Get the API key from Amasty
            $apiKey  = $this->_amastyconfigProvider->getApiKey();
            $nearestArray   =   array();
          
            $url    =   'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lng.'&key='.$apiKey;
            $resultString =  $this->_curl->get($url);
            $resultString = $this->_curl->getBody();
            $result = json_decode($resultString, true);
            $addressResult  =   $result['results'][0]['address_components'];

            foreach($addressResult as $address){
                switch ($address['types'][0]){
                    case "administrative_area_level_1": 
                                $nearestArray['state']    =   $address['short_name'];
                                break;
                    case "postal_code": 
                                $nearestArray['zip']      =   $address['short_name'];
                                break;
                    case "locality":
                                $nearestArray['locality'] =   $address['long_name'];
                                break;
                    case "default": break;
                }
                
            }

            //Save the customer location in session
            $this->_coreSession->setCustomerLocation($nearestArray);

            return $nearestArray;
            
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