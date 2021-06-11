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

class DeleteButton extends GenericButton implements ButtonProviderInterface
{
	/**
	 * @return array
	 */
	public function getButtonData()
	{
		$data = [];
		
		if ($this->getId()) 
		{
			$data = 
			[
				'label' => __('Delete Fee'),
				'class' => 'delete',
				'on_click' => 'deleteConfirm(\''
				. __('Are you sure you want to delete this fee ?')
				. '\', \'' . $this->getDeleteUrl() . '\')',
				'sort_order' => 20,
			];
		}
		return $data;
	}
	
	/**
	 * @return string
	 */
	public function getDeleteUrl()
	{
		return $this->getUrl('*/*/delete', ['id' => $this->getId()]);
	}
}