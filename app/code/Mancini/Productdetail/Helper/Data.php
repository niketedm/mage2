<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mancini\Productdetail\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
class Data extends AbstractHelper
{
     
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManagerInterface;

    /**
     * @var File
     */
    protected $_fileDriver;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Framework\Filesystem\Driver\File $fileDriver
    ) {
        parent::__construct($context);
        $this->_storeManagerInterface = $storeManagerInterface;
        $this->_fileDriver = $fileDriver;
    }
    
    /**
     * media path of swatch
     * @return string
     */
    public function getSwatchPath(){
        $store      = $this->_storeManagerInterface->getStore();
        $swatchUrl  = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)."wysiwyg/swatches/";
        return $swatchUrl;
    }
    /**
     * media path 
     * @return string
     */
    public function getMediaPath(){
        $store      =   $this->_storeManagerInterface->getStore();
        $mediaUrl   =   $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl;
    }
    /**
     * check whether the swatch image exist
     * @return int
     */
    public function checkSwatchExist ($swatchLabel) {
        $fileName = $this->getSwatchPath().$swatchLabel.".png";
        if (($swatchLabel != '') && ($this->_fileDriver->isExists($fileName))) {
            return 0;
        } else {
            return 1;
        }
    }    
}

