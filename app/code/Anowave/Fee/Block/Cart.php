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

class Cart extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Anowave\Fee\Helper\Data
     */
    protected $helper;
    
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;
    
    /**
     * 
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;
    
    /**
     * Constructor 
     * 
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Anowave\Fee\Helper\Data $helper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct
    (
        \Magento\Framework\View\Element\Template\Context $context,
        \Anowave\Fee\Helper\Data $helper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) 
    {
        parent::__construct($context, $data);
        
        /**
         * Set helper 
         * 
         * @var \Anowave\Fee\Helper\Data $helper
         */
        $this->helper = $helper;
        
        /**
         * Set checkout session 
         * 
         * @var \Magento\Checkout\Model\Session $checkoutSession
         */
        $this->checkoutSession = $checkoutSession;
        
        /**
         * Set price currency 
         * 
         * @var \Anowave\Fee\Block\Cart $priceCurrency
         */
        $this->priceCurrency = $priceCurrency;
    }
    
    /**
     * Get helper
     * 
     * @return \Anowave\Fee\Helper\Data
     */
    public function getHelper()
    {
        return $this->helper;
    }
    
    /**
     * Current fee
     * 
     * @return number[]|mixed[]|NULL
     */
    public function getCurrentFee()
    {
        $quote = $this->checkoutSession->getQuote();
        
        if ($quote)
        {
            return 
            [
                'fee'     => $this->priceCurrency->convertAndFormat($this->helper->getFee($quote)),
                'fee_tax' => $this->priceCurrency->convertAndFormat($this->helper->getFeeTax($quote))
            ];
        }

        return null;
    }
}