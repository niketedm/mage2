<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


declare(strict_types=1);

namespace Amasty\MegaMenu\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class IconStatus implements OptionSourceInterface
{
    const ENABLED = 'desktopAndMobile';

    const DESKTOP = 'desktop';

    const MOBILE = 'mobile';

    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::ENABLED,
                'label' => __('Both Desktop and Mobile')
            ],
            [
                'value' => self::DESKTOP,
                'label' => __('Desktop Only')
            ],
            [
                'value' => self::MOBILE,
                'label' => __('Mobile Only')
            ]
        ];
    }

    public function toArray(): array
    {
        return [
            self::ENABLED => __('Both Desktop and Mobile'),
            self::DESKTOP => __('Desktop Only'),
            self::MOBILE => __('Mobile Only')
        ];
    }
}
