<?php
namespace Mancini\ProductConsole\Model;

use Exception;
use Magento\Framework\Filesystem;

class ImportSource
{
    protected $relatedProducts = [];
    protected $configureableProducts = [];
    protected $productCategories = [];
    protected $skuSerial = [];

    protected $productModel;
    protected $galleryModel;
    protected $importModel;
    protected $logWriter;

    /**
     * @param Import\Source\CsvFactory $sourceCsvFactory
     * @param Filesystem $filesystem
     */
    public function __construct(
        Product $productModel,
        Gallery $galleryModel,
        Import $importModel,
        LogWriter $logWriter
    )
    {
        $this->productModel = $productModel;
        $this->galleryModel = $galleryModel;
        $this->importModel = $importModel;
        $this->logWriter = $logWriter;
    }

    public function importSource($source)
    {
        $suffix = 'backend-import-' . date('Y-m-d');
        $this->productModel->setDate($suffix);
        $this->galleryModel->setDate($suffix);
        $this->logWriter->setSuffix($suffix);

        foreach ($source as $rowNum => $rowData) {
            $dataArray = $this->importModel->getDataArray('product_console_import', 'import', $rowData);

            try {
                $productId = $this->productModel->saveSimpleProduct($dataArray);
                //$galleryModel->importImageByDir($dataArray['sku'], $dataArray);
            } catch (Exception $e) {
                $output->writeln($dataArray['sku'] . ' ' . $e->getMessage());
                $productId = false;
            }

            if ($dataArray['related_products']) {
                $this->relatedProducts[$dataArray['sku']] = explode('&', $dataArray['related_products']);
            }
            if ($dataArray['parent_sku']) {
                $this->configureableProducts[$dataArray['parent_sku']][] = $dataArray['sku'];
            }

            $this->productCategories[$dataArray['sku']] = $this->productModel->prepareCategories($dataArray);

            $this->skuSerial[$dataArray['sku']] = $dataArray;

        }
        if (!empty($this->configureableProducts)) {
            foreach ($this->configureableProducts as $sku => $childSkus) {

                $data = isset($this->skuSerial[$sku]) ? $this->skuSerial[$sku] : '';
                if ($data == '') {
                    $this->productModel->proccessParentSkuNotExists($sku, $childSkus);
                    continue;
                }
                $configurableSku = $this->productModel->processConfigurableProducts($sku, $childSkus, $data);
                if ($configurableSku) {
                    $this->skuSerial[$configurableSku] = $this->skuSerial[$sku];
                    if (isset($this->productCategories[$sku])) {
                        $this->productCategories[$configurableSku] = $this->productCategories[$sku];
                    }
                    if (isset($this->relatedProducts[$sku])) {
                        $this->relatedProducts[$configurableSku] = $this->relatedProducts[$sku];
                    }

                }
            }
        }
        if (!empty($this->relatedProducts)) {
            foreach ($this->relatedProducts as $sku => $linkArray) {
                $this->productModel->addRelatedProducts($sku, $linkArray);
            }
        }


        if (!empty($this->skuSerial)) {
            foreach ($this->skuSerial as $sku => $productData) {
                try {
                    $this->galleryModel->importImageByDir($sku, $productData);
                } catch (Exception $e) {
                    echo $sku . ' ' . $e->getMessage();
                }
            }
        }

        if (!empty($this->productCategories)) {
            foreach ($this->productCategories as $sku => $categories) {
                try {
                    $this->productModel->assignProductCategories($sku, $categories);
                } catch (Exception $e) {
                    $output->writeln($sku . ' ' . $e->getMessage());
                }
            }

        }
        $logContent = $this->logWriter->getLogContent();
        return $logContent;
    }

}

?>
