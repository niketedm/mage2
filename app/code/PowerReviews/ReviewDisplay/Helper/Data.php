<?php

/**
 * @author Michal Walkowiak
 * @copyright Copyright (c) 2017 PowerReviews (http://www.powerreviews.com)
 * @package PowerReviews_ReviewDisplay
 */

namespace PowerReviews\ReviewDisplay\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    const ENABLE      = 'powerreviews_reviewdisplay/general/enable';
    const MERCHANT_ID = 'powerreviews_reviewdisplay/general/merchant_id';
    const MERCHANT_GROUP_ID  = 'powerreviews_reviewdisplay/general/merchant_group_id';
    const API_KEY  = 'powerreviews_reviewdisplay/general/api_key';
    const LOCALE  = 'powerreviews_reviewdisplay/general/locale';

    const PRODUCT_PAGE_REVIEW_SNIPPET = 'powerreviews_reviewdisplay/on_off_sections/product_page_review_snippet';
    const PRODUCT_PAGE_REVIEW_DISPLAY = 'powerreviews_reviewdisplay/on_off_sections/product_page_review_display';
    const SEARCH_RESULTS_CATEGORY_PAGE_SNIPPET = 'powerreviews_reviewdisplay/on_off_sections/search_results_category_page_snippet';
    const PRODUCT_PAGE_QUESTION_SNIPPET = 'powerreviews_reviewdisplay/on_off_sections/product_page_question_snippet';
    const PRODUCT_PAGE_QUESTION_DISPLAY = 'powerreviews_reviewdisplay/on_off_sections/product_page_question_display';

    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);

        $this->_scopeConfig = $context->getScopeConfig();
    }


    public function getEnable()
    {
        return $this->_scopeConfig->getValue(self::ENABLE, \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE);
    }

    public function getMerchantId()
    {
        return $this->_scopeConfig->getValue(self::MERCHANT_ID, \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE);
    }

    public function getMerchantGroupId()
    {
        return $this->_scopeConfig->getValue(self::MERCHANT_GROUP_ID, \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE);
    }

    public function getApiKey()
    {
        return $this->_scopeConfig->getValue(self::API_KEY, \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE);
    }

    public function getLocale()
    {
        return $this->_scopeConfig->getValue(self::LOCALE, \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE);
    }

    public function getProductPageReviewSnippet()
    {
        return $this->_scopeConfig->getValue(self::PRODUCT_PAGE_REVIEW_SNIPPET, \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE);
    }

    public function getProductPageReviewDisplay()
    {
        return $this->_scopeConfig->getValue(self::PRODUCT_PAGE_REVIEW_DISPLAY, \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE);
    }

    public function getSearchResultsCategoryPageSnippet()
    {
        return $this->_scopeConfig->getValue(self::SEARCH_RESULTS_CATEGORY_PAGE_SNIPPET, \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE);
    }

    public function getProductPageQuestionSnippet()
    {
        return $this->_scopeConfig->getValue(self::PRODUCT_PAGE_QUESTION_SNIPPET, \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE);
    }

    public function getProductPageQuestionDisplay()
    {
        return $this->_scopeConfig->getValue(self::PRODUCT_PAGE_QUESTION_DISPLAY, \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE);
    }
}
