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

namespace Anowave\Fee\Controller\Checkout;

class ApplyPaymentMethod extends \Magento\Framework\App\Action\Action
{
	/**
	 * @var \Magento\Framework\Controller\Result\ForwardFactory
	 */
	protected $resultForwardFactory;
	
	/**
	 * @var \Magento\Framework\View\LayoutFactory
	 */
	protected $layoutFactory;
	
	/**
	 * @var \Magento\Checkout\Model\Cart
	 */
	protected $cart;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
	 * @param \Magento\Framework\View\LayoutFactory $layoutFactory
	 * @param \Magento\Checkout\Model\Cart $cart
	 */
	public function __construct
	(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
		\Magento\Framework\View\LayoutFactory $layoutFactory,
		\Magento\Checkout\Model\Cart $cart
	) 
	{
		$this->resultForwardFactory = $resultForwardFactory;
		$this->layoutFactory = $layoutFactory;
		$this->cart = $cart;
			
		parent::__construct($context);
	}
	
	/**
	 * Execute 
	 * 
	 * @return \Magento\Framework\Controller\ResultInterface
	 */
	public function execute()
	{
		$method = $this->getRequest()->getParam('payment_method');
		
		$quote = $this->cart->getQuote();
		
		$quote->getPayment()->setMethod($method);
		
		$quote->setTotalsCollectedFlag(false);
		$quote->collectTotals();
		
		$quote->save();
	}
}