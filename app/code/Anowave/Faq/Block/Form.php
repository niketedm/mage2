<?php
/**
 * Anowave Magento 2 FAQ
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
 * @package 	Anowave_Faq
 * @copyright 	Copyright (c) 2018 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */

namespace Anowave\Faq\Block;

/**
 * Adminhtml customer groups edit form
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
	protected $locale = null;

	/**
	 * @var \Magento\Framework\View\Element\BlockFactory
	 */
	protected $_blockFactory;
	
	/**
	 * @var \Magento\Cms\Model\Wysiwyg\Config
	 */
	protected $_wysiwygConfig;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Backend\Block\Template\Context $context
	 * @param \Magento\Framework\Registry $registry
	 * @param \Magento\Framework\Data\FormFactory $formFactory
	 * @param \Magento\Framework\View\Element\BlockFactory $blockFactory
	 * @param array $data
	 */
    public function __construct
    (
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
    	\Magento\Framework\View\Element\BlockFactory $blockFactory,
    	\Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) 
    {  
        parent::__construct($context, $registry, $formFactory, $data);
        
        $this->locale 			= $context->getLocaleDate();
        $this->_blockFactory 	= $blockFactory;
        $this->_wysiwygConfig 	= $wysiwygConfig;
        
        $this->setProductId
        (
        	$this->getRequest()->getParam('id')
        );
    }

    /**
     * Prepare form for render
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset('base_fieldset', 
        [
        	'legend' => __('Add FAQ')
        ]);        
        
        $fieldset->addField
        (
        	'faq_grid_id', 'hidden',
        	[
        		'name' 		=> 'faq_grid_id',
        		'value'		=> $this->grid->getId()
        	]
        );
        
        $fieldset->addField
        (
            'faq_product_id', 'hidden',
            [
                'name' 		=> 'faq_product_id',
            	'value'		=> $this->getProductId(),
                'required' 	=> true
            ]
        );

        $fieldset->addField
        (
        	'faq', 'text',
        	[
        		'name' 		=> 'faq',
        		'label' 	=> __('Question'),
        		'title' 	=> __('Question'),
        		'note' 		=> __('Enter FAQ question'),
        		'required' 	=> false
        	]
        );
        
        $fieldset->addField
        (
        	'faq_content', 'editor',
        	[
        		'name' 		=> 'faq_content',
        		'label' 	=> __('Answer'),
        		'title'		=> __('Answer'),
        		'note' 		=> __('Enter FAQ answer content'),
        		'config' 	=> $this->_wysiwygConfig->getConfig(),
        		'required' 	=> false
        	]
        );
        
        $submit = $fieldset->addField
        (
        	'submitter', 'text',
        	[
        		'name' => 'submitter'
        	]
        );
        
        $submit->setRenderer
        (
        	$this->getLayout()->createBlock('Anowave\Faq\Block\Form\Renderer\Submit')
        );
        
        $form->setUseContainer(false);
        $form->setMethod('post');
           
        $this->setForm($form);
    }
}