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

namespace Anowave\Fee\Block\Adminhtml\Fee\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SaveButton extends GenericButton implements ButtonProviderInterface
{
	public function getButtonData()
	{
		return 
		[
			'label' => __('Save'),
			'class' => 'save primary',
			'data_attribute' => 
			[
				'mage-init' => 
				[
					'button' => 
					[
						'event' => 'save'
					]
				],
				'form-role' => 'save',
			],
			'sort_order' => 90,
		];
	}
}