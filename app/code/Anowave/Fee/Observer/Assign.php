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

use Magento\Framework\Event\ObserverInterface;

class Assign implements ObserverInterface
{
	/**
	 * Execute oberver 
	 * 
	 * @see \Magento\Framework\Event\ObserverInterface::execute()
	 */
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		/**
		 * Fee
		 * 
		 * @var float $fee
		 */
		$fee = (float) $observer->getQuote()->getFee();
		
		if ($fee > 0)
		{
			$observer->getOrder()->setFee($fee);
		}
		
		/**
		 * Fee tax
		 * 
		 * @var float $fee_tax
		 */
		$fee_tax = (float) $observer->getQuote()->getFeeTax();
		
		if ($fee_tax > 0)
		{
			$observer->getOrder()->setFeeTax($fee);
		}
	}
}