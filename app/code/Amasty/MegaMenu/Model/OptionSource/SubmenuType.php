<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


declare(strict_types = 1);

namespace Amasty\MegaMenu\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class SubmenuType implements OptionSourceInterface
{
    const WITHOUT_CONTENT = 0;

    const WITH_CONTENT = 1;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::WITHOUT_CONTENT, 'label' => __('Without Content')],
            ['value' => self::WITH_CONTENT, 'label' => __('With Content')]
        ];
    }
}
