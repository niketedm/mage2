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

class Plugin extends \Magento\Backend\Block\Template
{
	/**
	 * Block output modifier 
	 * 
	 * @param \Magento\Framework\View\Element\Template $block
	 * @param string $html
	 */
	public function afterToHtml($block, $content) 
	{
		if (!$this->getRequest()->getParam('ajax'))
		{
			$content .= $block->getLayout()->createBlock('Anowave\Faq\Block\Form')->toHtml();
		}
		
		return $content;
	}
}