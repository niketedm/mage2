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



namespace Mirasvit\GdprConsent\Model;

use Magento\Framework\Model\AbstractModel;
use Mirasvit\GdprConsent\Api\Data\ConsentInterface;

class Consent extends AbstractModel implements ConsentInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Consent::class);
    }

    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    public function setCustomerId($value)
    {
        return $this->setData(self::CUSTOMER_ID, $value);
    }

    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    public function setRemoteAddr($value)
    {
        return $this->setData(self::REMOTE_ADDR, $value);
    }

    public function getRemoteAddr()
    {
        return $this->getData(self::REMOTE_ADDR);
    }

    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    public function setType($value)
    {
        return $this->setData(self::TYPE, $value);
    }

    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    public function setStatus($value)
    {
        return $this->setData(self::STATUS, $value);
    }
}
