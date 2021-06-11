<?php
namespace Mancini\Cart\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session;
use Magento\Quote\Model\QuoteRepository;

class Delete extends \Magento\Framework\App\Action\Action
{
     

    /**
     * @var  \Magento\Checkout\Helper\Cart
     */
    protected $cartHelper;

    /**
     * @var Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepo; 

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param Mancini\ShippingZone\Helper\Data $helperData
     * @param Mancini\ShippingZone\Model\ShippingZone\Zipcodes $locationModelFactory
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        Cart $modelCart,
        \Magento\Checkout\Helper\Cart $cartHelper,
        QuoteRepository $quoteRepo
  
    ) {
        $this->checkoutSession  =   $checkoutSession;
        $this->_modelCart       =   $modelCart;
        $this->cartHelper       =   $cartHelper;
        $this->quoteRepo        =   $quoteRepo;
        parent::__construct($context);
    }


    public function execute()
    {
        $params              = $this->getRequest()->getParams();

        $protectionProductId = $params['protectionproduct'];
        $quoteId             = $params['quoteitemid'];
        $mainprdid           = $params['mainprditemid'];

        $items               = $this->cartHelper->getCart()->getItems();

        foreach ($items as $item) {
            if ($item->getProduct()->getId() == $protectionProductId  ) {
                $itemId     = $item->getItemId();
                $this->cartHelper->getCart()->removeItem($itemId)->save();
               
                $quote      = $this->quoteRepo->getActive($quoteId);
                $quoteItem  = $quote->getItemById($mainprdid);
                $quoteItem->setProtectionPlan(0);
                $quoteItem->save();

                break;
            }
        }
        
    }
}




