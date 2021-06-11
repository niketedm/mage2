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

namespace Anowave\Fee\Block\Config;

class Conditions extends \Magento\Config\Block\System\Config\Form\Field
{
	/**
	 * Block factory
	 *
	 * @var \Magento\Framework\View\Element\BlockFactory
	 */
	protected $blockFactory;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Framework\View\Element\BlockFactory $blockFactory
	 */
	public function __construct
	(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Framework\View\Element\BlockFactory $blockFactory,
		array $data = []
	)
	{
		parent::__construct($context);
		
		$this->blockFactory = $blockFactory;
	}
	
	protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
    	return $this->blockFactory->createBlock('Anowave\Fee\Block\Config\ConditionsTemplate')->toHtml();
    }
}