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

class Update
{
	/**
	 * @var \Magento\Checkout\Model\Session
	 */
	protected $checkoutSession;
	
	/**
	 * @var \Magento\Framework\Registry
	 */
	protected $registry;
	
	/**
	 * Paypal methods 
	 * 
	 * @var array
	 */
	protected $methods = 
	[
	    'payflowpro',
	    'payflow_link',
	    'payflow_advanced',
	    'braintree_paypal',
	    'paypal_express_bml',
	    'payflow_express_bml',
	    'payflow_express',
	    'paypal_express'
	];
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Checkout\Model\Session $checkoutSession
	 * @param \Magento\Framework\Registry $registry
	 */
	public function __construct
	(
		\Magento\Checkout\Model\Session $checkoutSession,
		\Magento\Framework\Registry $registry
	)
	{
		/**
		 * Set checkout session
		 *
		 * @var \Magento\Checkout\Model\Session $checkoutSession
		 */
		$this->checkoutSession = $checkoutSession;
		
		/**
		 * Set registry
		 *
		 * @var \Magento\Framework\Registry $registry
		 */
		$this->registry = $registry;
	}
	
	/**
	 * Get shipping, tax, subtotal and discount amounts all together
	 *
	 * @return array
	 */
	public function afterGetAmounts(\Magento\Paypal\Model\Cart $cart, $total)
	{
		$loop = $this->registry->registry('loop') ? true : false;
		
		if (!$loop)
		{
			$this->registry->register('loop', true);
			
			/**
			 * Get quote
			 *
			 * @var \Magento\Quote\Model\Quote $quote
			 */
			$quote = $this->checkoutSession->getQuote();
			
			if($quote && $quote->getId() && in_array($quote->getPayment()->getMethod(), $this->methods))
			{
			    /**
			     * Update subtotal
			     */
			    if (isset($total['subtotal']))
			    {
			        $total['subtotal'] += (float) $quote->getFee();
			    }
			    
			    /**
			     * Update tax
			     */
			    if(isset($total['tax']))
			    {
                    $total['tax'] += (float) $quote->getFeeTax();
			    }
			}
		}
		
		return $total;
	}
}