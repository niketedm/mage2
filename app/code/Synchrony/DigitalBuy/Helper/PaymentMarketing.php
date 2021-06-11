<?php

namespace Synchrony\DigitalBuy\Helper;

use Synchrony\DigitalBuy\Gateway\Config\RevolvingConfig as Config;
use Synchrony\DigitalBuy\Model\Source\DynamicPricing\DisplayArea;
use Synchrony\DigitalBuy\Model\Source\DynamicPricing\PromotionCalculationTypes;
use Synchrony\DigitalBuy\Model\Source\DynamicPricing\Strategy;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Cms\Model\BlockFactory;
use \Magento\Framework\UrlInterface;

/**
 * Class Payment Marketing Helper
 */
class PaymentMarketing
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var TimezoneInterface
     */
    private $_timezone;

    /**
     * @var FilterProvider
     */
    private $_filterProvider;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    private $_storeManager;

    /**
     * Block factory
     *
     * @var BlockFactory
     */
    private $_blockFactory;

    /**
     * @var Config
     */
    private $config;

    /**
     * PaymentMarketing constructor.
     * @param UrlInterface $urlBuilder
     * @param TimezoneInterface $timezone
     * @param FilterProvider $filterProvider
     * @param StoreManagerInterface $storeManager
     * @param BlockFactory $blockFactory
     * @param Config $config
     */
    public function __construct(
        UrlInterface $urlBuilder,
        TimezoneInterface $timezone,
        FilterProvider $filterProvider,
        StoreManagerInterface $storeManager,
        BlockFactory $blockFactory,
        Config $config
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->_timezone = $timezone;
        $this->_filterProvider = $filterProvider;
        $this->_storeManager = $storeManager;
        $this->_blockFactory = $blockFactory;
        $this->config = $config;
    }

    /**
     * Check if Payment Marketing is enabled and not expired
     *
     * @return bool
     */
    public function isPaymentMarketingEnabled()
    {
        if ($this->config->isDynPriceBlockEnabled() && !$this->hasMarketingPeriodExpired()) {
            return true;
        }
        return false;
    }

    /**
     * Check if Payment Marketing block is expired
     * @return bool
     */
    private function hasMarketingPeriodExpired()
    {
        $currentDate = $this->_timezone->scopeTimeStamp();
        $startDate = strtotime($this->config->getDynPriceBlockStartDate());
        $endDate = strtotime($this->config->getDynPriceBlockEndDate());
        if ((!$startDate || $currentDate >= $startDate) && (!$endDate || $currentDate <= $endDate)) {
            return false;
        }
        return true;
    }

    /**
     * Check if Payment Marketing block can be shown in PDP
     * @return bool
     */
    public function canShowBlockInPDP()
    {
        $displayAreas = $this->config->getDynPriceBlockDisplayArea();
        if (in_array(DisplayArea::PRODUCT_DETAILS_PAGE, $displayAreas)
            || in_array(DisplayArea::BOTH_PAGES, $displayAreas)) {
            return true;
        }
        return false;
    }

    /**
     * Check if Payment Marketing block can be shown in Checkout
     * @return bool
     */
    public function canShowBlockInCheckout()
    {
        $displayAreas = $this->config->getDynPriceBlockDisplayArea();
        if (in_array(DisplayArea::CHECKOUT_PAGE, $displayAreas)
                || in_array(DisplayArea::BOTH_PAGES, $displayAreas)) {
                return true;
        }
        return false;
    }

    /**
     * Check if Display mode is set to static
     * @return bool
     */
    public function isDisplayModeStatic()
    {
        if ($this->config->getDynPriceBlockDisplayStrategy() == Strategy::STATIC) {
            return true;
        }
        return false;
    }

    /**
     * Retrieve promotional template file name based on promotion type
     *
     * @return string
     */
    public function getPromotionalTemplate()
    {
        switch ($this->getPromotionCalculationType()) {
            case PromotionCalculationTypes::EQUAL_PAY_NO_INTEREST_TYPE:
                return 'Synchrony_DigitalBuy/marketing/equal-pay-no-interest-type';

            case PromotionCalculationTypes::DEFFERED_INTEREST_TYPE:
                return 'Synchrony_DigitalBuy/marketing/deffered-interest-type';

            case PromotionCalculationTypes::REDUCED_APR_TYPE:
                return 'Synchrony_DigitalBuy/marketing/reduced-apr';

        }
    }

    /**
     * Get Payment promotion calculation type
     *
     * @return int
     */
    public function getPromotionCalculationType()
    {
        return $this->config->getDynPriceBlockPromoCalcType();
    }

    /**
     * Get Promotion Block Config
     * @return array
     */
    public function getPromotionBlockConfig()
    {
        $config = [
            'term' => $this->config->getDynPriceBlockTerm(),
            'minAmount' => $this->config->getDynPriceBlockMinAmount(),
            'promotionalPeriod' => $this->getPromotionalPeriod(),
            'template' => $this->getPromotionalTemplate(),
            'promotionCalculationType' => $this->getPromotionCalculationType(),
            'apr' => $this->config->getDynPriceBlockApr(),
            'minInterestCharge' => $this->config->getDynPriceBlockMinInterestCharge(),
            'links' => [
                'applyNowLink' => $this->config->getApplyNowUrl()
            ]
        ];

        if ($this->config->getFinancingCmsPageUrlKey()) {
            $config['links']['cmsPageLink'] = $this->urlBuilder->getUrl($this->config->getFinancingCmsPageUrlKey());
        }

        return $config;
    }

    /**
     * Get Promotion Period
     * @return string
     */
    public function getPromotionalPeriod()
    {
        $promoString = '';
        $startDate = $this->config->getDynPriceBlockStartDate();
        if ($startDate) {
            $startDate = $this->formatDate($startDate);
        }
        $endDate = $this->config->getDynPriceBlockEndDate();
        if ($endDate) {
            $endDate = $this->formatDate($endDate);
        }
        if ($startDate && $endDate) {
            $promoString = __('between %1 and %2', $startDate, $endDate);
        } else if ($endDate) {
            $promoString = __('until %1', $endDate);
        }

        return $promoString;
    }

    /**
     * Retrieve formatting date
     *
     * @param null|string|\DateTimeInterface $date
     * @param int $format
     * @param bool $showTime
     * @param null|string $timezone
     * @return string
     */
    public function formatDate(
        $date = null,
        $format = \IntlDateFormatter::SHORT,
        $showTime = false,
        $timezone = null
    ) {
        $date = $date instanceof \DateTimeInterface ? $date : new \DateTime($date);
        return $this->_timezone->formatDateTime(
            $date,
            $format,
            $showTime ? $format : \IntlDateFormatter::NONE,
            null,
            $timezone
        );
    }

    /**
     * Prepare Static block Content HTML
     *
     * @return string
     */
    public function getStaticBlockHtml()
    {
        $html ='';
        $blockIdentifier = $this->config->getDynPriceBlockStaticBlockIdentifier();
        if ($blockIdentifier) {
            $storeId = $this->_storeManager->getStore()->getId();
            /** @var \Magento\Cms\Model\Block $block */
            $block = $this->_blockFactory->create();
            $block->setStoreId($storeId)->load($blockIdentifier);
            if ($block->isActive()) {
                $html = $this->_filterProvider->getBlockFilter()->setStoreId($storeId)->filter($block->getContent());
            }
        }
        return $html;
    }
}
