<?php
	/**
	 * Rewrite Amasty Storelocator AJAX Controller
	 *
	 * @category    Mancini
	 * @package     Mancini_Storelocator
	 * @author      Mancini
	 *
	 */
    declare(strict_types=1);

    namespace Mancini\Storelocator\Controller\Index;
    use \Magento\Framework\Session\SessionManagerInterface as CoreSession;

	class Ajax extends \Amasty\Storelocator\Controller\Index\Ajax
	{    
         /**
         * @var Magento\Framework\Session\SessionManagerInterface
         */
        protected $_coreSession;

        /**
         * Constructor
         *
         * @param \Magento\Framework\Session\SessionManagerInterface $coreSession
         * @param \Magento\Framework\App\Action\Context $context
         */
        public function __construct(
            \Magento\Framework\App\Action\Context $context,
            CoreSession $coreSession
            ) {
                $this->_coreSession     = $coreSession;
                parent::__construct($context);
        }

		/**
		 * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
		 */
		public function execute()
		{          
            $this->_view->loadLayout();

            /** @var \Amasty\Storelocator\Block\Location $block */
            $block = $this->_view->getLayout()->getBlock('amlocator_ajax');
            
            /** Fore saving the values to the session */
            $response       = $block->getJsonLocations();
            $nearestStores  = json_decode($response);
            $nearestStore   = $nearestStores->items;
          
            if(!empty($nearestStore[0])){
                $sessionArray['id']         = $nearestStore[0]->id;
                $sessionArray['zip']        = $nearestStore[0]->zip;
                $sessionArray['address']    = $nearestStore[0]->address;
                $sessionArray['state']      = $nearestStore[0]->state;

                $this->_coreSession->setNearestStore($sessionArray);
            }

            $this->getResponse()->setBody($response);
		}
	}