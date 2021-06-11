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
 
namespace Anowave\Fee\Block;

class Totals
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
	 * @param unknown $helper
	 * @param unknown $quoteRepository
	 */
	public function __construct
	(
		\Anowave\Fee\Helper\Data $helper,
		\Magento\Quote\Api\CartRepositoryInterface $quoteRepository
	)
	{
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
	
	/**
	 * Before Get Total
	 * 
	 * @param mixed $block
	 */
	public function beforeGetTotals($block)
	{
	    if ($this->helper->isActive())
	    {
    		$block->addTotal(new \Magento\Framework\DataObject
    		(
                [
                    'code' 			=> 'fee',
                	'value' 		=> $this->getFee
                	(
                		$block->getSource()
                	),
                    'label' 		=> $this->helper->getFeeName(),
                	'strong'		=> false,
                    'area' 			=> 'footer'
                ]
            ));
	    }
	}
	
	/**
	 * Get fee from order 
	 * 
	 * @param \Magento\Sales\Model\Order $order
	 * @return float
	 */
	private function getFee(\Magento\Framework\Api\ExtensibleDataInterface $order)
	{	
		/**
		 * Get fee from order 
		 * 
		 * @var float $fee
		 */
		$fee = (float) $order->getFee();
		
		/**
		 * Try to load fee from quote (backwards compatibility)
		 */
		if (!$fee)
		{
			if ($order->getQuoteId())
			{
				try 
				{
					$quote = $this->quoteRepository->get
					(
						$order->getQuoteId()
					);
					
					if ($quote->getId())
					{
						$fee = $quote->getFee();
					}
				}
				catch (\Exception $e){}
			}
		}
		
		return $fee;
	}
}