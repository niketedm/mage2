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

namespace Anowave\Fee\Model\Total\Creditmemo;

class FeeTax extends \Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal
{
	/**
	 * @var \Anowave\Fee\Helper\Data
	 */
	protected $helper = null;

	/**
	 * Constructor 
	 * 
	 * @param \Anowave\Fee\Helper\Data $helper
	 * @param array $data
	 */
	public function __construct
	(
		\Anowave\Fee\Helper\Data $helper,
		array $data = []
	)
	{
		$this->helper			= $helper;
	}
	
	public function collect
	(
		\Magento\Sales\Model\Order\Creditmemo $creditmemo
	) 
	{
		$store = $creditmemo->getStore();
        $order = $creditmemo->getOrder();
        
        /**
         * Get Order Fee
         * 
         * @var float
         */
        $fee = $order->getFee();
        
        /**
         * Set invoice fee
         */
        $creditmemo->setFee($order->getFee());
        
        /**
         * Update invoice totals
         */
        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $fee);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $fee);

		return $this;
	}
}