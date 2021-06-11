<?php

/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\BlogPlus\Model\Config\Source;

/**
 * Class AutopublishCategories
 * @package Magefan\BlogPlus\Model\Config\Source
 */
class FbAutopublishCategory extends \Magefan\Blog\Model\Config\Source\Category
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            parent::toOptionArray();

            foreach ($this->options as $key => $option) {
                if (!$option['value']) {
                    $this->options[$key]['label'] = __('All Categories');
                    break;
                }
            }
        }

        return $this->options;
    }
}
