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

namespace Anowave\Fee\Block\Sales\Order;

class Fee extends \Magento\Framework\View\Element\Template
{
	/**
	 * Tax configuration model
	 *
	 * @var \Magento\Tax\Model\Config
	 */
	protected $_config;

	/**
	 * @var Order
	 */
	protected $_order;

	/**
	 * @var \Magento\Framework\DataObject
	 */
	protected $_source;
	
	/**
	 * @var \Anowave\Fee\Helper\Data
	 */
	protected $helper = null;

	/**
	 * @param \Magento\Framework\View\Element\Template\Context $context
	 * @param \Magento\Tax\Model\Config $taxConfig
	 * @param array $data
	 */
	public function __construct
	(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Tax\Model\Config $taxConfig,
		\Anowave\Fee\Helper\Data $helper,
		array $data = []
	) 
	{
		$this->_config = $taxConfig;
		
		$this->helper = $helper;
		
		parent::__construct($context, $data);
	}

	/**
	 * Check if we nedd display full tax total info
	 *
	 * @return bool
	 */
	public function displayFullSummary()
	{
		return true;
	}

	/**
	 * Get data (totals) source model
	 *
	 * @return \Magento\Framework\DataObject
	 */
	public function getSource()
	{
		return $this->_source;
	}
	
	public function getStore()
	{
		return $this->_order->getStore();
	}

	/**
	 * @return Order
	 */
	public function getOrder()
	{
		return $this->_order;
	}

	/**
	 * @return array
	 */
	public function getLabelProperties()
	{
		return $this->getParentBlock()->getLabelProperties();
	}

	/**
	 * @return array
	 */
	public function getValueProperties()
	{
		return $this->getParentBlock()->getValueProperties();
	}

	/**
	 * Initialize all order totals relates with tax
	 *
	 * @return \Magento\Tax\Block\Sales\Order\Tax
	 */
	public function initTotals()
	{
		$parent = $this->getParentBlock();
		
		$this->_order 	= $parent->getOrder();
		$this->_source 	= $parent->getSource();

		$store = $this->getStore();

		if ($this->helper->isActive())
		{
    		$fee = new \Magento\Framework\DataObject
    		(
    			[
    				'code' 		=> 'fee',
    				'strong' 	=> false,
    				'value' 	=> $this->helper->getFee($this->_order->getQuote()),
    				'label' 	=> $this->helper->getFeeName()
    			]
    		);
    
    		$parent->addTotal($fee, 'fee');
		}
		
		return $this;
	}
}