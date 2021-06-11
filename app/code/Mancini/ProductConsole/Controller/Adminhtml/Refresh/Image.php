<?php

namespace Mancini\ProductConsole\Controller\Adminhtml\Refresh;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Catalog\Controller\Adminhtml\Product;
use Magento\Catalog\Controller\Adminhtml\Product\Builder;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Mancini\ProductConsole\Model\Gallery;

class Image extends Product
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    protected $gallery;

    /**
     * @param Context $context
     * @param Builder $productBuilder
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Builder $productBuilder,
        PageFactory $resultPageFactory,
        Gallery $gallery
    ) {
        parent::__construct($context, $productBuilder);
        $this->resultPageFactory = $resultPageFactory;
        $this->gallery = $gallery;
    }

    /**
     * Product edit form
     *
     * @return ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        /** @var StoreManagerInterface $storeManager */
        $productId = (int)$this->getRequest()->getParam('id');
        $product = $this->productBuilder->build($this->getRequest());

        if (($productId && !$product->getEntityId())) {
            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $this->messageManager->addError(__('This product doesn\'t exist.'));
            return $resultRedirect->setPath('catalog/product/');
        } else if ($productId === 0) {
            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $this->messageManager->addError(__('Invalid product id. Should be numeric value greater than 0'));
            return $resultRedirect->setPath('catalog/product/');
        }

        try {
            $this->gallery->refreshImage($product->getSku(), $product->getData());
            $this->messageManager->addSuccess(__('You refreshed product image'));
        } catch (Exception $e) {

            $this->messageManager->addError($e->getMessage());

        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('catalog/product/edit', ['id' => $productId]);
        return $resultRedirect;
    }
}
