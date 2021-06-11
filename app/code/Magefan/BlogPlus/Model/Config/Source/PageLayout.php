<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\BlogPlus\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class PageLayout
 * @package Magefan\BlogPlus\Model\Config\Source
 */
class PageLayout implements OptionSourceInterface
{
    /**
     * Options int
     *
     * @return array
     */
    public function toOptionArray()
    {
        return  [
            ['value' => '', 'label' => __('-- Please Select --')],
            ['value' => 'empty', 'label' => __('Empty')],
            ['value' => '1column', 'label' => __('1 column')],
            ['value' => '2columns-left', 'label' => __('2 columns with left bar')],
            ['value' => '2columns-right', 'label' => __('2 columns with right bar')],
            ['value' => '3columns', 'label' => __('3 columns')],
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];
        foreach ($this->toOptionArray() as $item) {
            $array[$item['value']] = $item['label'];
        }
        return $array;
    }
}
