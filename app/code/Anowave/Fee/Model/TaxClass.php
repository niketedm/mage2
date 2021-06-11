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

use Magento\Tax\Model\TaxClass\Source\Product as ProductTaxClassSource;

class TaxClass implements \Magento\Framework\Option\ArrayInterface
{
	/**
	 * @var ProductTaxClassSource
	 */
	protected $productTaxClassSource;
	
	/**
	 * Constructor 
	 * 
	 * @param unknown $productTaxClassSource
	 */
	public function __construct
	(
		ProductTaxClassSource $productTaxClassSource
	) 
	{	
		$this->productTaxClassSource = $productTaxClassSource;	
	}
	
	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		$options = [];
		
		foreach ($this->productTaxClassSource->getAllOptions() as $class)
		{
			$options[] = 
			[
				'value' => (int) $class['value'],
				'label' => $class['label']
			];
		}
		
		return $options;
	}
}