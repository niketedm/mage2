<?php

namespace Mancini\ProductConsole\Console\Command;

use Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for displaying information related to indexers.
 */
class ImportCommand extends AbstractCommand
{
    protected $relatedProducts = [];
    protected $configurableProducts = [];
    protected $productCategories = [];
    protected $skuSerial = [];
    protected $position = null;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('product_console:import')
            ->setDescription('Import Product Data')
            ->setDefinition([
                new InputArgument(
                    parent::FILENAME_ARGUMENT,
                    InputArgument::OPTIONAL,
                    'Filename'
                ),
                new InputArgument(
                    'position',
                    InputArgument::OPTIONAL,
                    'Position'
                )
            ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $this->getImportFileName($input);
        $this->setPosition($input);

        $importModel = $this->getObjectManager()->create('Mancini\ProductConsole\Model\Import');
        $readModel = $this->getObjectManager()->create('Mancini\ProductConsole\Model\Read');
        $productModel = $this->getObjectManager()->create('Mancini\ProductConsole\Model\Product');
        $productModel->setDate(date('Y-m-d'));
        $galleryModel = $this->getObjectManager()->create('Mancini\ProductConsole\Model\Gallery');
        $galleryModel->setDate(date('Y-m-d'));

        $logWriter = $this->getObjectManager()->create('Mancini\ProductConsole\Model\LogWriter');
        $logWriter->setSuffix(date('Y-m-d'));

        try {
            $sourceCsv = $readModel->getCsv($filename);
        } catch (Exception $e) {
            echo $e->getMessage() . "\n";
            $logWriter->writeLog($e->getMessage());
            $logWriter->sendLogEmail();
            exit();
        }
        $logWriter->writeLog('Start importing ' . $filename);
        $output->writeln('Start Import Simple Product');
        $count = 0;
        $page = 1;
        foreach ($sourceCsv as $rowNum => $rowData) {
            $escape = false;
            if ($count == 100) {
                $output->writeln($page * 100);
                $page++;
                $count = 0;
            }

            $dataArray = $importModel->getDataArray('product_console_import', 'import', $rowData);

            //$output->writeln($rowNum.' '.$dataArray['sku']);
            //3812
            if ($rowNum <= $this->position && $this->position != null) {
                $escape = true;
            }
            if (!$escape):
                try {
                    $productId = $productModel->saveSimpleProduct($dataArray);
                    //$galleryModel->importImageByDir($dataArray['sku'], $dataArray);
                } catch (Exception $e) {
                    $output->writeln($dataArray['sku'] . ' ' . $e->getMessage());
                    $productId = false;
                }
            else:
                $productId = true;
            endif;
            //exit();
            if ($dataArray['related_products']) {
                $this->relatedProducts[$dataArray['sku']] = explode('&', $dataArray['related_products']);
            }
            if ($dataArray['parent_sku']) {
                $this->configurableProducts[$dataArray['parent_sku']][] = $dataArray['sku'];
            }
            //$categories = $this->prepareCategories($productData);
            //$this->assignProductCategories($newProduct->getSku(), $categories);
            //if($productId){
            $this->productCategories[$dataArray['sku']] = $productModel->prepareCategories($dataArray);
            //}
            $this->skuSerial[$dataArray['sku']] = $dataArray;
            //$output->writeln($rowNum.' '.$dataArray['sku']);
            //exit();
            $count++;
        }
        $output->writeln('End Import Simple Product');
        if (!empty($this->configurableProducts)) {
            $output->writeln('Start processing configurable products');
            $i = 0;
            foreach ($this->configurableProducts as $sku => $childSkus) {
                //$output->writeln($sku);
                if ($i % 100 == 0) {
                    $output->writeln($i);
                }
                $data = isset($this->skuSerial[$sku]) ? $this->skuSerial[$sku] : '';
                if ($data == '') {
                    $productModel->proccessParentSkuNotExists($sku, $childSkus);
                    continue;
                }
                $configurableSku = $productModel->processConfigurableProducts($sku, $childSkus, $data);
                if ($configurableSku) {
                    $this->skuSerial[$configurableSku] = $this->skuSerial[$sku];
                    //$galleryModel->importImageByDir($configurableSku, $data);
                    if (isset($this->productCategories[$sku])) {
                        $this->productCategories[$configurableSku] = $this->productCategories[$sku];
                    }

                    /*foreach($childSkus as $childsku){
                        if(isset($this->productCategories[$childsku])){
                            unset($this->productCategories[$childsku]);
                        }
                    }*/
                    if (isset($this->relatedProducts[$sku])) {
                        $this->relatedProducts[$configurableSku] = $this->relatedProducts[$sku];
                    }

                }
                $i++;
            }
        }

        if (!empty($this->relatedProducts)) {
            $output->writeln('Start adding related products');
            $i = 0;
            foreach ($this->relatedProducts as $sku => $linkArray) {
                //$output->writeln($sku);
                if ($i % 100 == 0) {
                    $output->writeln($i);
                }
                $productModel->addRelatedProducts($sku, $linkArray);
                $i++;
            }
        }

        if (!empty($this->skuSerial)) {
            $i = 0;
            $output->writeln('Start processing product images');
            foreach ($this->skuSerial as $sku => $productData) {
                if ($i % 100 == 0) {
                    $output->writeln($i);
                }
                try {
                    $galleryModel->importImageByDir($sku, $productData);
                } catch (Exception $e) {
                    echo $sku . ' ' . $e->getMessage();
                }
                $i++;
            }
            $output->writeln('End processing product images');
        }

        if (!empty($this->productCategories)) {
            $output->writeln('Start processing product categories');
            $i = 0;
            foreach ($this->productCategories as $sku => $categories) {
                //$output->writeln($sku);
                if ($i % 100 == 0) {
                    $output->writeln($i);
                }
                try {
                    $productModel->assignProductCategories($sku, $categories);
                } catch (Exception $e) {
                    $output->writeln($sku . ' ' . $e->getMessage());
                }

                $i++;
            }
            $output->writeln('End processing product categories');
        }
        $logWriter->writeLog('End importing ' . $filename);
        $logWriter->sendLogEmail();
    }

    /**
     * Return import filename
     *
     * @return string $filename
     */
    protected function getImportFileName(InputInterface $input)
    {
        if ($input->getArgument(self::FILENAME_ARGUMENT)) {
            $filename = $input->getArgument(self::FILENAME_ARGUMENT);
        } else {
            $filename = 'exportDownload' . '/export-' . date('Y-m-d') . '.csv';
        }
        return $filename;
    }

    protected function setPosition(InputInterface $input)
    {
        if ($input->getArgument('position')) {
            $position = intval($input->getArgument('position'));
            if (is_numeric($position) && $position > 0) {
                $this->position = $position;
            }
        }
    }

    protected function createSourceCsvModel($file)
    {
        return $this->getObjectManager()->create('Mancini\ProductConsole\Model\Source\CsvFactory');
    }
}
