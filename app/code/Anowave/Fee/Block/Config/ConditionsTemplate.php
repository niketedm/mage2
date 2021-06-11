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

namespace Anowave\Fee\Block\Config;

class ConditionsTemplate extends \Magento\Backend\Block\Template
{
	/**
	 * @var \Magento\Framework\Registry
	 */
	protected $registry;
	
	/**
	 * Renderer fieldset
	 *
	 * @var \Magento\Backend\Block\Widget\Form\Renderer\Fieldset
	 */
	protected $rendererFieldset;
	
	/**
	 * @var \Magento\Rule\Block\Conditions
	 */
	protected $conditions;
	
	/**
	 * @var \Anowave\Fee\Model\ResourceModel\Conditions\CollectionFactory
	 */
	protected $conditionsCollectionFactory;
	
	/**
	 * @var \Anowave\Fee\Model\ConditionsFactory
	 */
	protected $conditionsFactory;
	
	/**
	 * @var \Magento\Framework\Data\FormFactory
	 */
	protected $formFactory = null;
	
	/**
	 * @var Magento\Framework\App\Request\Http
	 */
	protected $request;
	
	/**
	 * @var \Anowave\Fee\Helper\Data
	 */
	protected $helper = null;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Backend\Block\Template\Context $context
	 * @param \Magento\Framework\Registry $registry
	 * @param \Magento\Framework\Data\FormFactory $formFactory
	 * @param \Magento\Rule\Block\Conditions $conditions
	 * @param \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset
	 * @param \Anowave\Fee\Model\ResourceModel\Conditions\CollectionFactory $conditionsCollectionFactory
	 * @param \Anowave\Fee\Model\ConditionsFactory $conditionsFactory
	 * @param array $data
	 */
	public function __construct
	(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\Data\FormFactory $formFactory,
		\Magento\Rule\Block\Conditions $conditions,
		\Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset,
		\Anowave\Fee\Model\ResourceModel\Conditions\CollectionFactory $conditionsCollectionFactory,
		\Anowave\Fee\Model\ConditionsFactory $conditionsFactory,
		\Anowave\Fee\Helper\Data $helper,
		array $data = []
	)
	{
		parent::__construct($context);
		
		/**
		 * Set registry 
		 * 
		 * @var \Magento\Framework\Registry $registry
		 */
		$this->registry = $registry;
		
		/**
		 * Set fieldset renderer
		 * 
		 * @var \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset
		 */
		$this->rendererFieldset = $rendererFieldset;
		
		/**
		 * Set conditions
		 * 
		 * @var \Magento\Rule\Block\Conditions $conditions
		 */
		$this->conditions = $conditions;
		
		/**
		 * Set conditions collection factory 
		 * 
		 * @var \Anowave\Fee\Model\ResourceModel\Conditions\CollectionFactory $conditionsCollectionFactory
		 */
		$this->conditionsCollectionFactory = $conditionsCollectionFactory;
		
		/**
		 * Set conditions factory 
		 * 
		 * @var \Anowave\Fee\Model\ConditionsFactory $conditionsFactory
		 */
		$this->conditionsFactory = $conditionsFactory;
		
		/**
		 * Set helper 
		 * 
		 * @var \Anowave\Fee\Helper\Data $helper
		 */
		$this->helper = $helper;
		
		/**
		 * Set form factgory 
		 * 
		 * @var \Magento\Framework\Data\FormFactory
		 */
		$this->formFactory = $formFactory;
		
		/**
		 * Set request 
		 * 
		 * @var \Magento\Framework\App\Request\Http $request
		 */
		$this->request = $context->getRequest();
	}
	
	public function _construct()
	{
		parent::_construct();
		
		$this->setTemplate('conditions.phtml');
	}
	
	/**
	 * Get form 
	 * 
	 * @return \Magento\Framework\Data\Form
	 */
	public function getForm()
	{
		$collection = $this->conditionsCollectionFactory
						   ->create()
		                   ->addFieldToFilter('rule_default', \Anowave\Fee\Observer\Config::DEFAULT_CONDITION_VALUE)
		                   ->addFieldToFilter('rule_fee_store_id', $this->helper->getCurrentScopeStoreId());
		
		if ($collection->getSize())
		{
			$model = $collection->getFirstItem();
		}
		else 
		{
			$model = $this->conditionsFactory->create();
		}

		$formName = 'sales_rule_form';
		
		/**
		 * Get conditions fieldset
		 * 
		 * @var Ambiguous $conditionsFieldSetId
		 */
		$conditionsFieldSetId = $model->getConditionsFieldSetId($formName);
		

		/** @var \Magento\Framework\Data\Form $form */
		$form = $this->formFactory->create();

		$form->setHtmlIdPrefix('rule_');
		
		/**
		 * Get renderer
		 * 
		 * @var Magento\Backend\Block\Widget\Form\Renderer\Fieldset $renderer
		 */
		$renderer = $this->rendererFieldset->setTemplate('Magento_CatalogRule::promo/fieldset.phtml')->setNewChildUrl($this->getUrl('sales_rule/promo_quote/newConditionHtml/form/rule_conditions_fieldset', array('form_namespace' => $formName)))->setFieldSetId($conditionsFieldSetId);
		
		/**
		 * Create fieldset
		 * 
		 * @var \Magento\Framework\Data\Form\Element\Fieldset $fieldset
		 */
		$fieldset = $form->addFieldset('conditions_fieldset',['legend' => __('Apply the rule only if the following conditions are met (leave blank for all products)')])->setRenderer($renderer);
		
		$fieldset->addField
		(
			'conditions',
			'text',
			[
				'name' 	=>    'conditions', 
				'label' => __('Conditions'), 
				'title' => __('Conditions')	
			]
		)->setRule($model)->setRenderer($this->conditions);
	
		/**
		 * Set form values
		 */
		$form->setValues($model->getData());

		/**
		 * Set form name
		 */
		$this->setConditionFormName($model->getConditions(), $formName, 'rule_conditions_fieldset');

		return $form;
	}
	
	/**
	 * Set conditions form name
	 *
	 * @param \Magento\Rule\Model\Condition\AbstractCondition $conditions
	 * @param string $formName
	 */
	protected function setConditionFormName(\Magento\Rule\Model\Condition\AbstractCondition $conditions, $formName, $jsFormName)
	{
		$conditions->setFormName($formName);
		$conditions->setJsFormObject($jsFormName);
		
		if ($conditions->getConditions() && is_array($conditions->getConditions()))
		{
			foreach ($conditions->getConditions() as $condition)
			{
				$this->setConditionFormName($condition, $formName, $jsFormName);
			}
		}
	}
}