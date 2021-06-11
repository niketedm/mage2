<?php
/**
 * @author      WebPanda
 * @package     WebPanda_PeopleWatching
 * @copyright   Copyright (c) WebPanda (https://webpanda-solutions.com/)
 * @license     https://webpanda-solutions.com/license-agreement
 */
namespace WebPanda\PeopleWatching\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 * @package WebPanda\PeopleWatching\Helper
 */
class Config extends AbstractHelper
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Config constructor.
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
        $this->scopeConfig = $context->getScopeConfig();
    }

    /**
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->scopeConfig->getValue('people_watching/views/enabled');
    }

    /**
     * @return int
     */
    public function getMinimumViews()
    {
        return $this->scopeConfig->getValue('people_watching/views/minimum_views');
    }

    /**
     * @return int
     */
    public function getLifetime()
    {
        return $this->scopeConfig->getValue('people_watching/views/lifetime');
    }
    /**
     * @return int
     */
    public function getNumberInflate()
    {
        return $this->scopeConfig->getValue('people_watching/views/number_inflate');
    }

    /**
     * @param $viewCount
     * @return string
     */
    public function getMessageSingle($viewCount)
    {
        $message = $this->scopeConfig->getValue('people_watching/views/message_single');
        return str_replace('{{views}}', $viewCount, $message);
    }

    /**
     * @param $viewCount
     * @return string
     */
    public function getMessage($viewCount)
    {
        $message = $this->scopeConfig->getValue('people_watching/views/message');
        return str_replace('{{views}}', $viewCount, $message);
    }

    /**
     * @param $viewCount
     * @return string
     */
    public function getFinalMessage($viewCount)
    {
        return ($viewCount == 1) ? $this->getMessageSingle($viewCount) : $this->getMessage($viewCount);
    }

    /**
     * @return string
     */
    public function getStyle()
    {
        return $this->scopeConfig->getValue('people_watching/views/style');
    }
}
