<?php

namespace Mancini\ProductConsole\Controller\Adminhtml\Refresh;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Catalog\Controller\Adminhtml\Product;
use Magento\Catalog\Controller\Adminhtml\Product\Builder;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Mancini\ProductConsole\Model\Gallery;

class MassImage extends Product
{
    /**
     * Massactions filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    protected $gallery;

    /**
     * @param Context $context
     * @param Builder $productBuilder
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param Gallery $gallery
     */
    public function __construct(
        Context $context,
        Builder $productBuilder,
        Filter $filter,
        CollectionFactory $collectionFactory,
        Gallery $gallery
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->gallery = $gallery;
        parent::__construct($context, $productBuilder);
    }

    /**
     * @return Redirect
     * @throws LocalizedException
     */
    public function execute()
    {
        /** @var Collection $collection */
        $collection = $this->filter->getCollection($this->collectionFactory->create())->addAttributeToSelect('*');
        $productRefresh = 0;
        foreach ($collection->getItems() as $product) {
            /** @var \Magento\Catalog\Model\Product $product */
            try {
                $this->gallery->refreshImage($product->getSku(), $product->getData());
                $productRefresh++;
            } catch (Exception $e) {
                $this->messageManager->addError(
                    $e->getMessage()
                );
            }
        }
        $this->messageManager->addSuccess(
            __('A total of %1 record(s) have been deleted.', $productRefresh)
        );

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('catalog/product/index');
    }
}
