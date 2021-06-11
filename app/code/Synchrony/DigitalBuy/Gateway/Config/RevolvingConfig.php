<?php
namespace Synchrony\DigitalBuy\Gateway\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;

class RevolvingConfig extends AbstractPaymentConfig
{
    /**
     * Synchrony DigitalBuy Revolving payment method code.
     *
     * @var string
     */
    const METHOD_CODE = 'synchrony_digitalbuy';

    /**
     * Retrieve Merchant ID to be used in Digital Buy API calls
     *
     * @param string|int|null $storeId
     * @return string
     */
    public function getDigitalBuyApiMerchantId($storeId = null)
    {
        return $this->commonConfig->getDigitalBuyApiMerchantId($storeId);
    }

    /**
     * Retrieve Digital Buy API password
     *
     * @param string|int|null $storeId
     * @return mixed
     */
    public function getDigitalBuyApiPassword($storeId = null)
    {
        return $this->commonConfig->getDigitalBuyApiPassword($storeId);
    }

    /**
     * Retrieve default promo code
     *
     * @param string|int|null $storeId
     * @return mixed
     */
    public function getDefaultPromoCode($storeId = null)
    {
        return $this->getValue('default_promo_code', $storeId);
    }

    /**
     * Retrieve show address match note flag
     *
     * @param string|int|null $storeId
     * @return bool
     */
    public function getCanShowAddressMatchNote($storeId = null)
    {
        return (bool)$this->getValue('show_address_match_note', $storeId);
    }

    /**
     * Retrieve Flag to determine if Dynamic Pricing Block is enabled or not
     *
     * @param string|int|null $storeId
     * @return bool
     */
    public function isDynPriceBlockEnabled($storeId = null)
    {
        return (bool)$this->getValue('marketing_priceblock_enable', $storeId);
    }

    /**
     * Retrieve Minimum Amount for Dynamic Pricing Block
     *
     * @param string|int|null $storeId
     * @return float
     */
    public function getDynPriceBlockMinAmount($storeId = null)
    {
        return $this->getValue('marketing_priceblock_min_amount', $storeId);
    }

    /**
     * Retrieve Dynamic Pricing Block display areas
     *
     * @param string|int|null $storeId
     * @return array
     */
    public function getDynPriceBlockDisplayArea($storeId = null)
    {
        return explode(',', $this->getValue('marketing_priceblock_area', $storeId));
    }

    /**
     * Retrieve Dynamic Pricing Block Display strategy
     *
     * @param string|int|null $storeId
     * @return int
     */
    public function getDynPriceBlockDisplayStrategy($storeId = null)
    {
        return $this->getValue('marketing_priceblock_strategy', $storeId);
    }

    /**
     * Retrieve Static block Identifier for Dynamic Pricing Block (for Static strategy)
     *
     * @param string|int|null $storeId
     * @return string
     */
    public function getDynPriceBlockStaticBlockIdentifier($storeId = null)
    {
        return $this->getValue('marketing_priceblock_static_block', $storeId);
    }

    /**
     * Retrieve promo type for Dynamic Pricing Block
     *
     * @param string|int|null $storeId
     * @return int
     */
    public function getDynPriceBlockPromoCalcType($storeId = null)
    {
        return $this->getValue('marketing_priceblock_promo_type', $storeId);
    }

    /**
     * Retrieve term length for Dynamic Pricing Block
     *
     * @param string|int|null $storeId
     * @return int
     */
    public function getDynPriceBlockTerm($storeId = null)
    {
        return (int) $this->getValue('marketing_priceblock_term_mo', $storeId);
    }

    /**
     * Retrieve APR for Dynamic Pricing Block
     *
     * @param string|int|null $storeId
     * @return float
     */
    public function getDynPriceBlockApr($storeId = null)
    {
        return (float) $this->getValue('marketing_priceblock_apr', $storeId);
    }

    /**
     * Retrieve Minimum Interest Charge for Dynamic Pricing Block
     *
     * @param string|int|null $storeId
     * @return float
     */
    public function getDynPriceBlockMinInterestCharge($storeId = null)
    {
        return (float) $this->getValue('marketing_priceblock_minimium_interest_charge', $storeId);
    }

    /**
     * Retrieve Start date for Dynamic Pricing Block
     *
     * @param string|int|null $storeId
     * @return string
     */
    public function getDynPriceBlockStartDate($storeId = null)
    {
        return $this->getValue('marketing_priceblock_start_date', $storeId);
    }

    /**
     * Retrieve End Date for Dynamic Pricing Block
     *
     * @param string|int|null $storeId
     * @return string
     */
    public function getDynPriceBlockEndDate($storeId = null)
    {
        return $this->getValue('marketing_priceblock_end_date', $storeId);
    }

    /**
     * Retrieve End Date for Dynamic Pricing Block
     *
     * @param string|int|null $storeId
     * @return string
     */
    public function getFinancingCmsPageUrlKey($storeId = null)
    {
        return $this->getValue('link_cms_page', $storeId);
    }

    /**
     * Retrieve Apply now URL
     *
     * @param string|int|null $storeId
     * @return string
     */
    public function getApplyNowUrl($storeId = null)
    {
        return $this->getValue('apply_now_url', $storeId);
    }
}
