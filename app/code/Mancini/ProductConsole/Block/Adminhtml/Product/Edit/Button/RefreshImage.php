<?php

namespace Mancini\ProductConsole\Block\Adminhtml\Product\Edit\Button;

use Magento\Catalog\Block\Adminhtml\Product\Edit\Button\Generic;

class RefreshImage extends Generic
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        if ($this->getProduct()->getId()) {
            return [
                'label' => __('Refresh Image'),
                'on_click' => sprintf(
                    "location.href = '%s';",
                    $this->getUrl('product_console/refresh/image', array('id' => $this->getProduct()->getId()))
                ),
                'class' => 'action-secondary',
                'sort_order' => 40
            ];
        } else {
            return [];
        }
    }
}
