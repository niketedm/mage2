<?php
/**
 * @author Mancini
 * @package Mancini_Cart
 * Date: 2021-05-26
 */

declare(strict_types=1);

namespace Mancini\Cart\Controller\Protector;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Checkout\Model\Cart;
use Magento\Catalog\Model\Product;
use Magento\Framework\Data\Form\FormKey;
use Magento\Quote\Model\QuoteRepository;
use Magento\Framework\UrlFactory;
use Magento\Framework\Controller\Result\Redirect;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;
    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;   
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;
   
    protected $quoteRepo;
    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param PageFactory $resultPageFactory
     * @param FormKey $formKey
     * @param Cart $cart
     * @param Product $product
     */
    public function __construct(
        QuoteRepository $quoteRepo,
        Context $context,
        JsonFactory $resultJsonFactory,
        FormKey $formKey,
        Cart $cart,
        Product $product, 
        UrlFactory $urlFactory
    )
    {
        $this->_resultJsonFactory   = $resultJsonFactory;
        $this->formKey              = $formKey;
        $this->cart                 = $cart;
        $this->product              = $product;
        $this->quoteRepo            = $quoteRepo;
        $this->urlModel             = $urlFactory->create();
        parent::__construct($context);
    }
    public function execute()
    {
        $resultJson = $this->_resultJsonFactory->create();
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $params     = $this->getRequest()->getParams();
        $productId  = isset($params['productid']) ? (int)$params['productid'] : 0;
        $quoteId    = isset($params['quoteid']) ? (int)$params['quoteid'] : 0;
        $qty        = isset($params['qty']) ? (int)$params['qty'] : 0;

        if($productId > 0 && $quoteId > 0)
        {
            try {
                $params['form_key']      = $this->formKey->getFormKey();
                $params['product']       = $productId;
                $params['qty']           = $qty;

                $_product                = $this->product->load($productId);
                $this->cart->addProduct($_product, $params);
                $this->cart->save();

                $quote = $this->quoteRepo->getActive($quoteId);


                foreach($quote->getItems() as $item){
                    $quoteItem = $quote->getItemById($item['item_id']);

                    if (!$quoteItem) {
                    continue;
                    }
                    $quoteItem->setProtectionPlan($productId);
                    $quoteItem->save();
                }

              
                $this->quoteRepo->save($quote);

                $this->messageManager->addSuccess(__('Successfully Added product to cart'));

            }
            catch(\Exception $e) {
                $this->messageManager->addErrorMessage(__("Couldn't add the product to cart"));
            }
        }


        $defaultUrl = $this->urlModel->getUrl('checkout/cart', ['_secure' => true]);
        return $resultRedirect->setUrl($this->_redirect->error($defaultUrl));

    }
}
