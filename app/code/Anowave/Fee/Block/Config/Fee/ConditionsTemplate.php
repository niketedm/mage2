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

namespace Anowave\Fee\Block\Config\Fee;

class ConditionsTemplate extends \Anowave\Fee\Block\Config\ConditionsTemplate
{
	public function getForm()
	{
		if (null !== $fee = $this->request->getParam('id'))
		{
			$collection = $this->conditionsCollectionFactory->create()->addFieldToFilter('rule_fee_id', $fee);
			
			if ($collection->getSize())
			{
				$model = $collection->getFirstItem();
			}
			else
			{
				$model = $this->conditionsFactory->create();
			}
		}
		else 
		{
			$model = $this->conditionsFactory->create();
		}

		/**
		 * Get form name
		 * 
		 * @var string $formName
		 */
		$formName = \Anowave\Fee\Helper\Data::FEE_CONDITIONS_FORM_NAME;
		
		/**
		 * Get conditions fieldset
		 * 
		 * @var Ambiguous $conditionsFieldSetId
		 */
		$conditionsFieldSetId = $model->getConditionsFieldSetId($formName);
		
		/** 
		 * @var \Magento\Framework\Data\Form $form 
		 */
		$form = $this->formFactory->create();

		/**
		 * Set prefix
		 */
		$form->setHtmlIdPrefix('rule_');
		
		/**
		 * Get renderer
		 * 
		 * @var Magento\Backend\Block\Widget\Form\Renderer\Fieldset $renderer
		 */
		$renderer = $this->rendererFieldset->setTemplate('Magento_CatalogRule::promo/fieldset.phtml')->setNewChildUrl($this->getUrl('sales_rule/promo_quote/newConditionHtml/form/', array('form_namespace' => $formName)))->setFieldSetId($conditionsFieldSetId);

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
				'name' 	=> 'conditions', 
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
}