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

/**
 * Google Analytics module observer
 *
 */
class Attribute implements \Magento\Framework\Option\ArrayInterface
{
	const NONE = 'none'; 
	
	/**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory
     */
    protected $attributeCollectionFactory;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Framework\ObjectManagerInterface $interface
	 */
	public function __construct
	(
		\Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory $attributeCollectionFactory
	) 
	{
		$this->attributeCollectionFactory = $attributeCollectionFactory;
	}
	
	
	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		$attributes = 
		[
			[
				'value' => self::NONE,
				'label' => __('None')
			]
		];
		
		
		foreach ($this->attributeCollectionFactory->create() as $attribute)
		{
			$attributes[] = 
			[
				'value' => $attribute->getAttributeCode(),
				'label' => $attribute->getFrontendLabel()
			];
		}
		
		return $attributes;
	}
}