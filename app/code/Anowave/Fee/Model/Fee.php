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

class Fee extends \Magento\Framework\Model\AbstractModel
{
	/**
	 * Enabled fee
	 * 
	 * @var integer
	 */
	const STATUS_ENABLED = 1;
	
	/**
	 * Disabled fee
	 * 
	 * @var integer
	 */
	const STATUS_DISABLED = 0;
	
	protected function _construct()
	{
		$this->_init('Anowave\Fee\Model\ResourceModel\Fee');
	}
}