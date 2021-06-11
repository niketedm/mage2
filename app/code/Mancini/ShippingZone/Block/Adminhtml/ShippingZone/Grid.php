<?php

namespace Mancini\ShippingZone\Block\Adminhtml\ShippingZone;

use Magento\Backend\Block\Widget\Grid\Container;

class Grid extends Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_shippingzone';
        $this->_blockGroup = 'Mancini_ShippingZone';
        $this->_headerText = __('Manage Shipping Zones');
        $this->_addButtonLabel = __('Add Shipping Zone');
        $this->addButton(
            'import',
            [
                'label' => __('Import Shipping Zones'),
                'class' => 'import',
                'on_click' => sprintf("location.href = '%s';", $this->getZoneImportUrl()),
                'sort_order' => 20,
            ]
        );
        parent::_construct();
    }

    /**
     * @return string
     */
    public function getZoneImportUrl()
    {
        return $this->getUrl('*/zone/index');
    }
}
