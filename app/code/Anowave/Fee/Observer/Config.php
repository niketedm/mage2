<?php
/**
 * Anowave Magento 2 Extra Fee
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * http://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Anowave
 * @package 	Anowave_Fee
 * @copyright 	Copyright (c) 2020 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */

namespace Anowave\Fee\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class Config implements ObserverInterface
{
	const DEFAULT_CONDITION_VALUE = 1;
	
	/**
	 * @var \Anowave\Fee\Helper\Data 
	 */
	protected $_helper = null;
	
	/**
	 * @var \Magento\Framework\Message\ManagerInterface
	 */
	protected $_messageManager 	= null;
	
	/**
	 * @var \Magento\Framework\App\RequestInterface
	 */
	protected $request;
	
	/**
	 * API 
	 * 
	 * @var \Anowave\Fee\Model\Api
	 */
	protected $api = null;
	
	/**
	 * @var \Anowave\Fee\Model\ConditionsFactory
	 */
	protected $conditionsFactory;
	
	/**
	 * @var \Anowave\Fee\Model\ResourceModel\Conditions\CollectionFactory
	 */
	protected $conditionsCollectionFactory;
	
	/**
	 * @var \Magento\Backend\Model\Session
	 */
	protected $session;
	
	/**
	 * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
	 */
	protected $dateFilter;
	
	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $storeManager;
	
	/**
	 * Constructor 
	 * 
	 * @param \Anowave\Fee\Helper\Data $helper
	 * @param \Magento\Framework\Message\ManagerInterface $messageManager
	 * @param \Magento\Framework\App\RequestInterface $request
	 */
	public function __construct
	(
		\Anowave\Fee\Helper\Data $helper, 
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Framework\App\RequestInterface $request,
		\Anowave\Fee\Model\ConditionsFactory $conditionsFactory,
		\Anowave\Fee\Model\ResourceModel\Conditions\CollectionFactory $conditionsCollectionFactory,
		\Magento\Backend\Model\Session $session,
		\Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
		\Magento\Store\Model\StoreManagerInterface $storeManager
	)
	{
		/**
		 * Set helper 
		 * 
		 * @var \Anowave\Fee\Observer\Config $_helper
		 */
		$this->_helper = $helper;
		
		/**
		 * Set message manager 
		 * 
		 * @var \Anowave\Fee\Observer\Config $_messageManager
		 */
		$this->_messageManager = $messageManager;
		
		/**
		 * Set request 
		 * 
		 * @var \Magento\Framework\App\RequestInterface
		 */
		$this->request = $request;
		
		/**
		 * Set conditions factory 
		 * 
		 * @var \Anowave\Fee\Model\ConditionsFactory
		 */
		$this->conditionsFactory = $conditionsFactory;
		
		/**
		 * Set conditions collection factory
		 * 
		 * @var \Anowave\Fee\Model\ResourceModel\Conditions\CollectionFactory $conditionsCollectionFactory
		 */
		$this->conditionsCollectionFactory = $conditionsCollectionFactory;
		
		/**
		 * Set session 
		 * 
		 * @var \Magento\Backend\Model\Session
		 */
		$this->session = $session;
		
		/**
		 * Set date filter 
		 * 
		 * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
		 */
		$this->dateFilter = $dateFilter;
		
		/**
		 * Set store manager 
		 * 
		 * @var \Anowave\Fee\Observer\Config $storeManager
		 */
		$this->storeManager = $storeManager;
	}
	
	/**
	 * Execute 
	 * 
	 * {@inheritDoc}
	 * @see \Magento\Framework\Event\ObserverInterface::execute()
	 */
	public function execute(EventObserver $observer)
	{
		$this->_helper->notify($this->_messageManager);
		
		if (!$this->getRequest()->getPostValue()) 
		{
			return true;	
		}
		
		try 
		{
			$collection = $this->conditionsCollectionFactory
							   ->create()
							   ->addFieldToFilter('rule_default',self::DEFAULT_CONDITION_VALUE)
							   ->addFieldToFilter('rule_fee_store_id', $this->getCurrentScopeStoreId());
			
			if ($collection->getSize())
			{
				$model = $collection->getFirstItem();
			}
			else 
			{
				$model = $this->conditionsFactory->create();
			}

			/**
			 * Get data 
			 * 
			 * @var [] $data
			 */
			$data = $this->getRequest()->getPostValue();
			
			/**
			 * Create input filter 
			 * 
			 * @var \Zend_Filter_Input $inputFilter
			 */
			$inputFilter = new \Zend_Filter_Input(['from_date' => $this->dateFilter, 'to_date' => $this->dateFilter],[],$data);
			
			/**
			 * Filter data
			 */
			$data = $inputFilter->getUnescaped();
			
			/**
			 * Validate result 
			 * 
			 * @var bool $validateResult
			 */
			$validateResult = $model->validateData(new \Magento\Framework\DataObject($data));
			
			if ($validateResult !== true) 
			{
				foreach ($validateResult as $errorMessage) 
				{
					$this->_messageManager->addErrorMessage($errorMessage);
				}
				
				return false;
			}
		
			$data = $this->prepareData($data);
			
			/**
			 * Load post data into model
			 */
			$model->loadPost($data);
			
			/**
			 * Set as default rule
			 */
			$model->setRuleDefault(self::DEFAULT_CONDITION_VALUE);
			
			/**
			 * Set website id
			 */
			$model->setRuleFeeWebsiteId
			(
				$this->getCurrentScopeWebsiteId()
			);
			
			/**
			 * Set store id
			 */
			$model->setRuleFeeStoreId
			(
				$this->getCurrentScopeStoreId()
			);
			
			/**
			 * Save model
			 */
			$model->save();
		} 
		catch (\Magento\Framework\Exception\LocalizedException $e) 
		{
			$this->_messageManager->addErrorMessage($e->getMessage());
		} 
		catch (\Exception $e) 
		{
			$this->_messageManager->addErrorMessage($e->getMessage());
		}

		return true;
	}
	
	/**
	 * Prepares specific data
	 *
	 * @param [] $data
	 * @return []
	 */
	protected function prepareData($data)
	{
		if (isset($data['rule']['conditions'])) 
		{
			$data['conditions'] = $data['rule']['conditions'];
		}
	
		unset($data['rule']);
		
		$data['name'] 			= __('Default condition');
		$data['description'] 	= __('Applies for all fees');
	
		return $data;
	}
	

	/**
	 * Get current website id
	 * 
	 * @return number
	 */
	public function getCurrentScopeWebsiteId()
	{
		return $this->_helper->getCurrentScopeWebsiteId();
	}
	
	/**
	 * Get current store id 
	 * 
	 * @return number
	 */
	public function getCurrentScopeStoreId()
	{
		return $this->_helper->getCurrentScopeStoreId();
	}
	
	/**
	 * Get request
	 *
	 * @return \Magento\Framework\App\RequestInterface
	 */
	public function getRequest()
	{
		return $this->request;
	}
}