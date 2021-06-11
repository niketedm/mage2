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

namespace Anowave\Fee\Model\Sales\Pdf;

use Magento\Sales\Model\Order\Pdf\Total\DefaultTotal;

class Fee extends \Magento\Sales\Model\Order\Pdf\Total\DefaultTotal
{
	/**
	 * @var \Anowave\Fee\Helper\Data
	 */
	protected $helper;
	
	/**
	 * @var \Magento\Quote\Api\CartRepositoryInterface
	 */
	protected $quoteRepository;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Tax\Helper\Data $taxHelper
	 * @param \Magento\Tax\Model\Calculation $taxCalculation
	 * @param \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory $ordersFactory
	 * @param \Anowave\Fee\Helper\Data $helper
	 * @param array $data
	 */
	public function __construct
	(
		\Magento\Tax\Helper\Data $taxHelper,
		\Magento\Tax\Model\Calculation $taxCalculation,
		\Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory $ordersFactory,
		\Anowave\Fee\Helper\Data $helper,
		\Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
		array $data = []
	) 
	{
		parent::__construct($taxHelper, $taxCalculation, $ordersFactory);
		
		/**
		 * Set helper 
		 * 
		 * @var \Anowave\Fee\Helper\Data $helper
		 */
		$this->helper = $helper;
		
		/**
		 * Set quote repository
		 *
		 * @var \Anowave\Fee\Block\Totals $quoteRepository
		 */
		$this->quoteRepository = $quoteRepository;
	}
	
	public function getTotalsForDisplay()
	{
		try 
		{
			$fee = $this->getAmount();
			
			/**
			 * Fallback
			 */
			if (!$fee && $this->getOrder()->getQuoteId())
			{
				$quote = $this->quoteRepository->get
				(
					$this->getOrder()->getQuoteId()
				);
				
				if ($quote->getId())
				{
					$fee = $quote->getFee();
				}
			}
		}
		catch (\Exception $e){}
		
		$amount = $this->getOrder()->formatPriceTxt($fee);
		
		if ($this->getAmountPrefix()) 
		{
			$amount = $this->getAmountPrefix() . $amount;
		}
		
		$title = $this->helper->getFeeName();
		
		if ($this->getTitleSourceField()) 
		{
			$label = $title . ' (' . $this->getTitleDescription() . '):';
		} 
		else 
		{
			$label = $title . ':';
		}
		
		$fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
		
		return 
		[
			[
				'amount' => $amount, 
				'label' => $label, 
				'font_size' => $fontSize
			]
		];
	}
}