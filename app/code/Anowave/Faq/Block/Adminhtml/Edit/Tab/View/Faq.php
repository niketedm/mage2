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
 
namespace Anowave\Faq\Block\Adminhtml\Edit\Tab\View;

class Faq extends \Magento\Backend\Block\Widget\Grid\Extended
{
	/**
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry = null;
	
	/**
	 * @var \Anowave\Faq\Model\ResourceModel\Item\CollectionFactory
	 */
	protected $_collectionFactory = null;
	
	/**
	 * @var \Magento\Framework\View\Element\BlockFactory
	 */
	protected $_blockFactory;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Backend\Block\Template\Context $context
	 * @param \Magento\Backend\Helper\Data $backendHelper
	 * @param \Anowave\Faq\Model\ResourceModel\Item\CollectionFactory $collectionFactory
	 * @param \Magento\Framework\Registry $coreRegistry
	 * @param \Magento\Framework\View\Element\BlockFactory $blockFactory
	 * @param array $data
	 */
    public function __construct
    (
    	\Magento\Backend\Block\Template\Context $context,
    	\Magento\Backend\Helper\Data $backendHelper,
    	\Anowave\Faq\Model\ResourceModel\Item\CollectionFactory $collectionFactory,
    	\Magento\Framework\Registry $coreRegistry,
    	\Magento\Framework\View\Element\BlockFactory $blockFactory,
    	array $data = []
    ) 
    {
        $this->_coreRegistry 		= $coreRegistry;
        $this->_collectionFactory 	= $collectionFactory;
        $this->_blockFactory 		= $blockFactory;
        
        parent::__construct($context, $backendHelper, $data);
    }
    
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
    	parent::_construct();
    	
    	$this->setId('faq_grid');
    	$this->setUseAjax(true);    
    }
    
    protected function _prepareCollection()
    {
    	$collection = $this->_collectionFactory->create()->addProduct($this->getProductId());

    	/**
    	 * Set collection
    	 */
    	$this->setCollection($collection);
    	
    	return $this;
    }
    
    /**
     * Prepare grid mass actions
     *
     * @return void
     */
    protected function _prepareMassaction()
    {
    	$this->setMassactionIdField('faq_id');

    	$this->getMassactionBlock()->setTemplate('Anowave_Faq::action.phtml');
    	$this->getMassactionBlock()->setUseAjax(true);
    	$this->getMassactionBlock()->setFormFieldName('faq');
    	
    	    	  
    	$this->getMassactionBlock()->addItem
    	(
    		'delete',
    		[
    			'label' 	=> __('Delete'),
    			'url' 		=> $this->getUrl
    			(
    				'faq/index/massDelete',['_current' => true]
    			),
    			'confirm' 	=> __('Are you sure?'),
    			'complete' 	=> 'faq_gridJsObject.reload()'
    		]
    	);
    }

    /**
     * Prepare columns.
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('faq',
        [
        	'header' 	=> __('Question'),
        	'index'		=> 'faq',
        	'type' 		=> 'text',
        	'width' 	=> '100px'
        ]);
        
        $this->addColumn('faq_content',
        [
        	'header' 	=> __('Content'),
        	'index'		=> 'faq_content',
        	'type' 		=> 'text'
        ]);
        
        $this->addColumn('faq_sort[]',
        [
        	'header' 	=> __('Sort'),
        	'index'		=> 'faq_sort',
        	'type' 		=> 'input',
        	'width' 	=> '100px'
        ]);
        

        return parent::_prepareColumns();
    }

    /**
     * Get headers visibility
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getHeadersVisibility()
    {
        return $this->getCollection()->getSize() >= 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getRowUrl($row)
    {
        return null;
    }
    
    /**
     * @return string
     */
    public function getGridUrl()
    {
    	return $this->getUrl('faq/index/faq', ['_current' => true]);
    }
}
