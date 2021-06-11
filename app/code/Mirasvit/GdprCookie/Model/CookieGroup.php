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
use Mirasvit\GdprCookie\Api\Data\CookieGroupInterface;

class CookieGroup extends AbstractModel implements CookieGroupInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Cookie::class);
    }

    public function getId()
    {
        return $this->getData(self::ID);
    }

    public function getName()
    {
        return $this->getData(self::NAME);
    }

    public function setName($value)
    {
        return $this->setData(self::NAME, $value);
    }

    public function getPriority()
    {
        return $this->getData(self::PRIORITY);
    }

    public function setPriority($value)
    {
        return $this->setData(self::PRIORITY, $value);
    }

    public function isActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }

    public function setIsActive($active)
    {
        return $this->setData(self::IS_ACTIVE, $active);
    }

    public function isRequired()
    {
        return $this->getData(self::IS_REQUIRED);
    }

    public function setIsRequired($value)
    {
        return $this->setData(self::IS_REQUIRED, $value);
    }

    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    public function setDescription($value)
    {
        return $this->setData(self::DESCRIPTION, $value);
    }

    public function getStoreIds()
    {
        return array_filter(explode(',', $this->getData(self::STORE_IDS)));
    }

    public function setStoreIds($value)
    {
        return $this->setData(self::STORE_IDS, implode(',', $value));
    }

}
