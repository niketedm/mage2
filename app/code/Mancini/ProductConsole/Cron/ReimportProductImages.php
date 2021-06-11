<?php

namespace Mancini\ProductConsole\Cron;

use Exception;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Indexer\Model\Processor;
use Mancini\ProductConsole\Model\Gallery;
use Mancini\ProductConsole\Model\Logger;

class ReimportProductImages
{
    /** @var Product */
    protected $catalogProduct;

    /** @var Gallery */
    protected $galleryImport;

    /** @var Logger */
    protected $logger;

    /**
     * @param Processor $processor
     */
    public function __construct(
        Product $catalogProduct,
        Gallery $galleryImport,
        Logger $logger
    ) {
        $this->catalogProduct = $catalogProduct;
        $this->galleryImport = $galleryImport;
        $this->logger = $logger;
    }

    public function execute()
    {
        $this->logger->info("Start Re-import Product Images ");
        /** @var Collection $collection */
        $collection = $this->catalogProduct->getCollection();
        $collection->addAttributeToSelect('*')
            ->addAttributeToFilter('status', Status::STATUS_ENABLED);
        foreach ($collection as $item) {
            //$this->logger->info($item->getSku());
            try {
                $this->galleryImport->importImageByDir($item->getSku(), $item->getData());
            } catch (Exception $e) {
                $this->logger->info($item->getSku() . ' ' . $e->getMessage());
            }
        }
        $this->logger->info("End Re-import Product Images ");
    }
}
