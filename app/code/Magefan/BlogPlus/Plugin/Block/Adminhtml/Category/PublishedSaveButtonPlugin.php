<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\BlogPlus\Plugin\Block\Adminhtml\Category;

use Magefan\BlogPlus\Plugin\Block\Adminhtml\AbstractPublishedSaveButtonPlugin;

/**
 * Class PublishedSaveButtonPlugin
 * @package Magefan\BlogPlus\Plugin\Block\Adminhtml\Category
 */
class PublishedSaveButtonPlugin extends AbstractPublishedSaveButtonPlugin
{
    /**
     * Remove save button if it's not allowed
     * @param \Magefan\Community\Block\Adminhtml\Edit\GenericButton $subject
     * @param $result
     * @return array
     */
    public function afterGetButtonData(
        \Magefan\Community\Block\Adminhtml\Edit\GenericButton $subject,
        $result
    ) {
        if (!$this->authorization->isAllowed('Magefan_BlogPlus::category_save_published')) {
            $model = $this->registry->registry('current_model');
            if ($model->isActive()) {
                return [];
            }
        }
        return $result;
    }
}
