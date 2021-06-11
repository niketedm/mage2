<?php
	/**
	 * Update Zipcode AJAX Controller
	 *
	 * @category    Mancini
	 * @package     Mancini_Storelocator
	 * @author      Mancini
	 *
	 */
    declare(strict_types=1);

    namespace Mancini\Storelocator\Controller\Updatezip;
    
    use Magento\Framework\App\Action\Action;
    use Magento\Framework\App\Action\Context;
    use Magento\Framework\Controller\Result\JsonFactory;
    use Magento\Framework\View\Result\PageFactory;
    use Magento\Customer\Model\Session;
    use Amasty\Storelocator\Model\Location;
    use Mancini\Storelocator\Helper\Data;

	class Index extends Action
	{    
        /**
         * @var Magento\Customer\Model\Session
         */
        protected $_customerSession;

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
         * Constructor
         *
         * @param \Magento\Customer\Model\Session $customerSession
         * @param \Magento\Framework\App\Action\Context $context
         * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
         * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
         * @param \Amasty\Storelocator\Model\Location $amastyLocationModel
         * @param \Mancini\Storelocator\Helper\Data $storeHelper
         */
        public function __construct(
            Context $context,
            PageFactory $resultPageFactory,
            JsonFactory $resultJsonFactory,
            Session $customerSession,
            Data $storeHelper,
            Location $amastyLocationModel
        ) {
            $this->_resultPageFactory   =   $resultPageFactory;
            $this->_resultJsonFactory   =   $resultJsonFactory;
            $this->_customerSession     =   $customerSession;
            $this->_amastyLocationModel =   $amastyLocationModel;
            $this->_storeHelper              =   $storeHelper;
            parent::__construct($context);
        }

		/**
		 * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
		 */
		public function execute()
		{          
            $result     =   $this->_resultJsonFactory->create();
            $resultPage =   $this->_resultPageFactory->create();
            $storeId    =   $this->getRequest()->getParam('storeid');
            $distance   =   $this->getRequest()->getParam('distance');

            $storeDetails   =   $this->_amastyLocationModel->load($storeId);
            $storeDetail    =   $storeDetails->getData();

            $storeArray = [
                'id' => (int)$storeDetail['id'],
                'zip' => $storeDetail['zip'],
                'address'=>$storeDetail['address'],
                'state'=>$this->_storeHelper->getRegionDataByName($storeDetail['state']),
                'name'=>$storeDetail['name'],
                'city'=>$storeDetail['city'],
                'phone'=>$storeDetail['phone'],
                'url_key'=>$storeDetail['url_key'],
                'distance'=>$distance
            ];
    
            //Set the new address location to the Cookie
            $this->_storeHelper->deleteCookie("custloc");
            $this->_storeHelper->deleteCookie("nearestid");
            $this->_storeHelper->deleteCookie("distance");
            $this->_storeHelper->deleteCookie("zipcode");
            
            $this->_storeHelper->setCookie('distance', $distance);
            $this->_storeHelper->setCookie("custloc",$storeArray['address'].",".$storeArray['city'].",".$storeArray['state']);
            $this->_storeHelper->setCookie("nearestid", $storeArray['id']);
            $this->_storeHelper->setCookie("zipcode",$storeArray['state']." ".$storeArray['zip']);
                        
            $result->setData(['output'=>$storeArray]);
            $this->_storeHelper->flushCache();

            return $result;
        }
    }