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

use Magento\Checkout\Model\Session as CheckoutSession;

class Tax implements ObserverInterface
{
	/** 
	 * @var CheckoutSession 
	 */
	protected $checkoutSession;
	
	/**
	 * @var \Anowave\Fee\Helper\Data
	 */
	protected $helper;
	
	/**
	 * Constructor 
	 * 
	 * @param CheckoutSession $checkoutSession
	 * @param \Anowave\Fee\Helper\Data $helper
	 */
	public function __construct
	(
		CheckoutSession $checkoutSession,
		\Anowave\Fee\Helper\Data $helper
	) 
	{
		$this->checkoutSession = $checkoutSession;
		
		/**
		 * Set helper 
		 * 
		 * @var \Anowave\Fee\Helper\Data $helper
		 */
		$this->helper = $helper;
	}
	
	
	/**
	 * Execute oberver 
	 * 
	 * @see \Magento\Framework\Event\ObserverInterface::execute()
	 */
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$tax = (float) $this->helper->getFeeTax
		(
		    $observer->getQuote()
		);
		
		/** 
		 * @var \Magento\Quote\Model\Quote\Address\Total 
		 **/
		$total = $observer->getData('total');
		
		/**
		 * Get applied taxes 
		 * 
		 * @var array $taxes
		 */
		$taxes = is_null($total->getAppliedTaxes()) ? [] : $total->getAppliedTaxes();
		
		if (count($taxes) > 0 && $tax >= 0) 
		{
			/**
			 * Add tax
			 */
			$total->addTotalAmount('tax', $tax);
			
			/**
			 * Add base total
			 */
			$total->addBaseTotalAmount('tax', $tax);
		}
		
		return $this;
	}
}