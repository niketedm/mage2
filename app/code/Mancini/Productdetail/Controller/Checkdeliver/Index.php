<?php
namespace Mancini\Productdetail\Controller\Checkdeliver;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Mancini\ShippingZone\Helper\Data;
use Mancini\ShippingZone\Model\ShippingZone\Zipcodes;

class Index extends \Magento\Framework\App\Action\Action
{
     /**
     * @var  \Mancini\ShippingZone\Model\ShippingZone\CollectionFactory
     */
    protected $locationModelFactory;
     /**
     * @var Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var Mancini\ShippingZone\Helper\Data
     */
    protected $_ManciniHelperData;
     /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param Mancini\ShippingZone\Helper\Data $helperData
     * @param Mancini\ShippingZone\Model\ShippingZone\Zipcodes $locationModelFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory,
        Data $ManciniHelperData,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        Zipcodes $locationModelFactory,
        \Mancini\Storelocator\Helper\Data $storeHelper,
        \Psr\Log\LoggerInterface $logger
        
    ) {
        $this->locationModelFactory =  $locationModelFactory;
        $this->_ManciniHelperData   = $ManciniHelperData;
        $this->resultPageFactory    = $resultPageFactory;
        $this->resultJsonFactory    = $resultJsonFactory; 
        $this->jsonHelper           = $jsonHelper;
        $this->_storeHelper         =   $storeHelper;
        $this->logger               = $logger;
        parent::__construct($context);
    }


    public function execute()
    {
        $contact = '';
        $nextZipResult = '';
        try {
        $zipcode    = $this->getRequest()->getParam('zipcode');
        //code for getting address based using zipcode form Mancini_shippingzone module
        $collection = $this->locationModelFactory->getCollection()->addFieldToFilter('zipcode',$zipcode);;
        $result = $collection->getData();
            if($result!=null){
                    foreach ($result as $customerAddres)
                        {
                            $city       =   $customerAddres['city'];
                            $region     =   $customerAddres['state']; 
                            $postcode   =   $customerAddres['zipcode']; 
                                
                            $address 	= $city ." , " .$region ." - " .$postcode;
                        } 
            }
            else{
                $city = $region = $postcode ="NA";
                $address="NA";
            }
            $this->_storeHelper->deleteCookie("zipcodedeliverto");
            $this->_storeHelper->deleteCookie("custlocdeliverto");
            $this->_storeHelper->setCookie("zipcodedeliverto",$region." ".$postcode);
            $this->_storeHelper->setCookie("custlocdeliverto",$city .",".$region); 
          
            
            $zone_id = $this->_ManciniHelperData->getShippingZoneByZipcode($zipcode);
            if($zone_id == null) {
                $zipResult  = "Please call";
                $contact = '800-647-5337';
                $nextZipResult = "for delivery options to this zipcode.";

            }
            else{
                $zipResult = 1;
            }

        $result = ['success' => true, 'updateaddress' =>  $address ,'updateavailability'=>$zipResult, 'contact'=> $contact , 'nextzipresult'=> $nextZipResult ];
        return $this->jsonResponse($result);
    } catch (\Magento\Framework\Exception\LocalizedException $e) {
        return $this->jsonResponse($e->getMessage());
    } catch (\Exception $e) {
        $this->logger->critical($e);
        return $this->jsonResponse($e->getMessage());
    }
}

    /**
     * Create json response
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }
}

