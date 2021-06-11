<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mancini\Cart\Controller\Cart;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;

use Magento\Catalog\Controller\Product\View\ViewInterface;
use Magento\Checkout\Model\Cart as CustomerCart;


/**
 * Action Delete.
 *
 * Deletes item from cart.
 */
class Delete extends \Magento\Checkout\Controller\Cart implements HttpPostActionInterface
{
    /**
     * Delete shopping cart item action
     * @return \Magento\Framework\Controller\Result\Redirect
     */

    /**
     * @var  \Magento\Checkout\Helper\Cart
     */
    protected $cartHelper;

    /**
     * @var \Magento\Quote\Model\Quote\ItemFactory
     */
    protected $itemFactory;


    public function __construct(        
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Quote\Model\Quote\ItemFactory $itemFactory,
        \Magento\Checkout\Helper\Cart $cartHelper  
    ) {
            $this->itemFactory  =   $itemFactory;
            $this->cartHelper   =   $cartHelper;
            parent::__construct($context, $scopeConfig,$checkoutSession,$storeManager,$formKeyValidator,$cart);
      }


    public function execute()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $id         =   (int)$this->getRequest()->getParam('id');
        $quoteItem  =   $this->itemFactory->create()->load($id);

        if($quoteItem->getProtectionPlan()){
           $protectionPlanId   =   "";
           $protectionPlanId   =   $quoteItem->getProtectionPlan();
           $items              =   $this->cartHelper->getCart()->getItems();

            foreach ($items as $item) {
                if ($item->getProduct()->getId() == $protectionPlanId ) {
                    $protectionItemId = $item->getItemId();
                    break; 
                }
            }
        }
        

        if($id) {
            try {
                $this->cart->removeItem($id);
                if(isset($protectionItemId)){
                    $this->cart->removeItem($protectionItemId);
                }
                // We should set Totals to be recollected once more because of Cart model as usually is loading
                // before action executing and in case when triggerRecollect setted as true recollecting will
                // executed and the flag will be true already.
                $this->cart->getQuote()->setTotalsCollectedFlag(false);
                $this->cart->save();
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('We can\'t remove the item.'));
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
            }
        }
        $defaultUrl = $this->_objectManager->create(\Magento\Framework\UrlInterface::class)->getUrl('*/*');
        return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRedirectUrl($defaultUrl));
    }
}
