<?php
	/**
	 * Change Zipcode AJAX Controller
	 *
	 * @category    Mancini
	 * @package     Mancini_Storelocator
	 * @author      Mancini
	 *
	 */
    declare(strict_types=1);

    namespace Mancini\Storelocator\Controller\Changezip;
    
    use Magento\Framework\App\Action\Action;
    use Magento\Framework\App\Action\Context;
    use Magento\Framework\Controller\Result\JsonFactory;
    use Magento\Framework\View\Result\PageFactory;
    use Magento\Customer\Model\Session;

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
         * Constructor
         *
         * @param \Magento\Customer\Model\Session $customerSession
         * @param \Magento\Framework\App\Action\Context $context
         * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
         * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
         */
        public function __construct(
            Context $context,
            PageFactory $resultPageFactory,
            JsonFactory $resultJsonFactory,
            Session $customerSession
        ) {
            $this->_resultPageFactory   =   $resultPageFactory;
            $this->_resultJsonFactory   =   $resultJsonFactory;
            $this->_customerSession = $customerSession;
            parent::__construct($context);
        }

		/**
		 * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
		 */
		public function execute()
		{          
            $result =   $this->_resultJsonFactory->create();
            $resultPage =   $this->_resultPageFactory->create();
            $currentZipcode = $this->getRequest()->getParam('zipcode');
    
            $data   =    array('currentZipcode' => $currentZipcode);
    
            $block  =   $resultPage->getLayout()
                        ->createBlock('Mancini\Storelocator\Block\Neareststore')
                        ->setTemplate('Mancini_Storelocator::storelist.phtml')
                        ->setData('data', $data)
                        ->toHtml();
    
            $result->setData(['output'=>$block]);
            return $result;
		}
	}