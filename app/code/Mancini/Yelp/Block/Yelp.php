<?php

namespace Mancini\Youtube\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mancini\Yelp\Helper\Data;

class Yelp extends Template {
    protected $_scopeConfig;
    protected $_storeManager;
    protected $_helper;

    public function __construct(
        Context $context,
        Data $_helper,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->_scopeConfig = $context->getScopeConfig();
        $this->_storeManager = $context->getStoreManager();
        $this->_helper = $_helper;
    }
}
