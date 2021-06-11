<?php
namespace Mancini\ProductConsole\Model;

use Exception;
use Magento\Catalog\Api\AttributeSetRepositoryInterface;
use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Api\CategoryLinkRepositoryInterface;
use Magento\Catalog\Api\CategoryManagementInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\ProductAttributeOptionManagementInterface;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Api\ProductLinkManagementInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\CategoryProductLinkFactory;
use Magento\Catalog\Model\Indexer\Product\Price\Processor;
use Magento\Catalog\Model\Product\Attribute\Backend\Media\ImageEntryConverter;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\AttributeSet\Options;
use Magento\Catalog\Model\Product\Copier;
use Magento\Catalog\Model\Product\Gallery\EntryFactory;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ProductLink\LinkFactory;
use Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable\AttributeFactory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable\OptionValueFactory;
use Magento\Eav\Model\Entity\Attribute\OptionFactory;
use Magento\Framework\Api\Data\ImageContentInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\App\CacheInterface;
use Magento\Store\Model\StoreManagerInterface;

class ProductSync
{
    protected $productRepository;
    protected $productAttributeRepository;
    protected $productAttributeOptionManagement;
    protected $attributeSetRepository;
    protected $searchCriteria;
    protected $productFactory;
    protected $stockItemFactory;
    protected $attributeSetOptions;
    protected $optionFactory;
    protected $categoryRepository;
    protected $categoryManagement;
    protected $categoryLinkRepository;
    protected $categoryLinkManagement;
    protected $categoryFactory;
    protected $categoryProductLinkFactory;
    protected $productLinkManagement;
    protected $productLinkFactory;
    protected $configurableOptionFactory;
    protected $configurableOptionValueFactory;
    protected $productCopier;
    protected $galleryEntryFactory;
    protected $storeManager;
    protected $_productPriceIndexerProcessor;
    protected $_cache;
    protected $attributeTypeList = [];
    protected $attributesOptions = [];
    protected $attributeSets = [];
    protected $categoryTree = null;
    protected $count = 2;
    protected $logger;
    protected $logWriter;
    protected $date = '';


    public function __construct(
        ProductRepositoryInterface $productRepository,
        ProductAttributeRepositoryInterface $productAttributeRepository,
        ProductAttributeOptionManagementInterface $productAttributeOptionManagement,
        AttributeSetRepositoryInterface $attributeSetRepository,
        SearchCriteriaInterface $searchCriteria,
        StockItemInterfaceFactory $stockItemFactory,
        Options $attributeSetOptions,
        OptionFactory $optionFactory,
        CategoryRepositoryInterface $categoryRepository,
        CategoryManagementInterface $categoryManagement,
        CategoryLinkRepositoryInterface $categoryLinkRepository,
        CategoryLinkManagementInterface $categoryLinkManagement,
        CategoryFactory $categoryFactory,
        CategoryProductLinkFactory $categoryProductLinkFactory,
        ProductLinkManagementInterface $productLinkManagement,
        LinkFactory $productLinkFactory,
        ProductFactory $productFactory,
        AttributeFactory $configurableOptionFactory,
        OptionValueFactory $configurableOptionValueFactory,
        Copier $productCopier,
        EntryFactory $galleryEntryFactory,
        StoreManagerInterface $storeManager,
        Processor $productPriceIndexerProcessor,
        CacheInterface $cache,
        Logger $logger,
        LogWriter $logWriter
    )
    {
        $this->productRepository = $productRepository;
        $this->productAttributeRepository = $productAttributeRepository;
        $this->productAttributeOptionManagement = $productAttributeOptionManagement;
        $this->attributeSetRepository = $attributeSetRepository;
        $this->searchCriteria = $searchCriteria;
        $this->stockItemFactory = $stockItemFactory;
        $this->attributeSetOptions = $attributeSetOptions;
        $this->optionFactory = $optionFactory;
        $this->categoryRepository = $categoryRepository;
        $this->categoryManagement = $categoryManagement;
        $this->categoryLinkRepository = $categoryLinkRepository;
        $this->categoryLinkManagement = $categoryLinkManagement;
        $this->categoryFactory = $categoryFactory;
        $this->categoryProductLinkFactory = $categoryProductLinkFactory;
        $this->productLinkManagement = $productLinkManagement;
        $this->productLinkFactory = $productLinkFactory;
        $this->productFactory = $productFactory;
        $this->configurableOptionFactory = $configurableOptionFactory;
        $this->configurableOptionValueFactory = $configurableOptionValueFactory;
        $this->productCopier = $productCopier;
        $this->galleryEntryFactory = $galleryEntryFactory;
        $this->storeManager = $storeManager;
        $this->storeManager->setCurrentStore('admin');
        $this->_productPriceIndexerProcessor = $productPriceIndexerProcessor;
        $this->_cache = $cache;
        $this->logger = $logger;
        $this->logWriter = $logWriter;
    }

    public function setDate($dateString)
    {
        $this->date = $dateString;
        $this->logWriter->setSuffix($this->date);
    }

    public function saveSimpleProduct($productData)
    {

        $product = $this->productFactory->create()->load(null);
        if (!$this->isProductExist($productData['sku']) && in_array($productData['sw_status'], array('DV', 'SDV', 'X', 'DO'))) {
            return false;
        }

        $this->prepareProductName($productData);
        if (!$productData['name']) {
            $this->logWriter->writeLog("Error:" . $productData['sku'] . " can't generate product name. Status: " . $productData['sw_status']);
            return false;
        }
        $this->replaceData($productData);
        $product->setTypeId('simple');
        $this->prepareBaseData($product, $productData);
        $this->prepareGalleryData($product, $productData);
        $this->prepareStockInventoryData($product, $productData);
        $this->prepareCustomAttributesData($product, $productData);
        try {
            $newProduct = $this->productRepository->save($product);
            return $newProduct->getId();
        } catch (Exception $e) {
            $this->logWriter->writeLog($product->getSku() . " Status: " . $productData['sw_status'] . ' ' . $e->getMessage());

            return false;
        }

        //$categories = $this->prepareCategories($productData);
        //$this->assignProductCategories($newProduct->getSku(), $categories);

    }

    public function isProductExist($sku)
    {
        try {
            $product = $this->productRepository->get($sku);
            return true;
        } catch (Exception $e) {
            return false;
        }

        if ($product->getId()) {
            return true;
        } else {
            return false;
        }
    }

    public function prepareProductName(&$productData, $isConfigurable = false)
    {
        $nameArray = [];
        if ($productData['brand']) {
            $nameArray[] = $productData['brand'];
        }
        if ($productData['brand_collection']) {
            $nameArray[] = $productData['brand_collection'];
        }
        //$name = $productData['brand_collection'];
        if ($productData['short_description']) {
           // $nameArray[] = $productData['short_description'];
        }

        if ($productData['matt_firmness']) {
            $nameArray[] = $productData['matt_firmness'];
        }
		if (!($isConfigurable)){
			if ($productData['matt_size']) {
				$this->replaceData($productData);
				$nameArray[] = $productData['matt_size']; 
			}
		}

		
        $productData['name'] = implode(' ', $nameArray);
        $productData['name'] = str_replace("Sealy Stearns and Foster", "Stearns and Foster", $productData['name']);
    }

    protected function replaceData(&$productData)
    {
        $replaceData = [
            'comfort' => [
                1 => 'Very Firm',
                2 => 'Firm',
                3 => 'Medium',
                4 => 'Soft', 
                5 => 'Very Soft'
            ],
            'matt_size' => [
                'CA. King &amp; Eastern King' => 'CA. King & Eastern King',
                'Cal King/ East King' => 'Cal King / East King',
                'Calfornia King' => 'California King',
                'CALIFORNIA King' => 'California King',
                "Ful" => 'Full',
                'Ful XL' => 'Full XL',
                'Full Xl' => 'Full XL',
                'Full/ Queen' => 'Full / Queen',
                'Full/Full' => 'Full / Full',
                'Full/Queen' => 'Full / Queen',
                'QUEEN' => 'Queen',
                'Queen / East king' => 'Queen / East King',
                'Queen/ East King' => 'Queen / East King',
                'Queen/East King' => 'Queen / East King',
                'Queen/Eastern King' => 'Queen / Eastern King',
                'Twin Xl' => 'Twin XL',
                'Twin XL / Easttern King' => 'Twin XL / Eastern King',
                'Twin XL/ East King' => 'Twin XL / East King',
                'Twin XL/East King' => 'Twin XL / East King',
                'Twin Xl/Eastern King' => 'Twin XL / Eastern King',
                'Twin XL/Full XL' => 'Twin XL / Full XL',
                'Twin XL/Twin XL' => 'Twin XL / Twin XL',
                'Twin/Full' => 'Twin / Full',
                'Twin/Full XL' => 'Twin / Full XL',
                'Twin/Twin' => 'Twin / Twin',
                'Twin/Twin XL' => 'Twin / Twin XL'
            ]
        ];

        foreach ($replaceData as $code => $data) {
            if (isset($productData[$code])) {
                if (isset($data[$productData[$code]])) {
                    $productData[$code] = $data[$productData[$code]];
                }
            }
        }

        $productData['sw_type'] = $productData['custom_a'];

        return $productData;
    }

    public function prepareBaseData(&$product, &$productData, $isConfigurable = false)
    {
        $sku = $productData['sku'];
        $baseAttributes = array('sku', 'status', 'name', 'price', 'special_price', 'cost', 'weight', 'short_description', 'description');
        //$sku = $productData['sku'];
        /*if($productData['status'] != \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED){
            $productData['status'] = \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED;
        }*/
        $webDisplay = intval($productData['status']);
        $productData['status'] = Status::STATUS_ENABLED;
		
		$url = preg_replace('#[^0-9a-z]+#i', '-', $productData['name']);
		if (!($isConfigurable)&&($productData['parent_sku'])){
			$addSkuToUrl = strtolower(preg_replace('#[^0-9a-z]+#i', '-', $productData['sku']));
			$product->setUrlKey($url . '-' . $addSkuToUrl);
		}
        $productData['price'] = floatval($productData['price']);
        if ($productData['price'] <= 0) {
            $productData['status'] = Status::STATUS_DISABLED;
            $productData['price'] = 0;
        }

        if (in_array($productData['sw_status'], array('DV', 'SDV', 'X', 'DO'))) {
            $productData['status'] = Status::STATUS_DISABLED;
        }

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

        if ($productData['price'] == 0) {
            $productData['special_price'] = '';
        }
        $productData['cost'] = $productData['cost'] ? floatval($productData['cost']) : '';
        foreach ($baseAttributes as $code) {
            if (isset($productData[$code])) {
                $product->setData($code, $productData[$code]);
                unset($productData[$code]);
            }
        }
        if ($webDisplay == 0) {
            if ($productData['parent_sku']) {
				$product->setVisibility(Visibility::VISIBILITY_IN_SEARCH);
			}
        } else {
            $product->setVisibility(Visibility::VISIBILITY_BOTH);
        }


        $websiteIds = array_keys($this->storeManager->getWebsites());
        $product->setWebsiteIds($websiteIds);
		
		if ($productData['brand']){
			$productData['manufacturer'] = $productData['brand'];
		}
        
        foreach($productData['web_collections'] as $webcoll){
        //if(stripos($productData['web_collections'], "Mattresses") !== false){
       // $webCollectionsArray = explode('&', $webcoll);
        $featuredKey = array_search('Featured', $webcoll);
        if ($featuredKey) {
            unset($webCollectionsArray[$featuredKey]);
        }
        $webCollections = implode("&", $webcoll);
        if (in_array($webCollections, array('Mattresses', 'Mattresses&Adjustable Mattresses', 'Bed in a box'))) {
            $attributeSetName = "Mattress";
        } else {
            $attributeSetName = "Furniture";
            $productData['furniture_vendor'] = $productData['brand'];
            $productData['brand'] = '';
            /*if(empty($productData['sw_length']) || empty($productData['sw_length']) || empty($productData['sw_length'])){
                if($product->getStatus() != \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED){
                    $this->logWriter->writeLog($sku.' doesn\'t have full deminsions');
                }

            }*/
        }
    }
		if (!empty($productData['sku_serial'])) {
            $product->setUpcCode($productData['sku_serial']);
        }
        $attributeSetId = $this->getAttributeSetId($attributeSetName);
        $product->setAttributeSetId($attributeSetId);

        return $product;

    }

    public function getAttributeSetId($attributeSetName)
    {
        if (empty($this->attributeSets)) {
            //$this->searchCriteria->setPageSize(20);
            //$this->searchCriteria->setCurrentPage(1);
            //$attributeSets = $this->attributeSetRepository->getList($this->searchCriteria);
            $attributeSets = $this->attributeSetOptions->toOptionArray();
            foreach ($attributeSets as $attributeSet) {
                $this->attributeSets[] = array(
                    'attribute_set_name' => $attributeSet['label'],
                    'attribute_set_id' => $attributeSet['value']
                );
            }
        }
        foreach ($this->attributeSets as $attributeSet) {
            if ($attributeSetName == $attributeSet['attribute_set_name']) {
                return $attributeSet['attribute_set_id'];
            }
        }

        return null;
    }

    public function prepareGalleryData(&$product, $productData)
    {
        return $this;
        if (empty($productData['sku_serial'])) {
            return $this;
        }
        $baseImageUrl = 'https://web.sleepworld.com/images/';
        $imageUrl = $baseImageUrl . $productData['sku_serial'] . '/' . $productData['sku_serial'] . '.jpg';
        try {
            $imageContent = file_get_contents($imageUrl);
        } catch (Exception $e) {
            return $this;
        }

        if (!$imageContent) {
            return $this;
        }
        $imageType = exif_imagetype($imageUrl);
        $mimeType = image_type_to_mime_type($imageType);
        $extension = image_type_to_extension($imageType);
        $contentArray = [
            ImageContentInterface::BASE64_ENCODED_DATA => base64_encode($imageContent),
            ImageContentInterface::TYPE => $mimeType,
            ImageContentInterface::NAME => $productData['sku_serial'] . $extension
        ];
        //$galleryEntry = $this->galleryEntryFactory->create();
        /*$galleryEntry->setMediaType(\Magento\Catalog\Model\Product\Attribute\Backend\Media\ImageEntryConverter::MEDIA_TYPE_CODE)
                    ->setType(['image'])
                    ->setDisabled(false)
                    ->setContent($contentArray);*/
        $galleryEntry = [
            'media_type' => ImageEntryConverter::MEDIA_TYPE_CODE,
            'label' => '',
            'position' => 1,
            'types' => ['image', 'small_image', 'thumbnail'],
            'disabled' => false,
            'content' => $contentArray
        ];

        $product->setData('media_gallery_entries', [$galleryEntry]);
        return $product;
    }

    public function prepareStockInventoryData(&$product, $productData)
    {
        $stockItem = $this->stockItemFactory->create();
        $stockItem->setManageStock(true);
        $stockItem->setQty(50);
        $stockItem->setIsInStock(true);

        if (isset($productData['qty'])) {
            $stockItem->setQty(intval($productData['qty']));
            if ($stockItem->getQty() <= 0) {
                $stockItem->setIsInStock(false);
            }
        }
        $product->getExtensionAttributes()->setStockItem($stockItem);
        return $product;
    }

    public function prepareCustomAttributesData(&$product, $productData)
    {
        $customAttributes = [];
        foreach ($productData as $code => $text) {
            $attributeType = $this->getAttributeType($code);
            if (in_array($attributeType, ['text', 'price', 'textarea'])) {
                $customAttributes[] = array(
                    'attribute_code' => $code,
                    'value' => $text
                );
            } elseif ($attributeType == 'boolean') {
                $customAttributes[] = array(
                    'attribute_code' => $code,
                    'value' => $text == 1 ? 1 : 0
                );
            } elseif ($attributeType == 'select') {
                if (strtolower($text) == 'n/a') {
                    $text = '';
                }
                $optionValue = $this->getAttributeOptionValue($code, $text);
                if ($optionValue) {
                    $customAttributes[] = array(
                        'attribute_code' => $code,
                        'value' => $optionValue
                    );
					var_dump($code." = ".$optionValue);
                }

            }
        }
        foreach ($customAttributes as $customAttribute) {
            $product->setData($customAttribute['attribute_code'], $customAttribute['value']);
        }
        //$product->setCustomAttributes($customAttributes);
        return $product;
    }

    public function getAttributeType($attributeCode)
    {
        if (!isset($this->attributeTypeList[$attributeCode])) {
            try {
                $attribute = $this->productAttributeRepository->get($attributeCode);
                $this->attributeTypeList[$attributeCode] = $attribute->getFrontendInput();
            } catch (Exception $e) {
                return null;
            }

        }

        return $this->attributeTypeList[$attributeCode];
    }

    public function getAttributeOptionValue($code, $optionLabel)
    {
        $optionLabel = trim($optionLabel);
        if (empty($optionLabel) || strlen($optionLabel) > 200) {
            return null;
        }
        if (!isset($this->attributesOptions[$code])) {
            $options = $this->productAttributeOptionManagement->getItems($code);
            foreach ($options as $option) {
                $this->attributesOptions[$code][] = array(
                    'label' => $option->getLabel(),
                    'value' => $option->getValue()
                );
            }
        }
        $optionValue = null;
        foreach ($this->attributesOptions[$code] as $option) {
            if ($option['label'] == $optionLabel) {
                $optionValue = $option['value'];
            }
        }

        if ($optionValue != null) {
            return $optionValue;
        }
        $newOption = $this->optionFactory->create();
        $newOption->setLabel($optionLabel);
        $result = $this->productAttributeOptionManagement->add($code, $newOption);
        if ($result) {
            unset($this->attributesOptions[$code]);
            //var_dump($optionLabel);
            return $this->getAttributeOptionValue($code, $optionLabel);
        } else {
            return false;
        }


    }

    public function prepareCategories($productData)
    {
        $categories = [];
        $level = [];
        $productData = $this->solveSpecialCategoryData($productData);
        
        foreach($productData['web_collections'] as $webcoll){

            $webCollectionsArray = explode('&', $webcoll['name']);
            $featuredKey = array_search('Featured', $webCollectionsArray);
            if ($featuredKey) {
                unset($webCollectionsArray[$featuredKey]);
            }
            $webCollections = implode("&", $webCollectionsArray);

            if (stripos($webCollections, "Mattresses") !== false) {
                $level[] = $webCollections;
                $level[] = $productData['brand'];

                if ($productData['brand'] == 'Beautyrest' && stripos($productData['brand_collection'], 'Black') !== false) {
                    $level[] = 'Beautyrest Black';
                }
            } else {
                $level[] = 'Furniture';
                if ($webCollections) {
                    if (stripos($webCollections, "&") !== false) {
                        $tempWebCollections = explode('&', $webCollections);
                        $key = array_search('Furniture', $tempWebCollections);
                        if ($key !== false) {
                            unset($tempWebCollections[$key]);
                            $webCollections = implode('&', $tempWebCollections);
                        }
                    }
                    $level[] = $webCollections;
                } else {
                    $level[] = 'Misc.';
                }
                if ($productData['custom_a']) {
                    $level[] = $productData['custom_a'];
                }
            }
        }
        //var_dump($level);
        $lines = [];
        $line = [];
        $maxCount = 0;
        foreach ($level as $k => $name) {
            if (stripos($name, "&") !== false) {

                $sameLevelCategories = explode('&', $name);
                $sameLevel = [];
                foreach ($sameLevelCategories as $sameLevelName) {
                    //$maxCount++;
                    //$categories[$k][] = $this->replaceSpecialCategoryName($sameLevelName, $k);
                    $sameLevel[] = $this->replaceSpecialCategoryName($sameLevelName, $k);
                }
                $result = array_unique($sameLevel);
                $d = 0;
                //var_dump($result);
                $levelNames = [];
                foreach ($result as $n) {
                    $levelNames[$d] = $n;
                    $d++;
                }
                //var_dump($result);
                if (count($levelNames) == 1) {
                    $categories[$k] = $levelNames[0];
                } else {
                    $categories[$k] = $levelNames;
                }

                if ($maxCount == 0) {
                    $maxCount = count($levelNames);
                } else {
                    $maxCount = $maxCount * count($levelNames);
                }


            } else {
                $categories[$k] = $this->replaceSpecialCategoryName($name, $k);
            }
        }
        //var_dump($maxCount);
        //var_dump($categories);
        //echo "------------\n";
        $categoriesArray = [];
        if ($maxCount >= 2) {
            //var_dump($categories);
            $categoriesArray = $this->getFormatCategories($categories);
            /*echo "000000000000"."\n";
            var_dump($data);
            exit();
            $i = 0;
            $stop = false;
            $array = [];
            foreach($categories as $level=>$data){

                if(is_array($data)){
                    if(!$stop){
                        $i = 0;
                        foreach($data as $k=>$v){
                            $array[$i][$level] = $v;
                            $i++;
                        }
                        $stop = true;
                    }else{
                        if(count($array) > 1){
                            for($j=0;$j < count($array);$j++){
                                $array[$j][$level] = $data;
                            }
                        }
                    }

                }

            }

            var_dump($array);
            exit();
            for($i=0;$i<count($maxCount);$i++){
                $i=0;
                foreach($categories as $level=>$data){
                    if(is_array($data)){

                        foreach($data as $k=>$v){
                            $categoriesArray[$i][$level]= $v;
                            $i++;
                        }

                    }else{
                        $categoriesArray[$i][$level]=$data;
                        $i++;
                    }
                    //$i++;
                }
            }*/
        } else {
            $categoriesArray[] = $categories;
        }
        foreach($productData['web_collections'] as $webcoll){
        if (stripos($webcoll['name'], "Featured") !== false) {
            $categoriesArray[] = array('Featured');
        }
        if (stripos($webcoll['name'], "Foundations") !== false) {
            $categoriesArray[] = array('All Foundations');
        }
        if (stripos($webcoll['name'], "Mattresses") !== false) {
            $categoriesArray[] = array('All Mattresses');
        }
        if ($webcoll['name'] == "Adjustable Foundations") {
            if (stripos($productData['sku'], 'SET') !== false) {
                $categoriesArray[] = array('Adjustable Base Sets');
            } else {
                $categoriesArray[] = array('Adjustable Bases');
            }
        }
        }
        //var_dump($categoriesArray);
        return $categoriesArray;
    }

    public function solveSpecialCategoryData($productData)
    {
        if ($productData['brand'] == 'Aireloom Bedding Company') {
            $productData['brand'] = 'Aireloom';
        }

        if ($productData['brand'] == 'Aireloom' && $productData['brand_collection'] == 'Kluft') {
            $productData['brand'] = $productData['brand'] . '&Kluft';
        }

        if ($productData['brand'] == 'Sleepworld Designs' && $productData['brand_collection'] == 'Stratus') {
            $productData['brand'] = $productData['brand'] . '&Stratus';
        }

        // if($productData['brand'] == 'Sealy' && $productData['brand_collection'] == 'Stearns and Foster'){
        //     $productData['brand'] = $productData['brand'].'&Stearns and Foster';
        // }
        foreach($productData['web_collections'] as $webcoll){
            if (stripos($webcoll['name'], "Mattresses") !== false && $productData['brand'] == 'Sealy'
                && stripos($productData['brand_collection'], "Stearns") !== false) {
                $productData['brand'] = 'Stearns and Foster';
            }
        }

        if ($productData['brand'] == 'Serta' && $productData['brand_collection'] == 'iComfort') {
            $productData['brand'] = $productData['brand'] . '&iComfort';
        }

        return $productData;
    }

    public function replaceSpecialCategoryName($name, $level = null)
    {
        $pillowAndBedding = ['Pillows', 'Pillow', 'Bedding', 'Bedding&Pillows', 'PillowsandBedding'];
        if (in_array($name, $pillowAndBedding) && $level == 1) {
            $name = "Pillows & Bedding";
        }

        return $name;
    }

    public function getFormatCategories($levelCategories)
    {

        $stop = false;
        $array = [];
        $i = 0;
        $hasSameLevel = false;
        $formatedArray = [];
        foreach ($levelCategories as $level => $data) {

            if (is_array($data)) {
                if (!$stop) {
                    foreach ($data as $k => $v) {
                        $array[$i] = array_merge($formatedArray, array($v));
                        $i++;
                    }
                    $stop = true;
                } else {
                    $hasSameLevel = true;
                    if (count($array) > 1) {
                        for ($j = 0; $j < count($array); $j++) {
                            $array[$j][] = $data;
                        }
                    }
                }

            } else {
                if (count($array) > 1) {
                    for ($j = 0; $j < count($array); $j++) {
                        $array[$j][] = $data;
                    }
                } else {
                    $formatedArray[] = $data;
                }

            }

        }
        if (empty($array)) {
            $array = $formatedArray;
        }
        if ($hasSameLevel) {
            $categories = [];
            foreach ($array as $levelItems) {
                $temp = $this->getFormatCategories($levelItems);
                $categories = array_merge($categories, $temp);
            }
            return $categories;
        } else {
            return $array;
        }
    }

    public function unassignProductCategories($sku = null)
    {
        if ($sku == null) {
            $productCollection = $this->productFactory->create()->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('visibility', array('IN' => array(Visibility::VISIBILITY_NOT_VISIBLE, Visibility::VISIBILITY_IN_SEARCH)));
            if ($productCollection->getSize() > 0) {
                foreach ($productCollection as $item) {
                    $categoryIds = $item->getResource()->getCategoryIds($item);
                    if (!empty($categoryIds)) {
                        foreach ($categoryIds as $categoryId) {
                            $this->categoryLinkRepository->deleteByIds($categoryId, $item->getSku());
                        }
                    }

                }
            }
        } else {
            try {
                $product = $this->productRepository->get($sku);
                $categoryIds = $product->getResource()->getCategoryIds($product);
                if (!empty($categoryIds)) {
                    foreach ($categoryIds as $categoryId) {
                        $this->categoryLinkRepository->deleteByIds($categoryId, $product->getSku());
                    }
                }
            } catch (Exception $e) {

            }

        }

    }

    public function assignProductCategories($sku, $categories = array())
    {
        if (empty($categories) || !$sku) {
            return;
        }
        //$this->unassignProductCategories($sku);
        if (!$this->canAssignCategory($sku)) {
            return;
        }

        $categoryIds = $this->getCagetoryIds($categories);
        if (empty($categoryIds)) {
            $this->logWriter->writeLog($sku . ' has not category');
        }
        try {
            $product = $this->productRepository->get($sku);
            $product->setCategoryIds($categoryIds);
            $product->save();
            //$this->categoryLinkManagement->assignProductToCategories($sku, $categoryIds);
        } catch (Exception $e) {
            $this->logWriter->writeLog("Category Error: " . $sku . ' ' . $e->getMessage());
        }

    }

    public function canAssignCategory($sku)
    {
        try {
            $product = $this->productRepository->get($sku);
        } catch (Exception $e) {
            return false;
        }
        $visibility = $product->getVisibility();
        if (in_array($visibility, array(Visibility::VISIBILITY_NOT_VISIBLE))) {
            return false;
        } else {
            return true;
        }
    }

    public function getCagetoryIds($categoriesArray)
    {
        if (empty($categoriesArray)) {
            return null;
        }

        if (!$this->categoryTree) {
            $this->categoryTree = $this->categoryManagement->getTree(2, 4);
        }
        $categoryIds = [];
        foreach ($categoriesArray as $k => $categories) {
            $parentId = $this->categoryTree->getId();
            $level = $this->categoryTree->getLevel();
            $categoryId = null;
            $categoryNode = null;
            foreach ($categories as $categoryName) {
                $category = $this->getCategoryByNameParentId(trim($categoryName), $parentId);
                if ($category->getId()) {
                    $categoryId = $category->getId();
                    $parentId = $category->getId();
                    $level = $category->getLevel();
                } else {
                    $newCategory = $this->createCategory(trim($categoryName), $parentId, $level + 1);
                    $categoryId = $newCategory->getId();
                    $parentId = $newCategory->getId();
                    $level = $newCategory->getLevel();
                }

            }
            $categoryIds[] = $categoryId;
        }
        /*foreach($this->categoryTree->getChildrenData() as $node){
            if($node->getName() == $categories[0]){
                $categoryNode = $node;
            }
        }
        if($categoryNode == null){
            $currentCategory = $this->createCategory($categories[0], $parentId, 2);
            if(isset($categories[1])){
                $currentCategory = $this->createCategory($categories[1], $currentCategory->getId(), 3);
            }
            $categoryId = $currentCategory->getId();
        }else{
            if(isset($categories[1])){
                $childNode = null;
                foreach($categoryNode->getChildrenData() as $node){
                    if($node->getName() == $categories[1]){
                        $childNode = $node;
                    }
                }
                if($childNode == null){
                    $newCategory = $this->createCategory($categories[1], $categoryNode->getId(), 3);
                    $categoryId = $newCategory->getId();
                }else{
                    $categoryId = $childNode->getId();
                }
            }else{
                $categoryId = $categoryNode->getId();
            }
        }*/

        return $categoryIds;

    }

    public function getCategoryByNameParentId($name, $parentId)
    {
        $category = $this->categoryFactory->create();
        $collection = $category->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('name', $name)
            ->addAttributeToFilter('parent_id', $parentId);
        $item = $collection->getFirstItem();
        return $item;
    }

    protected function createCategory($name, $parentId, $level)
    {
        $category = $this->categoryFactory->create()->load(null);
        $category->setParentId($parentId);
        $category->setName($name);
        $category->setIsActive(true);
        $category->setIncludeInMenu(true);
        try {
            $category = $this->categoryRepository->save($category);
        } catch (Exception $e) {
            $this->logWriter->writeLog("Create Category: " . $name . ' ' . $parentId . " " . $level . ' ' . $e->getMessage());
        }

        //$this->categoryManagement->getTree($category->getId(), 4);
        //$this->_cache->clean([\Magento\Catalog\Model\Category::CACHE_TAG]);
        //var_dump($this->categoryManagement->getCount());
        //$this->categoryTree = null;
        return $category;
    }

    public function addRelatedProducts($linkedSku, $skus)
    {
        if (empty($skus)) {
            return;
        }
        $links = [];
        foreach ($skus as $sku) {
            if (!$this->isProductExist($sku)) {
                continue;
            }
            $link = $this->productLinkFactory->create();
            $link->setSku($linkedSku);
            $link->setLinkType('related');
            //$link->setLinkedProductType();
            $link->setLinkedProductSku($sku);
            $links[] = $link;
        }

        if (empty($links)) {
            return;
        }
        try {
            $this->productLinkManagement->setProductLinks($linkedSku, $links);
        } catch (Exception $e) {
            //$this->logWriter->writeLog("Related Product:".$linkedSku.' '.$e->getMessage());
        }

    }

    public function processConfigurableProducts($parentSku, $childSkus, $productData)
    {
        if (empty($parentSku) || empty($childSkus)) {
            return;
        }

        $childSkus[] = $parentSku;
        try {
            $product = $this->productRepository->get($parentSku, true, null, true);
        } catch (Exception $e) {
            return false;
        }
        $superAttributes = $this->getSuperAttributesByAttributeSet($product->getAttributeSetId());
        foreach ($superAttributes as $k => $attributeCode) {
            if (!$product->getData($attributeCode)) {
                unset($superAttributes[$k]);
            }
        }
        if (empty($superAttributes)) {
            return false;
        }
        $childProducts = [];
        $enabledSimpleProducts = [];
        foreach ($childSkus as $childsku) {
            try {
                $childProduct = $this->productRepository->get($childsku, false, null, true);
                if ($childProduct->getStatus() == Status::STATUS_ENABLED) {
                    $enabledSimpleProducts[$childProduct->getId()] = $childProduct;
                }
                $childProducts[$childProduct->getId()] = $childProduct;
                //}
            } catch (Exception $e) {
                continue;
            }
        }
        $configStatus = Status::STATUS_ENABLED;
        if (empty($enabledSimpleProducts)) {
            //return false;
            $configStatus = Status::STATUS_DISABLED;
        }

        $configSku = $parentSku . '-' . 'CONFIG';
        try {
            $configProduct = $this->productRepository->get($configSku);
        } catch (Exception $e) {
            $configProduct = null;
        }

        if ($configProduct == null) {

            $configProduct = $this->productFactory->create()->load(null);

        }//else{
		$isConfigurable = true;
        $this->prepareProductName($productData,$isConfigurable);
        $productData['sku'] = $configSku;
        $this->replaceData($productData);
        $configProduct->setTypeId('configurable');
        $this->prepareBaseData($configProduct, $productData,$isConfigurable);
        //$this->prepareGalleryData($product, $productData);
        //$this->prepareStockInventoryData($configProduct, $productData);
        $this->prepareCustomAttributesData($configProduct, $productData);


        //$configProduct->setStatus($product->getStatus());
        $stockItem = $this->stockItemFactory->create();
        $stockItem->setManageStock(false);
        $stockItem->setIsInStock(true);
        $configProduct->getExtensionAttributes()->setStockItem($stockItem);
        /*$customAttributes = $product->getCustomAttributes();
        foreach($customAttributes as $customAttribute){
            if(in_array($customAttribute->getAttributeCode(),['category_ids','options_container','require_options','has_options','url_key'])){
                continue;
            }
            $configProduct->setData($customAttribute->getAttributeCode(), $customAttribute->getValue());
        }
        $configProduct->setAttributeSetId($product->getAttributeSetId());*/
        //}
        $websiteIds = array_keys($this->storeManager->getWebsites());
        $configProduct->setWebsiteIds($websiteIds);


        //if(in_array($productData['sw_status'], array('DV','SDV','X'))){
        $configProduct->setStatus($configStatus);
        //}
        $configProduct->setTypeId('configurable');
        $this->prepareGalleryData($configProduct, $productData);
        if (empty($enabledSimpleProducts)) {
            $newProduct = $this->productRepository->save($configProduct);
            return $newProduct->getSku();
        }
        $options = [];
        $sameValue = [];
        $sameValueSku = '';
        //var_dump($superAttributes);
        foreach ($superAttributes as $attributeCode) {
            $attribute = $this->productAttributeRepository->get($attributeCode);
            $option = $this->configurableOptionFactory->create();
            $option->setAttributeId($attribute->getId());
            $option->setLabel($attribute->getDefaultFrontendLabel());
            $option->setIsUseDefault(false);
            $values = [];
            $valueIndexArray = [];
            //var_dump(array_keys($childProducts));
            foreach ($enabledSimpleProducts as $id => $child) {
                if ($child->getData($attributeCode)) {
                    $valueIndexArray[] = $child->getData($attributeCode);
                    $sameValue[$id][] = $child->getData($attributeCode);
                    //$sameValueSku = $child->getSku();
                } else {
                    unset($enabledSimpleProducts[$id]);
                    /*if(in_array($child->getData($attributeCode), $sameValue)){
                        echo $child->getSku().' have same attribute Value'."\n";
                    }*/
                }

            }
            $valueIndexArray = array_unique($valueIndexArray);
            foreach ($valueIndexArray as $valueIndex) {
                $optionValue = $this->configurableOptionValueFactory->create();
                $optionValue->setValueIndex($valueIndex);
                $values[] = $optionValue;
            }
            $option->setValues($values);
            $options[] = $option;
        }
        //var_dump(array_keys($childProducts));
        $sameIds = [];
        //var_dump($sameValue);
        //$unique = array_unique($sameValue);
        //var_dump($unique);
        $tempSameValue = $sameValue;
        foreach ($sameValue as $id => $optionValues) {
            foreach ($tempSameValue as $tempId => $tempValues) {
                if ($id != $tempId && $optionValues == $tempValues) {
                    if (isset($enabledSimpleProducts[$id])) {
                        //var_dump($id);
                        //echo $enabledSimpleProducts[$id]->getSku().' have same attribute Value'."\n";
                        $this->logWriter->writeLog('Configurable Error:' . $enabledSimpleProducts[$id]->getSku() . ' have same attribute value with ' . $enabledSimpleProducts[$id]->getSku());
                        unset($tempSameValue[$id]);
                        unset($enabledSimpleProducts[$id]);
                    }

                }
            }
        }
        /*foreach($sameIds as $id){
            if(isset($enabledSimpleProducts[$id])){

            }
        }*/
//var_dump(count($enabledSimpleProducts));
        try {
            $configProduct->getExtensionAttributes()->setConfigurableProductOptions($options);
            $childProductIds = [];
            foreach ($enabledSimpleProducts as $id => $child) {
                $childProductIds[] = $id;
                $child->setVisibility(Visibility::VISIBILITY_NOT_VISIBLE);
                $child->save();
            }
            $configProduct->getExtensionAttributes()->setConfigurableProductLinks($childProductIds);
            $configProduct = $this->productRepository->save($configProduct);
            /*if($configProduct->getId()){
                $this->_productPriceIndexerProcessor->reindexRow($configProduct->getId());
            }*/
        } catch (Exception $e) {
            $this->logWriter->writeLog($configProduct->getSku() . " " . $e->getMessage());
            //echo $e->getMessage()."\n";
        }
        return $configProduct->getSku();
    }

    protected function getSuperAttributesByAttributeSet($attributeSetId)
    {
        $attributeSet = $this->attributeSetRepository->get($attributeSetId);
        $superAttributes = [];
        if ($attributeSet->getAttributeSetName() == 'Mattress') {
            $superAttributes = ['matt_size'];
        } elseif ($attributeSet->getAttributeSetName() == 'Furniture') {
            $superAttributes = ['matt_size', 'finish_color'];
        }
        return $superAttributes;
    }

    public function proccessParentSkuNotExists($parentSku, $childSkus)
    {
        if (!$this->isProductExist($parentSku)) {
            foreach ($childSkus as $childsku) {
                try {
                    $childProduct = $this->productRepository->get($childsku, false, null, true);
                    $childProduct->setStatus(Status::STATUS_DISABLED);
                    $childProduct->save();
                    $this->logWriter->writeLog('PARENT SKU ERROR: Can not find parent sku ' . $parentSku . '. Disable child product ' . $childsku);
                } catch (Exception $e) {
                    continue;
                }
            }
        }
    }

    public function updateVisibility($sku, $productData)
    {
        if (intval($productData['status']) == 0) {
            $visibility = Visibility::VISIBILITY_IN_SEARCH;
        } else {
            $visibility = Visibility::VISIBILITY_BOTH;
        }

        $this->saveProductAttribute($sku, 'visibility', $visibility);
    }

    public function saveProductAttribute($sku, $code, $value)
    {

        try {
            $product = $this->productRepository->get($sku);
            $product->setData($code, $value);
            $product->getResource()->saveAttribute($product, $code);
        } catch (Exception $e) {
            //throw new \Exception($e->getMessage());
            echo $sku . '  ' . $e->getMessage() . "\n";
        }

    }

    public function getProductCategoryIds($sku)
    {
        try {
            $product = $this->productRepository->get($sku);
        } catch (Exception $e) {
            //throw new \Exception($e->getMessage());
            //echo $sku.'  '.$e->getMessage()."\n";
            return false;
        }

        return $product->getResource()->getCategoryIds($product);
    }

    public function updateAttributeSet($sku, $productData)
    {
        if (in_array($productData['web_collections'], array('Mattresses', 'Mattresses&Adjustable Mattresses', 'Bed in a box'))) {
            $attributeSetName = "Mattress";
        } else {
            $attributeSetName = "Furniture";
        }
        $attributeSetId = $this->getAttributeSetId($attributeSetName);
        var_dump($attributeSetId);
        var_dump($sku);

        try {
            //$this->saveProductAttribute($sku, 'new_attribute_set_id', $attributeSetId);
            $product = $this->productRepository->get($sku, true, null, true);
            //$product->setData('new_attribute_set_id', $attributeSetId);
            $product->setAttributeSetId($attributeSetId);
            $product->setNewVariationsAttributeSetId($attributeSetId);
            //echo $product->getSku();
            $this->productRepository->save($product);
        } catch (Exception $e) {
            //throw new \Exception($e->getMessage());
            echo $sku . '  ' . $e->getMessage() . "\n";
        }

    }

}


?>
