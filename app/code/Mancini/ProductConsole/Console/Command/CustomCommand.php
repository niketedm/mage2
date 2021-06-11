<?php

namespace Mancini\ProductConsole\Console\Command;

use Exception;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for displaying information related to indexers.
 */
class CustomCommand extends AbstractCommand
{
    protected $relatedProducts = [];
    protected $configurableProducts = [];
    protected $configurableRelations = [];
    protected $productCategories = [];
    protected $skuSerial = [];

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('product_console:custom')
            ->setDescription('Update Product Data')
            ->setDefinition([
                new InputArgument(
                    parent::FILENAME_ARGUMENT,
                    InputArgument::OPTIONAL,
                    'Filename'
                ),
                new InputArgument(
                    'type',
                    InputArgument::OPTIONAL,
                    'Type'
                )
            ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $this->getImportFileName($input);
        $type = $this->getType($input);

        $importModel = $this->getObjectManager()->create('Mancini\ProductConsole\Model\Import');

        $readModel = $this->getObjectManager()->create('Mancini\ProductConsole\Model\Read');
        $sourceCsv = $readModel->getCsv($filename);

        //$csvData = [];
        foreach ($sourceCsv as $rowNum => $rowData) {
            $dataArray = $importModel->getDataArray('product_console_import', 'import', $rowData);
            $this->skuSerial[$dataArray['sku']] = $dataArray;
            if ($dataArray['parent_sku']) {
                $this->configurableProducts[] = $dataArray['parent_sku'];
                $this->configurableRelations[$dataArray['parent_sku']][] = $dataArray['sku'];
            }
        }
        $this->configurableProducts = array_unique($this->configurableProducts);
        if ($type == 'images') {
            $galleryModel = $this->getObjectManager()->create('Mancini\ProductConsole\Model\Gallery');
            if (!empty($this->configurableProducts)) {
                array_unique($this->configurableProducts);
                foreach ($this->configurableProducts as $sku) {
                    if ($galleryModel->isProductExist($sku) && isset($this->skuSerial[$sku])) {
                        $this->skuSerial[$sku . '-CONFIG'] = $this->skuSerial[$sku];
                    }

                }
            }
            if (!empty($this->skuSerial)) {
                foreach ($this->skuSerial as $sku => $productData) {
                    $output->writeln($sku);
                    //$galleryModel->importImage($sku, $productData);
                    $galleryModel->importImageByDir($sku, $productData);
                }
            }

        } elseif ($type == 'missing_images') {
            $galleryModel = $this->getObjectManager()->create('Mancini\ProductConsole\Model\Gallery');
            if (!empty($this->configurableProducts)) {
                array_unique($this->configurableProducts);
                foreach ($this->configurableProducts as $sku) {
                    if ($galleryModel->isProductExist($sku) && isset($this->skuSerial[$sku])) {
                        $this->skuSerial[$sku . '-CONFIG'] = $this->skuSerial[$sku];
                    }

                }
            }

            if (!empty($this->skuSerial)) {
                foreach ($this->skuSerial as $sku => $productData) {
                    //$output->writeln($sku);
                    $result = $galleryModel->checkMissingImage($sku, $productData);
                    if (!$result) {
                        $output->writeln($sku . '     ' . $productData['status']);
                    }
                }
            }
        } elseif ($type == 'remove_product_category') {
            $productModel = $this->getObjectManager()->create('Mancini\ProductConsole\Model\Product');
            $productModel->unassignProductCategories();
        } elseif ($type == 'update_sku_serial') {
            $productModel = $this->getObjectManager()->create('Mancini\ProductConsole\Model\Product');
            if (!empty($this->configurableProducts)) {
                array_unique($this->configurableProducts);
                foreach ($this->configurableProducts as $sku) {
                    if ($productModel->isProductExist($sku) && isset($this->skuSerial[$sku])) {
                        $this->skuSerial[$sku . '-CONFIG'] = $this->skuSerial[$sku];
                    }

                }
            }

            if (!empty($this->skuSerial)) {
                foreach ($this->skuSerial as $sku => $productData) {
                    //$output->writeln($sku);
                    $productModel->saveProductAttribute($sku, 'sku_serial', $productData['sku_serial']);
                }
            }
        } elseif ($type == 'update_price') {
            $productModel = $this->getObjectManager()->create('Mancini\ProductConsole\Model\Product');
            if (!empty($this->configurableProducts)) {
                array_unique($this->configurableProducts);
                foreach ($this->configurableProducts as $sku) {
                    if ($productModel->isProductExist($sku) && isset($this->skuSerial[$sku])) {
                        $this->skuSerial[$sku . '-CONFIG'] = $this->skuSerial[$sku];
                    }

                }
            }

            if (!empty($this->skuSerial)) {
                foreach ($this->skuSerial as $sku => $productData) {
                    echo $sku . "\n";
                    $productData['price'] = floatval($productData['price']);
                    $productData['special_price'] = $productData['special_price'] ? floatval($productData['special_price']) : '';
                    if (!empty($productData['special_price'])) {
                        $specialPrice = $productData['special_price'] > $productData['price'] ? $productData['price'] : $productData['special_price'];
                        $price = $productData['special_price'] > $productData['price'] ? $productData['special_price'] : $productData['price'];
                        $productData['special_price'] = $specialPrice;
                        $productData['price'] = $price;
                        if ($productData['price'] == $productData['special_price']) {
                            $productData['special_price'] = '';
                        }
                    }
                    $productModel->saveProductAttribute($sku, 'price', $productData['price']);
                    $productModel->saveProductAttribute($sku, 'special_price', $productData['special_price']);
                }
            }
        } elseif ($type == 'remove_disabled_products') {
            /** @var Product $productModel */
            $productModel = $this->getObjectManager()->create('Magento\Catalog\Model\Product');
            /** @var AbstractCollection $productCollection */
            $productCollection = $productModel->getCollection()->addAttributeToFilter('status', Status::STATUS_DISABLED);
            var_dump($productCollection->getSize());
            $productCollection->delete();
        } elseif ($type == 'update_product_categories') {
            $productModel = $this->getObjectManager()->create('Mancini\ProductConsole\Model\Product');
            if (!empty($this->configurableProducts)) {
                $configurableProduct = array_unique($this->configurableProducts);
                foreach ($configurableProduct as $sku) {
                    if ($productModel->isProductExist($sku) && isset($this->skuSerial[$sku])) {
                        $this->skuSerial[$sku . '-CONFIG'] = $this->skuSerial[$sku];
                    }
                }
            }

            if (!empty($this->skuSerial)) {
                foreach ($this->skuSerial as $sku => $productData) {
                    echo $sku . "\n";
                    /*if(!empty($sku)){
                        $productModel->unassignProductCategories($sku);
                    }*/
                    $categories = $productModel->prepareCategories($productData);
                    try {
                        $productModel->assignProductCategories($sku, $categories);
                    } catch (Exception $e) {
                        $output->writeln($sku . ' ' . $e->getMessage());
                    }
                }
            }
        } elseif ($type == 'update_configurable_visibility') {
            $productModel = $this->getObjectManager()->create('Mancini\ProductConsole\Model\Product');
            if (!empty($this->configurableProducts)) {
                $configurableProduct = array_unique($this->configurableProducts);
                foreach ($configurableProduct as $sku) {
                    if ($productModel->isProductExist($sku) && isset($this->skuSerial[$sku])) {
                        echo $sku . '-CONFIG' . "\n";
                        $this->skuSerial[$sku . '-CONFIG'] = $this->skuSerial[$sku];
                        $productModel->updateVisibility($sku . '-CONFIG', $this->skuSerial[$sku]);
                    }
                }
            }
        } elseif ($type == 'update_related_products') {
            $productModel = $this->getObjectManager()->create('Mancini\ProductConsole\Model\Product');
            if (!empty($this->configurableProducts)) {
                $configurableProduct = array_unique($this->configurableProducts);
                foreach ($configurableProduct as $sku) {
                    if ($productModel->isProductExist($sku) && isset($this->skuSerial[$sku])) {
                        $this->skuSerial[$sku . '-CONFIG'] = $this->skuSerial[$sku];
                    }
                }
            }

            if (!empty($this->skuSerial)) {
                foreach ($this->skuSerial as $sku => $productData) {
                    if (empty($productData['related_products'])) {
                        continue;
                    }
                    echo $sku . "\n";
                    $linkArray = explode('&', $productData['related_products']);
                    try {
                        $productModel->addRelatedProducts($sku, $linkArray);
                    } catch (Exception $e) {
                        $output->writeln($sku . ' ' . $e->getMessage());
                    }
                }
            }
        } elseif ($type == 'update_configurable_products') {
            $productModel = $this->getObjectManager()->create('Mancini\ProductConsole\Model\Product');
            if (!empty($this->configurableRelations)) {
                //$configurableProduct = array_unique($this->configureableProducts);
                foreach ($this->configurableRelations as $sku => $childSkus) {
                    if (isset($this->skuSerial[$sku])) {
                        $output->writeln($sku);
                        $productModel->processConfigurableProducts($sku, $childSkus, $this->skuSerial[$sku]);
                    } else {
                        $productModel->proccessParentSkuNotExists($sku, $childSkus);
                    }


                }
            }
        } elseif ($type == 'update_dimensions') {
            $productModel = $this->getObjectManager()->create('Mancini\ProductConsole\Model\Product');
            if (!empty($this->configurableProducts)) {
                $this->configurableProducts = array_unique($this->configurableProducts);
                foreach ($this->configurableProducts as $sku) {
                    if ($productModel->isProductExist($sku) && isset($this->skuSerial[$sku])) {
                        $this->skuSerial[$sku . '-CONFIG'] = $this->skuSerial[$sku];
                    }

                }
            }

            if (!empty($this->skuSerial)) {
                foreach ($this->skuSerial as $sku => $productData) {
                    echo $sku . "\n";

                    $productModel->saveProductAttribute($sku, 'sw_height', $productData['sw_height']);
                    $productModel->saveProductAttribute($sku, 'sw_length', $productData['sw_length']);
                    $productModel->saveProductAttribute($sku, 'sw_depth', $productData['sw_depth']);
                }
            }
        } elseif ($type == 'update_visibility') {
            $productModel = $this->getObjectManager()->create('Mancini\ProductConsole\Model\Product');
            if (!empty($this->configurableProducts)) {
                $this->configurableProducts = array_unique($this->configurableProducts);
                foreach ($this->configurableProducts as $sku) {
                    if ($productModel->isProductExist($sku) && isset($this->skuSerial[$sku])) {
                        $this->skuSerial[$sku . '-CONFIG'] = $this->skuSerial[$sku];
                    }

                }
            }

            if (!empty($this->skuSerial)) {
                foreach ($this->skuSerial as $sku => $productData) {

                    if (intval($productData['status']) == 0) {
                        echo $sku . "\n";
                        $productModel->saveProductAttribute($sku, 'visibility', Visibility::VISIBILITY_IN_SEARCH);
                    }
                }

            }
        } elseif ($type == "missing_category") {
            $productModel = $this->getObjectManager()->create('Mancini\ProductConsole\Model\Product');
            if (!empty($this->configurableProducts)) {
                $this->configurableProducts = array_unique($this->configurableProducts);
                foreach ($this->configurableProducts as $sku) {
                    if ($productModel->isProductExist($sku) && isset($this->skuSerial[$sku])) {
                        $this->skuSerial[$sku . '-CONFIG'] = $this->skuSerial[$sku];
                    }

                }
            }

            if (!empty($this->skuSerial)) {
                foreach ($this->skuSerial as $sku => $productData) {
                    if (!$productModel->isProductExist($sku)) {
                        continue;
                    }
                    $categoryIds = $productModel->getProductCategoryIds($sku);
                    //var_dump($categoryIds);
                    if ($categoryIds === false || count($categoryIds) > 0) {
                        //var_dump($categoryIds);
                        //var_dump(count($categoryIds));
                        continue;
                    }
                    if (!$productModel->canAssignCategory($sku)) {
                        continue;
                    }
                    //echo $sku."\n";
                    $output->writeln($sku);
                    $categories = $productModel->prepareCategories($productData);
                    try {
                        $productModel->assignProductCategories($sku, $categories);
                    } catch (Exception $e) {
                        $output->writeln($sku . ' ' . $e->getMessage());
                    }
                }

            }
        } elseif ($type == 'check_none_parent_sku') {
            $productModel = $this->getObjectManager()->create('Mancini\ProductConsole\Model\Product');
            if (!empty($this->configurableRelations)) {
                //$configurableProduct = array_unique($this->configureableProducts);
                foreach ($this->configurableRelations as $sku => $childSkus) {
                    if (!$productModel->isProductExist($sku)) {
                        //continue;
                        foreach ($childSkus as $simple) {
                            if (isset($this->skuSerial[$simple]) && !in_array($this->skuSerial[$simple]['sw_status'], array('DV', 'SDV', 'X'))) {
                                echo $simple . "," . $sku . "," . $this->skuSerial[$simple]['sw_status'] . "\n";
                            }

                        }
                    }


                }
            }
        } elseif ($type == 'missing_dimensions') {
            $productModel = $this->getObjectManager()->create('Mancini\ProductConsole\Model\Product');

            foreach ($this->skuSerial as $sku => $dataArray) {
                //if(!in_array($dataArray['sw_status'], array('DV','SDV','X'))){
                if (!in_array($dataArray['web_collections'], array('Mattresses', 'Mattresses&Adjustable Mattresses', 'Bed in a box'))) {
                    if (empty($dataArray['sw_length']) || empty($dataArray['sw_length']) || empty($dataArray['sw_length'])) {
                        echo $sku . "," . $dataArray['sw_height'] . ',' . $dataArray['sw_length'], ',' . $dataArray['sw_depth'] . ',' . $dataArray['sw_status'] . "\n";
                    }
                }
                //}
            }
        }
    }

    /**
     * Return import filename
     *
     * @return string $filename
     */
    protected function getType(InputInterface $input)
    {
        $type = '';
        if ($input->getArgument('type')) {
            $type = $input->getArgument('type');
        }
        return $type;
    }

    protected function createSourceCsvModel($file)
    {
        return $this->getObjectManager()->create('Mancini\ProductConsole\Model\Source\CsvFactory');
    }
}
