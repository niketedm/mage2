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



namespace Mirasvit\GdprCookie\Model;

use Magento\Framework\Model\AbstractModel;
use Mirasvit\GdprCookie\Api\Data\CookieConsentInterface;

class CookieConsent extends AbstractModel implements CookieConsentInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\CookieConsent::class);
    }

    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    public function setCustomerId($value)
    {
        return $this->getData($value, self::CUSTOMER_ID);
    }

    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    public function getGroupsIds()
    {
        return $this->getData(self::GROUP_IDS);
    }

    public function getRemoteAddr()
    {
        return $this->getData(self::REMOTE_ADDR);
    }

    public function setRemoteAddr($value)
    {
        return $this->getData($value, self::REMOTE_ADDR);
    }

    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

}
