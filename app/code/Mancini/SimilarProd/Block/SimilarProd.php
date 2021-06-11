<?php

/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Mancini\SimilarProd\Block;

use \WebPanda\PeopleWatching\Model\ResourceModel\View;
use Magento\Framework\Session\SessionManagerInterface;


class SimilarProd extends \Magento\Framework\View\Element\Template
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
     * @var  \WebPanda\PeopleWatching\Model\ResourceModel\View\CollectionFactory
     */
    protected $peopleviewModelFactory;

    /**
     * @var SessionManagerInterface
     */
    protected $session;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var \Mancini\Frog\Helper\ApiCall 
     */
    protected $_apiCallHelper;

    /**
     * Constructor
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\ProductRepository $productRepository,
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param \Magento\Catalog\Helper\Image $helperImage
     * @param \Magento\Catalog\Block\Product\ListProduct $listBlock
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet,
     * @param \WebPanda\PeopleWatching\Model\ResourceModel\View\CollectionFactory  $peopleviewModelFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Mancini\Frog\Helper\ApiCall $apiCallHelper
     * @param SessionManagerInterface $session
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
        View $peopleviewModelFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Mancini\Frog\Helper\ApiCall $apiCallHelper,
        SessionManagerInterface $session,
        array $data = []
    ) {
        $this->_registry                    =   $registry;
        $this->_priceHelper                 =   $priceHelper;
        $this->_productCollectionFactory    =   $productCollectionFactory;
        $this->_productRepository           =   $productRepository;
        $this->_imageHelper                 =   $imageHelper;
        $this->_listBlock                   =   $listBlock;
        $this->attributeSet                 =   $attributeSet;
        $this->peopleviewModelFactory       =   $peopleviewModelFactory;
        $this->customerSession              =   $customerSession;
        $this->_apiCallHelper               =   $apiCallHelper;
        $this->session                      =   $session;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getSimilarProducts($productId)
    {
        $similarPrds    =   $this->assignProducts($productId);
        return  $similarPrds;
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
     * Function to retrieve the customersession id
     * @return int
     */
    public function getCustomer()
    {
        $id = $this->customerSession->getCustomer()->getId();
        return $id;
    }
    /**
     * Function to retrieve on initial load
     * @return array
     */
    public function getInitialSimilarProducts()
    {
        $product        =   $this->getCurrentProduct();
        $productId      =   $product->getId();
        $similarPrds    =   $this->assignProducts($productId);

        return  $similarPrds;
    }

    /**
     * Function to get quantity of the product
     */
    public function getQuantityDetail()
    {
        //$skuSerial = $this->getCurrentProduct()->getSkuSerial();
        $params['item'] = '841230062976';
        $result = $this->_apiCallHelper->inetWebsiteItemDetail(\Zend_Http_Client::POST, $params);
        $details = json_decode($result->getBody(), true);


        $available_qt   =  $details['availabletosell'];


        return $available_qt;
    }

    /**
     * Function to get the products
     */

    public function assignProducts($productId)
    {
        $product                =   $this->_productRepository->getById($productId);
        $productId              =   $product->getId();
        $productType            =   $product->getTypeId();
        $attributeSetRepository =   $this->attributeSet->get($product->getAttributeSetId());
        $attributeSetName       =   $attributeSetRepository->getAttributeSetName();
        $attributeSetId         =   $product->getAttributeSetId();
        $comfort                =   $result =   '';
        $size                   =   $similarProduct =  array();
        $filterPrice            =   $minPrice  =    $maxPrice    =   0;
        $i = $minCount          =   $maxCount  =   0;

        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
        $collection->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        $collection->addAttributeToFilter('attribute_set_id', $attributeSetId);

        if ($product->getComfort()) {
            $comfort = $product->getComfort();
        }

        if ($product->getSize() && $productType != 'configurable') {
            $collection->addAttributeToFilter('size', ['in' => $product->getSize()]);
            //calculate filter price
            $filterPrice    =    $product->getPrice();
        }

        if ($productType == 'configurable') { //add additional checking for mattresses attribute Id
            $productTypeInstance = $product->getTypeInstance();
            $data                = $productTypeInstance->getConfigurableOptions($product);
            $usedProducts        = $productTypeInstance->getUsedProducts($product);

            foreach ($usedProducts  as $child) {
                if ($filterPrice >= $child->getPrice() || $filterPrice == 0)
                    $filterPrice = $child->getPrice();
            }

            foreach ($data as $attr) {
                foreach ($attr as $p) {
                    $size[] = $p['value_index'];
                }
            }
            $collection->addAttributeToFilter('size', ['in' => $size]);
        }

        if ($attributeSetName == "Mattress") {
            if ($product->getComfort()) {
                $comfort = $product->getComfort();
                $collection->addAttributeToFilter('comfort', ['eq' => $comfort]);
            }
        }


        // search criteria for price
        if ($filterPrice != 0) {
            $minPrice   =   $filterPrice - 200;
            $maxPrice   =   $filterPrice + 200;
            $collection->addFieldToFilter('price', ['from' => $minPrice, 'to' => $maxPrice]);
        }

        $collection->addAttributeToFilter('sku', array('neq' => $product->getSku()));
        $collection->setPageSize(15);

        foreach ($collection as $_product) {
            $productPrice           =   $_product->getPrice();
            $productFinalPrice      =   $_product->getFinalPrice();
            $productSpecialPrice    =   $_product->getSpecialPrice();
            $productId              =   $_product->getId();
            $imageUrl               =   $this->_imageHelper->init($_product, 'product_page_image_small')
                ->setImageFile($_product->getThumbnail()) // image,small_image,thumbnail
                ->resize(380)
                ->getUrl();
            $viewCount              = $this->peopleviewModelFactory->getViewCount(60, $productId, $this->session->getSessionId());
   
            if ($productPrice >= $minPrice && $productPrice <= $filterPrice && $minPrice != 0 && $minCount < 4) {
                $similarProduct[$i]['size']         =   $_product->getSize();
                $similarProduct[$i]['name']         =   $_product->getResource()->getAttribute('brand')->getFrontend()->getValue($_product) . " " . $_product->getName();
                $similarProduct[$i]['url']          =   $_product->getProductUrl();
                if ($productSpecialPrice) {
                    $similarProduct[$i]['price']    =   $this->_priceHelper->currency($productPrice, true, false);
                }
                $similarProduct[$i]['final_price']  =   $this->_priceHelper->currency($productFinalPrice, true, false);
                $similarProduct[$i]['image']        =   $imageUrl;
                $similarProduct[$i]['id']           =   $productId;
                $similarProduct[$i]['viewcount']    =   $viewCount;
                $similarProduct[$i]['itemsleft']    =   $this->getQuantityDetail();

                $minCount++;
            }
            if ($productPrice <= $maxPrice && $productPrice >= $filterPrice && $maxPrice != 0 && $maxCount < 4) {
                $similarProduct[$i]['size']         =   $_product->getSize();
                $similarProduct[$i]['name']         =   $_product->getResource()->getAttribute('brand')->getFrontend()->getValue($_product) . " " . $_product->getName();
                $similarProduct[$i]['url']          =   $_product->getProductUrl();
                if($productSpecialPrice){
                    $similarProduct[$i]['price']    =   $this->_priceHelper->currency($productPrice, true, false);
                }
                $similarProduct[$i]['final_price']  =   $this->_priceHelper->currency($productFinalPrice, true, false);
                $similarProduct[$i]['image']        =   $imageUrl;
                $similarProduct[$i]['id']           =   $productId;
                $similarProduct[$i]['viewcount']    =   $viewCount;
                $similarProduct[$i]['itemsleft']    =   $this->getQuantityDetail();

                $maxCount++;
            }
            $i++;
        }

        $similarProduct = array_values($similarProduct);
        return $similarProduct;
    }
}
