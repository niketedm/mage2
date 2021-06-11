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



namespace Mirasvit\GdprCookie\Model\ResourceModel\CookieConsent;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mirasvit\GdprCookie\Model\ResourceModel\CookieConsent;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Mirasvit\GdprCookie\Model\CookieConsent::class,
            CookieConsent::class
        );
    }
}
