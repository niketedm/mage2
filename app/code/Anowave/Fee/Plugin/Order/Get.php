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

namespace Anowave\Fee\Plugin\Order;

use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class Get
{
    /**
     * Order feedback field name
     */
    const FIELD_NAME = 'fee';
    /**
     * Order Extension Attributes Factory
     *
     * @var OrderExtensionFactory
     */
    protected $extensionFactory;
    /**
     * OrderRepositoryPlugin constructor
     *
     * @param OrderExtensionFactory $extensionFactory
     */
    public function __construct(OrderExtensionFactory $extensionFactory)
    {
        $this->extensionFactory = $extensionFactory;
    }
    
    /**
     * Add "customer_feedback" extension attribute to order data object to make it accessible in API data
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $order
     *
     * @return OrderInterface
     */
    public function afterGet(OrderRepositoryInterface $subject, OrderInterface $order)
    {
        /**
         * Get fee
         * 
         * @var string $fee
         */
        $fee = $order->getData(self::FIELD_NAME);
        
        /**
         * Get extension attributes
         * 
         * @var \Magento\Sales\Api\Data\OrderExtension $extensionAttributes
         */
        $extensionAttributes = $order->getExtensionAttributes() ? $order->getExtensionAttributes() : $this->extensionFactory->create();
        
        /**
         * Set fee
         */
        $extensionAttributes->setFee($fee);
        
        $order->setExtensionAttributes($extensionAttributes);
        
        return $order;
    }
    /**
     * Add "customer_feedback" extension attribute to order data object to make it accessible in API data
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderSearchResultInterface $searchResult
     *
     * @return OrderSearchResultInterface
     */
    public function afterGetList(OrderRepositoryInterface $subject, OrderSearchResultInterface $searchResult)
    {
        $orders = $searchResult->getItems();
        
        foreach ($orders as &$order) 
        {
            /**
             * Get fee
             * 
             * @var string $fee
             */
            $fee = $order->getData(self::FIELD_NAME);
            
            /**
             * Get extension attributes
             *
             * @var \Magento\Sales\Api\Data\OrderExtension $extensionAttributes
             */
            $extensionAttributes = $order->getExtensionAttributes() ? $order->getExtensionAttributes() : $this->extensionFactory->create();
            
            /**
             * Set fee
             */
            $extensionAttributes->setFee($fee);
            
            /**
             * Save extension attributes
             */
            $order->setExtensionAttributes($extensionAttributes);
        }
        return $searchResult;
    }
}