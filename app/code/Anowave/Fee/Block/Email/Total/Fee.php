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
 
namespace Anowave\Fee\Block\Email\Total;

class Fee extends \Magento\Framework\View\Element\Template
{
    const KEY = 'fee';
    
    /**
     * Tax configuration model
     *
     * @var \Magento\Tax\Model\Config
     */
    protected $config;
    
    /**
     * @var Order
     */
    protected $order;
    
    /**
     * @var \Magento\Framework\DataObject
     */
    protected $source;
    
    /**
     * 
     * @var \Anowave\Fee\Helper\Data
     */
    protected $helper;
    
    
    /**
     * Constructor 
     * 
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param \Anowave\Fee\Helper\Data $helper
     * @param array $data
     */
    public function __construct
    (
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Tax\Model\Config $taxConfig,
        \Anowave\Fee\Helper\Data $helper,
        array $data = []
    ) 
    {
        $this->config = $taxConfig;
        
        /**
         * Set helper 
         * 
         * @var \Anowave\Fee\Helper\Data $helper
         */
        $this->helper = $helper;
        
        parent::__construct($context, $data);
    }
    
    
    /**
     * Get data (totals) source model
     *
     * @return \Magento\Framework\DataObject
     */
    public function getSource()
    {
        return $this->source;
    }
    
    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }
    
    /**
     * Initialize all order totals relates with tax
     *
     * @return \Magento\Tax\Block\Sales\Order\Tax
     */
    public function initTotals()
    {
        /**
         * Get parent block
         * 
         * @var unknown $parent
         */
        $parent = $this->getParentBlock();
        
        /**
         * Get order
         * 
         * @var \Anowave\Fee\Block\Email\Total\Fee $order
         */
        $this->order = $parent->getOrder();
        
        /**
         * Get source 
         * 
         * @var \Anowave\Fee\Block\Email\Total\Fee $source
         */
        $this->source = $parent->getSource();
        
        /**
         * Get store 
         * 
         * @var unknown $store
         */
        $store = $this->getStore();
        
        $order = $this->order->load($this->order->getId());
        
        /**
         * Get fee
         * 
         * @var float $fee
         */
        $fee = (float) $order->getData(static::KEY);
        
        file_put_contents('debug.txt', $fee);
        
        if ($fee)
        {
            $charges = new \Magento\Framework\DataObject
            (
                [
                    'code'      => static::KEY,
                    'strong'    => false,
                    'value'     => $fee,
                    'label'     => $this->helper->getFeeName(),
                ]
            );
            
            $parent->addTotal($charges, static::KEY);
            $parent->addTotal($charges, static::KEY);
        }
        
        return $this;
    }
}