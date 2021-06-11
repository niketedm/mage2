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

class Address
{
	/**
	 * After load attribute options
	 * 
	 * @param \Magento\SalesRule\Model\Rule\Condition\Address $context
	 * @return \Magento\SalesRule\Model\Rule\Condition\Address
	 */
	public function afterLoadAttributeOptions(\Magento\SalesRule\Model\Rule\Condition\Address $context)
	{
		$attributes = $context->getAttributeOption();

		if (!array_key_exists('payment_method', $attributes))
		{
			$attributes['payment_method'] = __('Payment method');
			
			$context->setAttributeOption($attributes);
		}
		
		return $context;
	}
}