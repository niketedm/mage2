<?php

/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Mancini\InventoryLeft\Block;

class Quantityavailable extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Mancini\Frog\Helper\ApiCall 
     */
    protected $_apiCallHelper;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * Constructor
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param \Mancini\Frog\Helper\ApiCall $apiCallHelper
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Mancini\Frog\Helper\ApiCall $apiCallHelper,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_registry = $registry;
        $this->_apiCallHelper = $apiCallHelper;
    }

    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }

    /**
     * Function to retrieve the Item Details from FROG
     * @return string
     */

    public function getQuantityDetail()
    {
        $skuSerial = $this->getCurrentProduct()->getSkuSerial();
        $params['item'] = '4155976';
        $result = $this->_apiCallHelper->inetWebsiteItemDetail(\Zend_Http_Client::POST, $params);

        $details = json_decode($result->getBody(), true);
        if ($details['availabletosell']) {
            $available_qt = $details['availabletosell'];
        } else {
            $available_qt = 0;
        }
        return $available_qt;
    }
}
