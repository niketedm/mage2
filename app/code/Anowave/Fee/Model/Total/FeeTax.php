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

namespace Anowave\Fee\Model\Total;

class FeeTax extends Fee
{
	/**
	 * Constructor
	 *
	 * @param \Magento\Quote\Model\QuoteValidator $quoteValidator
	 * @param \Anowave\Fee\Helper\Data $helper
	 * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
	 * @param array $data
	 */
	public function __construct
	(
		\Magento\Quote\Model\QuoteValidator $quoteValidator,
		\Anowave\Fee\Helper\Data $helper,
		\Magento\Framework\App\ProductMetadataInterface $productMetadata,
		\Magento\Tax\Model\Calculation $taxCaculation,
		array $data = []
		)
	{
		parent::__construct($quoteValidator, $helper, $productMetadata, $taxCaculation);
	}
	
	/**
	 * Constructor
	 *
	 * {@inheritDoc}
	 * @see \Anowave\Fee\Model\Total\Fee::collect()
	 */
	public function collect
	(
		\Magento\Quote\Model\Quote $quote,
		\Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
		\Magento\Quote\Model\Quote\Address\Total $total
		)
	{
		\Magento\Quote\Model\Quote\Address\Total\AbstractTotal::collect($quote, $shippingAssignment, $total);
		
		if (!count($shippingAssignment->getItems()))
		{
			return $this;
		}
		
		if (!$this->helper->isActive())
		{
		    return $this;
		}
		
		/**
		 * Get fee tax
		 *
		 * @var float $tax
		 */
		$tax = (float) $this->helper->getFeeTax($quote, null);
		
		if ($tax >= 0)
		{
			/**
			 * Set fee
			 */
			$total->setFeeTax($tax);
			
			/**
			 * Set total tax amount
			 */
			$total->setTotalAmount('fee_tax', $tax);
			$total->setBaseTotalAmount('fee_tax', $tax);
			
			/**
			 * Set quote fee
			 */
			$quote->setFeeTax($tax);
		}
		
		return $this;
	}
	
	/**
	 * Assign subtotal amount and label to address object
	 *
	 * @param \Magento\Quote\Model\Quote $quote
	 * @param Address\Total $total
	 * @return array
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
	{
		return
		[
			'code' 	=> 'fee_tax',
			'title' => $this->getLabel(),
			'value' => $this->helper->getFeeTax($quote)
		];
	}
	
	/**
	 * Get Subtotal label
	 *
	 * @return \Magento\Framework\Phrase
	 */
	public function getLabel()
	{
		return __("Fee tax");
	}
}