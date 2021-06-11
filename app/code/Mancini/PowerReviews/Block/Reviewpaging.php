<?php

/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Mancini\PowerReviews\Block;


class Reviewpaging extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;
  
    /**
     * @var \Mancini\PowerReviews\Helper\Data
     */
    protected $_helperData;

    /**
     * Constructor
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Mancini\PowerReviews\Helper\Data $helperData
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Mancini\PowerReviews\Helper\Data $helperData,
        array $data = []
    ) {
        $this->_registry    =   $registry;
        $this->_helperData  = $helperData;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * Function to retrieve the current product from registry
     * @return object
     */
    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }

}
