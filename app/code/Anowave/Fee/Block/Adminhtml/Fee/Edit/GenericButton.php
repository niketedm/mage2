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

/**
 * Class GenericButton
 */
class GenericButton
{
	/**
	 * Url Builder
	 *
	 * @var \Magento\Framework\UrlInterface
	 */
	protected $urlBuilder;
	
	/**
	 * Registry
	 *
	 * @var \Magento\Framework\Registry
	 */
	protected $registry;
	
	/**
	 * Constructor
	 *
	 * @param \Magento\Backend\Block\Widget\Context $context
	 * @param \Magento\Framework\Registry $registry
	 */
	public function __construct
	(
		\Magento\Backend\Block\Widget\Context $context,
		\Magento\Framework\Registry $registry
	) 
	{
		$this->urlBuilder = $context->getUrlBuilder();
		$this->registry = $registry;
	}
	
	/**
	 * Return the synonyms group Id.
	 *
	 * @return int|null
	 */
	public function getId()
	{
		$fee = $this->registry->registry('fee');
		
		return $fee ? $fee->getId() : null;
	}
	
	/**
	 * Generate url by route and parameters
	 *
	 * @param   string $route
	 * @param   array $params
	 * @return  string
	 */
	public function getUrl($route = '', $params = [])
	{
		return $this->urlBuilder->getUrl($route, $params);
	}
}