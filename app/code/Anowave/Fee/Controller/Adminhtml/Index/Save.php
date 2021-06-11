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

namespace Anowave\Fee\Controller\Adminhtml\Index;

class Save extends \Magento\Backend\App\Action
{
	/**
	 * @var \Anowave\Fee\Model\FeeFactory
	 */
	protected $factory;
	
	/**
	 * @var \Anowave\Fee\Model\ConditionsFactory
	 */
	protected $conditionsFactory;
	
	/**
	 * @var \Anowave\Fee\Model\ResourceModel\Conditions\CollectionFactory
	 */
	protected $conditionsCollectionFactory;
	
	/**
	 * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
	 */
	protected $dateFilter;
	
	/**
	 * Cosntructor 
	 * 
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param \Anowave\Fee\Model\FeeFactory $factory
	 */
	public function __construct
	(
		\Magento\Backend\App\Action\Context $context,
		\Anowave\Fee\Model\FeeFactory $factory,
		\Anowave\Fee\Model\ConditionsFactory $conditionsFactory,
		\Anowave\Fee\Model\ResourceModel\Conditions\CollectionFactory $conditionsCollectionFactory,
		\Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
	)
	{
		parent::__construct($context);
		
		/**
		 * Set fee factory 
		 * 
		 * @var \Anowave\Fee\Model\FeeFactory  $factory
		 */
		$this->factory = $factory;
		
		/**
		 * Set conditions collection factory 
		 * 
		 * @var \Anowave\Fee\Controller\Adminhtml\Index\Save $conditionsCollectionFactory
		 */
		$this->conditionsCollectionFactory = $conditionsCollectionFactory;
		
		/**
		 * Set conditions factory 
		 * 
		 * @var \Anowave\Fee\Model\ConditionsFactory $conditionsFactory
		 */
		$this->conditionsFactory = $conditionsFactory;
		
		/**
		 * Set date filter
		 *
		 * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
		 */
		$this->dateFilter = $dateFilter;
	}
	
	public function execute()
	{
		$errors = [];
		
		$data = $this->getRequest()->getPostValue();

		$fee_id = $this->getCurrentFeeId();
		
		if ($data) 
		{
			try 
			{
				$fee = $this->factory->create();
				
				if ($fee_id)
				{
					/**
					 * Load model
					 */
					$fee->load($fee_id);
				}
				
				/**
				 * Set data
				 */
				$fee->setData($data['fee']);
				
				/**
				 * Save model
				 */
				$fee->save();
				
				/**
				 * Get model id
				 */
				$fee_id = $fee->getId();
				
				/**
				 * Save fee conditions
				 */
				if ($fee_id)
				{
					$collection = $this->conditionsCollectionFactory->create()->addFieldToFilter('rule_fee_id',$fee_id);
					
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
							$this->messageManager->addErrorMessage($errorMessage);
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
					$model->setRuleDefault(0);
					
					/**
					 * Set fee
					 */
					$model->setRuleFeeId($fee_id);

					/**
					 * Save model
					 */
					$model->save();
					
				}
			}
			catch (\Exception $e)
			{
				$errors[] = $e->getMessage();
				
				$this->_getSession()->setFeeFormData($data);
			}
		}
		
		$resultRedirect = $this->resultRedirectFactory->create();
		
		if ($errors) 
		{
			if ($fee_id) 
			{
				$resultRedirect->setPath
				(
					'fee/*/edit', ['id' => $fee_id, '_current' => true]
				);
			} 
			else 
			{
				$resultRedirect->setPath
				(
					'fee/*/create',['_current' => true]
				);
			}
		} 
		else 
		{
			$this->messageManager->addSuccessMessage('Fee saved successfully');
			
			if (null !== $back = $this->getRequest()->getParam('back'))
			{
				if ('edit' === $back)
				{
					$resultRedirect->setPath
					(
						'fee/*/edit', ['id' => $fee_id, '_current' => true]
					);
				}
				else 
				{
					$resultRedirect->setPath('fee/index');
				}
			}
			else 
			{
				$resultRedirect->setPath('fee/index');
			}
		}
		
		return $resultRedirect;
	}
	
	/**
	 * Retrieve current fee ID
	 *
	 * @return int
	 */
	private function getCurrentFeeId()
	{
		$data = $this->getRequest()->getPostValue('fee');
		
		return isset($data['fee_id']) ? $data['fee_id'] : null;
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
}