<?php
/**
 * @author      WebPanda
 * @package     WebPanda_PeopleWatching
 * @copyright   Copyright (c) WebPanda (https://webpanda-solutions.com/)
 * @license     https://webpanda-solutions.com/license-agreement
 */
namespace WebPanda\PeopleWatching\Block;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Registry;
use WebPanda\PeopleWatching\Helper\Config as ConfigHelper;

/**
 * Class PeopleWatching
 * @package WebPanda\PeopleWatching\Block
 */
class PeopleWatching extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var ConfigHelper
     */
    protected $configHelper;

    /**
     * PeopleWatching constructor.
     * @param Context $context
     * @param Registry $coreRegistry
     * @param ConfigHelper $configHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        ConfigHelper $configHelper,
        array $data
    ) {
        parent::__construct($context, $data);
        $this->coreRegistry = $coreRegistry;
        $this->configHelper = $configHelper;
    }

    /**
     * Retrieve current product model
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->coreRegistry->registry('product');
    }

    /**
     * @return string
     */
    public function getRegisterViewUrl()
    {
        return $this->getUrl('people/view/run');
    }

    /**
     * @return string
     */
    public function getStyle()
    {
        return $this->configHelper->getStyle();
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->configHelper->getEnabled()) {
            return '';
        }
        return parent::_toHtml();
    }
}
