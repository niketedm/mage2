<?php

/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Mancini\Productdetail\Plugin\ConfigurableProduct\Model;

class ConfigurableAttributeData
{
    /**
     * @var \Mancini\Productdetail\Helper\Data 
     */
    protected $_dataHelper;

    /**
     * @var \Magento\Catalog\Model\Product 
     */
    protected $_product;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $_priceHelper;

    /**
     * Constructor
     *
     * @param \Mancini\Productdetail\Helper\Data $dataHelper
     */
    public function __construct(
        \Mancini\Productdetail\Helper\Data $dataHelper,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Catalog\Model\Product $product,
        \Magento\Catalog\Model\ProductRepository $productRepository

    ) {
        $this->_dataHelper = $dataHelper;
        $this->_product    = $product;
        $this->_priceHelper =   $priceHelper;
        $this->_productRepository = $productRepository;

    }

    public function afterGetAttributesData(
        \Magento\ConfigurableProduct\Model\ConfigurableAttributeData $subject,
        $result
    ) {        
        foreach ($result as $key => $value) {            
            foreach ($value as $code) {
                if ($code !== null) {
                    if ($code['code'] === 'size') {
                        $i = 0;
                        $id = $code['id'];
                        foreach ($code['options'] as $key => $val) {
                            $length =   $width  =   $dimensions =   0;
                            $swatchLabel = str_replace(" ","-",trim($val['label']));

                            $swatchProducts = $val['products'];

                            if(isset($swatchProducts)){
                                foreach($swatchProducts as $swatchProduct){
                                    $productId  =  $swatchProduct;
                                }
                                if(isset($productId)){
                                    $product    =  $this->_productRepository->getById( $productId);

                                    if($product->getLength())
                                        $length     =   $product->getLength();
                                    if($product->getWidth())
                                        $width      =   $product->getWidth();
        
                                    if($length != 0 && $width != 0)
                                        $dimensions =   $length.'″x'.$width.'″';

                                    $result['attributes'][$id]['options'][$i]['dimension'] = $dimensions;
                                    $result['attributes'][$id]['options'][$i]['finalprice'] = $this->_priceHelper->currency( $product->getFinalPrice(), true, false);
                                }                              
                            }

                            $result['attributes'][$id]['options'][$i]['url'] = $this->_dataHelper->getSwatchPath() . $code['code'] . "/" . strtolower($swatchLabel) . ".png";                            
                            $i++;
                        }
                    }
                }
            }
        }

        return $result;
    }
}
