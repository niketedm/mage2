<?php
/**
 *
 * Copyright © 2015 Zmagecommerce. All rights reserved.
 */
namespace Mancini\WishlistIcon\Controller\Index;

use Magento\Framework\Controller\Result\JsonFactory;

class Wishlist extends \Magento\Framework\App\Action\Action
{
    protected $_customerSession;
    protected $_resultJsonFactory;
    protected $wishlistProvider;
    protected $_wishlistRepository;
    protected $_productRepository;
    protected $customerSession;
    protected $urlInterface;
    protected $_session;
    protected $storeManager;
    protected $resultPageFactory;
    private $wishlist;
    protected $mancini;

    public function __construct(
        \Magento\Wishlist\Model\Wishlist $wishlist,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Wishlist\Model\WishlistFactory $wishlistRepository,
        JsonFactory $resultJsonFactory,
        \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        $this->_customerSession = $customerSession;
        $this->_wishlistRepository = $wishlistRepository;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->wishlistProvider = $wishlistProvider;
        $this->_productRepository = $productRepository;
        $this->urlInterface = $urlInterface;
        $this->_session = $session;
        $this->storeManager = $storeManager;
        $this->resultPageFactory = $resultPageFactory;
        $this->wishlist = $wishlist;
        parent::__construct($context);

    }

    public function execute()
    {

        if ($this->getRequest()->getParam('method') == 'allproducts') {

            $customerId = $this->_customerSession->getCustomer()->getId();

            $mancini = array();
            $wishlist_collection = $this->wishlist->loadByCustomerId($customerId, true)->getItemCollection();
            foreach ($wishlist_collection as $item) {

                $mancini[] = $item->getProduct()->getId();
            }
            $response['productid'] = $mancini;
            $resultJson = $this->_resultJsonFactory->create();
            $resultJson->setData($response);

            return $resultJson;
        } else {
            //If customer logged in 
            if ($this->_session->isLoggedIn() == '1') {
                $response = [];
                $productId = $this->getRequest()->getParam('productId');
                $customerId = $this->_customerSession->getCustomer()->getId();
                $wishlistCollection = $this->wishlist->loadByCustomerId($customerId, true)->getItemCollection();

                if ($productId && $customerId) {
                    try {
                        $product = $this->_productRepository->getById($productId);
                    } catch (NoSuchEntityException $e) {
                        $product = null;
                    }
                    if ($product) {
                        $wishlist = $this->_wishlistRepository->create()->loadByCustomerId($customerId, true);

                        //Check whether the product is already existing
                        $isExist = false;
                        foreach ( $wishlistCollection as $wishlistItem ) { 
                            if($productId == $wishlistItem['product_id']){
                                $isExist = true;
                                $wishlistItem->delete();
                                break;
                            }
                        }

                        //If the product already exist, remove that product
                        if ($isExist){
                            //logger
                        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/4-4-2021.log');
                        $logger = new \Zend\Log\Logger();
                        $logger->addWriter($writer);
                        $logger->info($isExist);


                            $response['delete'] = 1;
                        } else {
                            $wishlist->addNewItem($product);
                            $wishlist->save();
                            $response['add'] = 1;

                        }
                       
                    }

                    $resultJson = $this->_resultJsonFactory->create();
                    $resultJson->setData($response);
                    return $resultJson;
                }

            } else {

                $response['productid'] = 'false';

                $resultJson = $this->_resultJsonFactory->create();
                $resultJson->setData($response);
                return $resultJson;

            }

        }

        echo("hello hi");

    }
}
