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

namespace Anowave\Fee\Model\Fee\Source;

class Status implements \Magento\Framework\Data\OptionSourceInterface
{
	/**
	 * Get options
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		$options[] = ['label' => '', 'value' => ''];
		
		$availableOptions = $this->getOptionArray();
		
		foreach ($availableOptions as $key => $value) 
		{
			$options[] = 
			[
				'label' => $value,
				'value' => $key,
			];
		}
		return $options;
	}
	
	public static function getOptionArray()
	{
		return 
		[
			1 => __('Enabled'), 
			0 => __('Disabled')
		];
	}
}