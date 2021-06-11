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
 
namespace Anowave\Faq\Block\Form\Renderer;

use Magento\Backend\Block\Widget;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Backend\Block\Template;


class Submit extends Widget implements RendererInterface
{
	public function __construct
	(
		\Magento\Backend\Block\Template\Context $context, array $data = []
	)
	{
		parent::_construct($context);
	}
    /**
     * Render form element as HTML
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
    	return $this->getLayout()->createBlock('Anowave\Faq\Block\Button')->toHtml();
    }
}
