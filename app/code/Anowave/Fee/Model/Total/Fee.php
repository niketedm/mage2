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

class Fee extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
	/**
	 * @var \Anowave\Fee\Helper\Data
	 */
	protected $helper = null;
	
	/**
	 * Collect grand total address amount
	 *
	 * @param \Magento\Quote\Model\Quote $quote
	 * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
	 * @param \Magento\Quote\Model\Quote\Address\Total $total
	 * @return $this
	 */
	protected $quoteValidator = null;
	
	/**
	 * @var \Magento\Framework\App\ProductMetadataInterface
	 */
	protected $productMetadata;
	
	/**
	 * @var \Magento\Tax\Model\Calculation
	 */
	protected $taxCaculation;
	
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
		$this->quoteValidator = $quoteValidator;
		
		/**
		 * Set helper
		 *
		 * @var \Anowave\Fee\Helper\Data $helper
		 */
		$this->helper = $helper;
		
		/**
		 * Set product meta data
		 *
		 * @var \Magento\Framework\App\ProductMetadataInterface $productMetadata
		 */
		$this->productMetadata = $productMetadata;
		
		/**
		 * Set tax caculation
		 *
		 * @var \Anowave\Fee\Model\Total\Fee $taxCaculation
		 */
		$this->taxCaculation = $taxCaculation;
	}
	
	/**
	 * Constructor
	 *
	 * @see \Magento\Quote\Model\Quote\Address\Total\AbstractTotal::collect()
	 */
	public function collect
	(
		\Magento\Quote\Model\Quote $quote,
		\Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
		\Magento\Quote\Model\Quote\Address\Total $total
		)
	{
		parent::collect($quote, $shippingAssignment, $total);
		
		if (!count($shippingAssignment->getItems()))
		{
			return $this;
		}
		
		if (!$this->helper->isActive())
		{
		    return $this;
		}
		
		/**
		 * Set fee
		 *
		 * @var float
		 */
		$fee = (float) $this->helper->getFee($quote, null);
		
		/**
		 * Set fee
		 */
		$total->setFee($fee);
		
		/**
		 * Set total amount
		 */
		$total->setTotalAmount('fee', $fee);
		$total->setBaseTotalAmount('fee', $fee);

		/**
		 * Update quote
		 */
		$quote->setFee($fee);
		
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
			'code'  => 'fee',
			'title' => $this->getLabel(),
			'value' => $this->helper->getFee($quote)
		];
	}
	
	/**
	 * Get Subtotal label
	 *
	 * @return \Magento\Framework\Phrase
	 */
	public function getLabel()
	{
		return $this->helper->getFeeName();
	}
}