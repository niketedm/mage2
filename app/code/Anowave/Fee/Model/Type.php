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

namespace Anowave\Fee\Model;

class Type implements \Magento\Framework\Option\ArrayInterface
{
	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		return 
		[
			[
				'value' => \Anowave\Fee\Helper\Data::FEE_TYPE_FIXED, 
				'label' => __('Fixed fee')
			], 
			[
				'value' => \Anowave\Fee\Helper\Data::FEE_TYPE_PERCENTAGE, 
				'label' => __('Percentage of order subtotal')
			],
			[
				'value' => \Anowave\Fee\Helper\Data::FEE_TYPE_PERCENTAGE_PRODUCT,
				'label' => __('Percentage of item subtotal (applicable for calculation per product only)')
			],
			[
				'value' => \Anowave\Fee\Helper\Data::FEE_TYPE_FIXED_PRODUCT,
				'label' => __('Fixed amount of item subtotal (applicable for calculation per product only)')
			],
			[
				'value' => \Anowave\Fee\Helper\Data::FEE_TYPE_FIXED_CATEGORY,
				'label' => __('Fixed amount from category fee (applicable for calculation per product only)')
			],
			[
				'value' => \Anowave\Fee\Helper\Data::FEE_TYPE_PERCENTAGE_CATEGORY,
				'label' => __('Percentage amount from category fee (applicable for calculation per product only)')
			],
			[
				'value' => \Anowave\Fee\Helper\Data::FEE_TYPE_ONCE_PER_CATEGORY,
				'label' => __('Once (fixed amount) per set of products within the same category')
			],
			[
				'value' => \Anowave\Fee\Helper\Data::FEE_TYPE_ONCE_PER_CATEGORY_P,
				'label' => __('Once (percentage amount) per accumulative single item price from products within the same category')
			],
			[
				'value' => \Anowave\Fee\Helper\Data::FEE_TYPE_ONCE_PER_CATEGORY_PQ,
				'label' => __('Once (percentage amount) per accumulative subtotal from products within the same category')
			]
		];
	}
	
	/**
	 * Get options in "key-value" format
	 *
	 * @return array
	 */
	public function toArray()
	{
		return 
		[
			'F' => __('Fixed fee'), 
			'P' => __('Percentage of order grand total')
		];
	}
}