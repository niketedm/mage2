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

namespace Anowave\Fee\Observer\Invoice;

use Magento\Framework\Event\ObserverInterface;

class Save implements ObserverInterface
{
	/**
	 * Execute oberver 
	 * 
	 * @see \Magento\Framework\Event\ObserverInterface::execute()
	 */
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$fee = $observer->getEvent()->getInvoice()->getFee();
		
		if ($fee) 
		{
			$observer->getEvent()->getInvoice()->getOrder()->setFee($fee);
		}
		
		return $this;
	}
}