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

class BackButton extends GenericButton implements ButtonProviderInterface
{
	/**
	 * @return array
	 */
	public function getButtonData()
	{
		return 
		[
			'label' => __('Back'),
			'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()),
			'class' => 'back',
			'sort_order' => 10
		];
	}
	
	/**
	 * Get URL for back (reset) button
	 *
	 * @return string
	 */
	public function getBackUrl()
	{
		return $this->getUrl('*/*/');
	}
}