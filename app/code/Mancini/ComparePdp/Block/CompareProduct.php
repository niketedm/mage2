<?php

/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Mancini\ComparePdp\Block;

class CompareProduct extends \Magento\Framework\View\Element\Template
{

    /**
     * @var attributeSet
     */
    protected $attributeSet;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var Repository
     */
    protected $_productRepository;

    /**
     * @var CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $_priceHelper;

    /**
     * @var StoreManagerInterface
     */
    protected $_imageHelper;

    /**
     * @var ListBlock
     */
    protected $_listBlock;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\ProductRepository $productRepository,
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param \Magento\Catalog\Helper\Image $helperImage
     * @param \Magento\Catalog\Block\Product\ListProduct $listBlock
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet,
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Catalog\Block\Product\ListProduct $listBlock,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable,
        array $data = []
    ) {
        $this->_registry                    =   $registry;
        $this->_priceHelper                 =   $priceHelper;
        $this->_productCollectionFactory    =   $productCollectionFactory;
        $this->_productRepository           =   $productRepository;
        $this->_imageHelper                 =   $imageHelper;
        $this->_listBlock                   =   $listBlock;
        $this->attributeSet                 =   $attributeSet;
        $this->configurable                 =   $configurable;    
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getCompareProducts($productId)
    {
        $comparePrds    =   $this->assignProducts($productId);

        return  $comparePrds;
    }
    /**
     * Function to retrieve the current product from registry
     * @return object
     */
    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }
    /**
     * Function to retrieve on initial load
     * @return array
     */
    public function getInitialCompareProducts()
    {
        $product        =   $this->getCurrentProduct();
        $productId      =   $product->getId();
        $comparePrds    =   $this->assignProducts($productId);

        return  $comparePrds;
    }

    /**
     * Function to get the products
     */

    public function assignProducts($productId)
    {
        $product                =   $this->_productRepository->getById($productId);
        $productType            =   $product->getTypeId();
        
        $attributeSetRepository =   $this->attributeSet->get($product->getAttributeSetId());
        $attributeSetName       =   $attributeSetRepository->getAttributeSetName();
        $attributeSetId         =   $product->getAttributeSetId();

        $comfort                =   $result =    $firmness =  $construction = '';
        $size                   =   $compareProduct =  array();
        $filterPrice            =   $minPrice  =    $maxPrice    =  $i = $minCount   =    $maxCount  =   0;
        $compareProd            =   $compareProducts  = array();
        $counter                =   0;
        $collection             =   $this->_productCollectionFactory->create();
        $weightUnit             =   $this->getWeightUnit();


        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
        $collection->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        $collection->addAttributeToFilter('attribute_set_id', $attributeSetId);

        if ($attributeSetName == "Mattress") {
            if ($product->getComfort()) {
                $comfort = $product->getResource()->getAttribute('comfort')->getFrontend()->getValue($product);
                $collection->addAttributeToFilter('comfort', ['eq' => $product->getComfort()]);
            }
            if ($product->getFirmness())
                $firmness = $product->getResource()->getAttribute('firmness')->getFrontend()->getValue($product);
        }

        if ($product->getType())
            $construction = $product->getResource()->getAttribute('type')->getFrontend()->getValue($product);

        if ($product->getSize() && $productType != 'configurable') {
            $collection->addAttributeToFilter('size', ['in' => $product->getSize()]);
            //calculate filter price
            $filterPrice    =    $product->getFinalPrice();
        }
        // First product is the main product
        $imageUrl   =   $this->_imageHelper->init($product, 'product_page_image_small')
            ->setImageFile($product->getThumbnail()) // image,small_image,thumbnail
            ->resize(380)
            ->getUrl();
        $addToCartUrl =   $this->_listBlock->getAddToCartUrl($product);

        $dimension="";
        if ($product->getLength() && $product->getWidth()){
            $dimension = $product->getLength().'"' . " X " . $product->getWidth().'"';
        }

        $compareProd[$counter]['id']             =   $productId;
        $compareProd[$counter]['size']           =   $product->getSize();
        $compareProd[$counter]['name']           =   $product->getResource()->getAttribute('brand')->getFrontend()->getValue($product). " " . $product->getName();
        $compareProd[$counter]['price']          =   $this->_priceHelper->currency($product->getFinalPrice(), true, false);
        $compareProd[$counter]['addtocart']      =   $addToCartUrl;
        $compareProd[$counter]['image']          =   $imageUrl;

        $configproduct = $this->configurable->getParentIdsByChild($productId);
        if(isset($configproduct[0])){
            $productParent= $this->_productRepository->getById($configproduct[0]);
            $compareProd[$counter]['url']         =   $productParent->getProductUrl(); 
          }
        else{
            $compareProd[$counter]['url']         =   $product->getProductUrl();
        }        
        $compareProd[$counter]['sku']             =   $product->getSku();

        if ($attributeSetName == "Mattress") {
            $compareProduct['price'][$i]         =   $this->_priceHelper->currency($product->getFinalPrice(), true, false);
            if ($product->getComfort()){
                $compareProduct['comfort'][$i]   =   $comfort;
            }
            if ($product->getFirmness()){
                $compareProduct['firmness'][$i]  =   (null !== $firmness) ? $firmness : '';
            }
            $compareProduct['type'][$i]          =   (null !== $construction) ? $construction : '';
            $compareProduct['height'][$i]        =   (null !== $product->getHeight()) ? $product->getHeight() : '';
            $compareProduct['weight'][$i]        =   ((null !== $product->getWeight()) ? (round($product->getWeight(), 2)." ".$weightUnit) : '');
            $compareProduct['dimension'][$i]     =   (null !== $dimension) ? $dimension : '';
        }

        if ($attributeSetName == "Furniture") {
            $compareProduct['price'][$i]         =   $this->_priceHelper->currency($product->getFinalPrice(), true, false);
            $compareProduct['type'][$i]          =   $product->getResource()->getAttribute('Furniture_Type')->getFrontend()->getValue($product);
            if($product->getHeight()){
                $compareProduct['height'][$i]    =   (null !== $product->getHeight()) ? $product->getHeight() . '"' : '';
            }
            $compareProduct['dimension'][$i]     =   (null !== $dimension) ? $dimension : '';
        }

        if ($productType == 'configurable') { //add additional checking for mattresses attribute Id
            $productTypeInstance = $product->getTypeInstance();
            $data = $productTypeInstance->getConfigurableOptions($product);
            $usedProducts = $productTypeInstance->getUsedProducts($product);

            foreach ($usedProducts  as $child) {
                if ($filterPrice >= $child->getFinalPrice() || $filterPrice == 0)
                    $filterPrice = $child->getFinalPrice();
            }

            foreach ($data as $attr) {
                foreach ($attr as $p) {
                    $size[] = $p['value_index'];
                }
            }
            $collection->addAttributeToFilter('size', ['in' => $size]);
        }

        // search criteria for price
        if ($filterPrice != 0) {
            $minPrice   =   $filterPrice - 200;
            $maxPrice   =   $filterPrice + 200;
            $collection->addFieldToFilter('price', ['from' => $minPrice, 'to' => $maxPrice]);
        }
        
        $collection->addAttributeToFilter('sku', array('neq' => $product->getSku()));
        $collection->setPageSize(3);

        foreach ($collection as $_product) {
            $i++;
            $counter++;
            $productPrice       =   $_product->getFinalPrice();
            $imageUrl           =   $this->_imageHelper->init($_product, 'product_page_image_small')
                ->setImageFile($_product->getThumbnail()) // image,small_image,thumbnail
                ->resize(380)
                ->getUrl();
            $addToCartUrl       =   $this->_listBlock->getAddToCartUrl($_product);

            if ($attributeSetName == "Mattress") {
                if ($_product->getComfort())
                    $comfort    =    $_product->getResource()->getAttribute('comfort')->getFrontend()->getValue($_product);
                if ($_product->getFirmness())
                    $firmness   =    $_product->getResource()->getAttribute('firmness')->getFrontend()->getValue($_product);
            }
            if ($_product->getType())
                $construction   =   $_product->getResource()->getAttribute('type')->getFrontend()->getValue($_product);


            if ($_product->getLength() && $_product->getWidth()){
                $dimension = $_product->getLength().'"' . " X " . $_product->getWidth() .'"';
            }

            $compareProd[$counter]['id']                =   $_product->getId();
            $compareProd[$counter]['price']             =   $this->_priceHelper->currency($_product->getFinalPrice(), true, false);
            $compareProd[$counter]['size']              =   $_product->getSize();
            $compareProd[$counter]['name']              =   $_product->getResource()->getAttribute('brand')->getFrontend()->getValue($_product). " " . $_product->getName();
            $compareProd[$counter]['addtocart']         =   $addToCartUrl;
            $compareProd[$counter]['image']             =   $imageUrl;
            $compareProd[$counter]['image']             =   $imageUrl;
            $compareProd[$counter]['url']               =   $_product->getProductUrl();
            $compareProd[$counter]['sku']               =   $product->getSku();

            if ($attributeSetName == "Mattress") {
                $compareProduct['price'][$i]            =   $this->_priceHelper->currency($_product->getFinalPrice(), true, false);
                if ($product->getComfort()){
                    $compareProduct['comfort'][$i]      =   (null !== $comfort) ? $comfort : '';
                }
                if ($product->getFirmness()){
                    $compareProduct['firmness'][$i]     =   (null !== $firmness) ? $firmness : '';
                }
                $compareProduct['type'][$i]             =   (null !== $construction) ? $construction : '';
                $compareProduct['height'][$i]           =   (null !== $_product->getHeight()) ? $_product->getHeight() . '"' : '';
                $compareProduct['weight'][$i]           =   ((null !== $_product->getWeight()) ? (round($_product->getWeight(), 2)." ".$weightUnit) : '');
                $compareProduct['dimension'][$i]        =   (null !== $dimension )? $dimension : '';
            }

            if ($attributeSetName == "Furniture") {
                $compareProduct['price'][$i]            =   $this->_priceHelper->currency($_product->getPrice(), true, false);
                $compareProduct['type'][$i]             =  $_product->getResource()->getAttribute('Furniture_Type')->getFrontend()->getValue($_product);
                if($product->getHeight()){
                    $compareProduct['height'][$i]       =   (null !== $_product->getHeight()) ? $_product->getHeight() : '';
                }
                $compareProduct['dimension'][$i]        =   (null !== $dimension) ? $dimension : '';
            }
        }
        $compareProducts[0] =  $compareProd;
        $compareProducts[1] =  $compareProduct;


        return $compareProducts;
    }

        /**
         * Function for getting unit of weight attribute of the current product
         * @return string
         */
        public function getWeightUnit()
        {
            return $this->_scopeConfig->getValue(
                    'general/locale/weight_unit',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        }
}
