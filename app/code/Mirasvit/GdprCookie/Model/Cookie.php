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
use Mirasvit\GdprCookie\Api\Data\CookieInterface;

class Cookie extends AbstractModel implements CookieInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Cookie::class);
    }

    public function getName()
    {
        return $this->getData(self::NAME);
    }

    public function setName($value)
    {
        return $this->setData(self::NAME, $value);
    }

    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    public function setCode($value)
    {
        return $this->setData(self::CODE, $value);
    }

    public function isActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }

    public function setActive($value)
    {
        return $this->setData(self::IS_ACTIVE, $value);
    }

    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    public function setDescription($value)
    {
        return $this->setData(self::DESCRIPTION, $value);
    }

    public function getLifetime()
    {
        return $this->getData(self::LIFETIME);
    }

    public function setLifetime($value)
    {
        return $this->setData(self::LIFETIME, $value);
    }

    public function getGroupId()
    {
        return $this->getData(self::GROUP_ID);
    }

    public function setGroupId($value)
    {
        return $this->setData(self::GROUP_ID, $value);
    }

    public function getStoreIds()
    {
        return array_filter(explode(',', $this->getData(self::STORE_IDS)));
    }

    public function setStoreIds($value)
    {
        return $this->setData(self::STORE_IDS, implode(',', array_filter($value)));
    }

}
