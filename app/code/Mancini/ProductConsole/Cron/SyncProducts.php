<?php

namespace Mancini\ProductConsole\Cron;

use Exception;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Indexer\Model\Processor;
use Mancini\ProductConsole\Model\Gallery;
use Mancini\ProductConsole\Model\Logger;

class SyncProducts
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
        /*$this->logger->info("Start Re-import Product Images ");
        /** @var Collection $collection 
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
        $this->logger->info("End Re-import Product Images ");*/

        $this->logger->info("Start Sync Products ");
        $date = date('Y-m-d H:i:s');
        $this->logger->info($date);
        $this->logger->info("End  Sync Products  ");

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/synccron.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            //$sku = $jsonArray['items'][0]['sku'];
            $logger->info($date);
        
    }
}
