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

namespace Anowave\Fee\Plugin;

class Express
{
	/**
	 * @var \Anowave\Fee\Helper\Data
	 */
	protected $helper;
	
	/**
	 * Cosntructor
	 *
	 * @param \Magento\Quote\Model\Quote $quote
	 * @param \Magento\Checkout\Model\Session $checkoutSession
	 * @param \Magento\Framework\Registry $registry
	 * @param \Anowave\Fee\Helper\Data $helper
	 */
	public function __construct
	(
		\Anowave\Fee\Helper\Data $helper
	)
	{
		/**
		 * Set helper
		 *
		 * @var \Anowave\Fee\Helper\Data $helper
		 */
		$this->helper = $helper;
	}
	
	/**
	 * Get shipping, tax, subtotal and discount amounts all together
	 *
	 * @return array
	 */
	public function beforeCapture(\Magento\PayPal\Model\Express $subject, \Magento\Payment\Model\InfoInterface $payment, $amount)
	{
		try 
		{
			$order = $payment->getOrder();
		
			if($amount != $order->getBaseGrandTotal()) 
			{ 
				$amount = $order->getBaseGrandTotal(); 
			}
		}
		catch (\Exception $e){}
		
		return [$payment, $amount];
	}	
}