<?php
namespace Mancini\ProductConsole\Model;

use Exception;
use Magento\Framework\Filesystem;
use Mancini\ProductConsole\Model\ProductSync;

class ImportApiSource
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
        ProductSync $productModel,
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

    public function importApiSource($items)
    {
        $suffix = 'backend-apisync-' . date('Y-m-d');
        $this->productModel->setDate($suffix);
        $this->galleryModel->setDate($suffix);
        $this->logWriter->setSuffix($suffix);


        //try {

        foreach ($items as $item) {
           // $dataArray = $this->importModel->getDataArray('product_console_import', 'import', $rowData);

            $sku            = $item['sku'];
            $skuserial      = $item['skuserial'];
            $qty            = $item['availabletosell'];
            $sw_status      = $item['inventoryrec']['status'];
            $status         = 1;
            $basePrice      = $item['inventoryrec']['retail2'];
            $specialPrice   = $item['inventoryrec']['retail1'];
            $brand          = $item['factsrec']['brand'];
            $brandCollection= $item['factsrec']['collection'];
            $shortDescription=$item['factsrec']['shortdescription'];
            $mattFirmness    =$item['factsrec']['mattfirmness'];
            $mattSize        =$item['factsrec']['mattsize'];
            $isParent        = 1;
            $parentSku       = '';
            $cost            = "255.00";
            $webCollections  = $item['factsrec']['webcollections'];

            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/productSyncAPIstaticresp.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            //$sku = $jsonArray['items'][0]['sku'];
            $logger->info(print_r($item, true));

    
            $dataArray = array(
                    'sku'=>$sku, //'SAT-MAD-8N47LV-71456L',
                    'sw_status'=>$sw_status,//'CR',
                    'status'=>$status,//1,
                    'price'=>$basePrice,//979.99,
                    'special_price'=>$specialPrice,// 749.99,
                    'sku_serial'=>$skuserial, // '3021244',
                    'qty'=>$qty, //1234,
                    'custom_a'=>'custom attr',
                    'brand' => $brand,//'Best Home Furnishings test',
                    'brand_collection' => $brandCollection,//'Maddox test',
                    'short_description' => $shortDescription,//'Rocker Recliner test',
                    'matt_firmness' =>$mattFirmness, 
                    'matt_size' =>$mattSize,
                    'is_parent' => $isParent,//1,
                    'parent_sku' =>$parentSku,//'',
                    'cost' => $cost,//255.00,
                    'web_collections'=>$webCollections,//'Furniture&Recliners and Lift Chairs'
            );


            $logger->info(print_r($dataArray, true));
                
                //$productId = $this->productModel->saveSimpleProduct($dataArray);
                //exit;

            
            $productId = $this->productModel->saveSimpleProduct($dataArray);
          
                //$galleryModel->importImageByDir($dataArray['sku'], $dataArray);
           

            if (isset($dataArray['related_products']) && $dataArray['related_products']) {
                $this->relatedProducts[$dataArray['sku']] = explode('&', $dataArray['related_products']);
            }
            if (isset($dataArray['parent_sku']) && $dataArray['parent_sku']) {
                $this->configureableProducts[$dataArray['parent_sku']][] = $dataArray['sku'];
            }

            $this->productCategories[$dataArray['sku']] = $this->productModel->prepareCategories($dataArray);

            $this->skuSerial[$dataArray['sku']] = $dataArray;

        }

        //} catch (Exception $e) {
        //$output->writeln($dataArray['sku'] . ' ' . $e->getMessage());
           // $this->messageManager->addError($e->getMessage());
           // $data = ['status' => 'Error','message'=> $e->getMessage()];
        //}

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
