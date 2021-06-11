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



namespace Mirasvit\Gdpr\DataManagement\Entity\Helper;

use Mirasvit\Gdpr\Model\ConfigProvider;

class DataProtection
{
    private $configProvider;

    public function __construct(
        ConfigProvider $configProvider
    ) {
        $this->configProvider = $configProvider;
    }

    /**
     * @param string $entityCreatedAt
     *
     * @return bool
     */
    private function isDaysForProtectionExpired($entityCreatedAt)
    {
        $daysToProtect          = $this->configProvider->getDataProtectionDays();
        $entityDateToTimestamp  = new \DateTime($entityCreatedAt);
        $protectionDayExpiredAt = date("Y-m-d", strtotime("+" . $daysToProtect . " days", $entityDateToTimestamp->getTimestamp()));
        $dateNow                = date("Y-m-d");

        return $dateNow >= $protectionDayExpiredAt;
    }

    /**
     * @param string $entityCode
     * @param string $entityCreatedAt
     *
     * @return bool
     */
    public function isDataProtectedByDay($entityCode, $entityCreatedAt)
    {
        if ($this->configProvider->isProtectedEntityCode($entityCode)) {
            if (!$this->isDaysForProtectionExpired($entityCreatedAt)) {
                return true;
            }
        }

        return false;
    }
}
