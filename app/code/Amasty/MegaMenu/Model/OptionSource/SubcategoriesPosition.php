<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


declare(strict_types = 1);

namespace Amasty\MegaMenu\Model\OptionSource;

class SubcategoriesPosition
{
    const NOT_SHOW = 0;

    const LEFT = 1;

    const TOP = 2;

    public function toOptionArray(bool $notShowOptions = false): array
    {
        $options = [
            self::LEFT => ['value' => self::LEFT, 'label' => __('Left')],
            self::TOP => ['value' => self::TOP, 'label' => __('Top')]
        ];

        if ($notShowOptions) {
            $options[self::NOT_SHOW] = ['value' => self::NOT_SHOW, 'label' => __('Do not show')];
        }

        return $options;
    }
}
