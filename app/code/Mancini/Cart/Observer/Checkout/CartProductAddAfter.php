<?php
/**
 * Copyright ©  All rights reserved.
 * Observer to save the protection to the quote item table
 */
declare(strict_types=1);

namespace Mancini\Cart\Observer\Checkout;

class CartProductAddAfter implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    public $request;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request
    ){
        $this->request = $request;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $post = $this->request->getPost();

        $item = $observer->getEvent()->getData('quote_item');			
        $item = ( $item->getParentItem() ? $item->getParentItem() : $item );
      
        if(isset($post['protectionprd'])){
            $item->setProtectionPlan($post['protectionprd']);
        } else{
            $item->setProtectionPlan(''); 
        }   

        $item->getProduct()->setIsSuperMode(true);
    }
}
