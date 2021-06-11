<?php
/**
 * Anowave Magento 2 FAQ
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
 * @package 	Anowave_Faq
 * @copyright 	Copyright (c) 2018 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */

namespace Anowave\Faq\Block;

class Button extends \Magento\Backend\Block\Template
{
	public function _construct()
	{
		parent::_construct();
		
		$this->setTemplate('renderer/button.phtml');
	}
	
	public function getTarget()
	{
		return $this->getUrl('faq/index/create') . '?isAjax=true';
	}
}