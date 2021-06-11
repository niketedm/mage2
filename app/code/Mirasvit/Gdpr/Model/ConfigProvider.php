<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-gdpr
 * @version   1.1.1
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Gdpr\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class ConfigProvider
{
    const CONSENT_COOKIE_NAME = 'gdpr_cookie_consent';

    private $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag('gdpr/general/is_enabled');
    }

    public function isDataProvideEnabled()
    {
        return $this->isEnabled() && $this->scopeConfig->isSetFlag('gdpr/personal_data/provide/is_enabled');
    }

    public function isDataAnonymizeEnabled()
    {
        return $this->isEnabled() && $this->scopeConfig->isSetFlag('gdpr/personal_data/anonymize/is_enabled');
    }

    public function isDataRemoveEnabled()
    {
        return $this->isEnabled() && $this->scopeConfig->isSetFlag('gdpr/personal_data/remove/is_enabled');
    }

    public function isAutoRemoveEnabled()
    {
        return $this->isEnabled() && $this->scopeConfig->isSetFlag('gdpr/personal_data/auto_remove/is_enabled');
    }

    /**
     * @return int
     */
    public function daysToAutoRemove()
    {
        return (int)$this->scopeConfig->getValue('gdpr/personal_data/auto_remove/days');
    }

    /**
     * @return bool
     */
    public function isPersonalDataProtectionEnabled()
    {
        return (bool)$this->scopeConfig->getValue('gdpr/personal_data/protection/is_enabled');
    }

    /**
     * @return int
     */
    public function getDataProtectionDays()
    {
        return (int)$this->scopeConfig->getValue('gdpr/personal_data/protection/days');
    }

    /**
     * @return array
     */
    public function getDataProtectionEntities()
    {
        $entities = $this->scopeConfig->getValue('gdpr/personal_data/protection/entities');

        return explode(',', $entities);
    }

    /**
     * @param string $entityCode
     *
     * @return bool
     */
    public function isProtectedEntityCode($entityCode)
    {
        if ($this->isPersonalDataProtectionEnabled()) {
            $entities = $this->getDataProtectionEntities();
            if (in_array($entityCode, $entities)) {
                return true;
            }
        }

        return false;
    }

    public function getCustomerAttributes()
    {
        return [
            'firstname',
            'lastname',
            'middlename',
            'prefix',
            'suffix',
            //            'email',
            'dob',
            'gender',
            'taxvat',
        ];
    }

    public function getOrderAttributes()
    {
        return [
            'customer_dob',
            'customer_email',
            'customer_firstname',
            'customer_lastname',
            'customer_middlename',
            'customer_prefix',
            'customer_suffix',
            'customer_taxvat',
        ];
    }

    public function getAddressAttributes()
    {
        return [
            'firstname',
            'lastname',
            'middlename',
            'prefix',
            'suffix',
            'company',
            'street',
            'city',
            'country',
            'postcode',
            'telephone',
            'fax',
            'email',
        ];
    }
}
