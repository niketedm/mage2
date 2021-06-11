<?php

namespace Mancini\ShippingZone\Block\Adminhtml\ShippingZone\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class ImportButton
 */
class ImportButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getShippingZoneId()) {
            $data = [
                'label' => __('Import Zone Zipcodes'),
                'class' => 'import',
                'on_click' => sprintf("location.href = '%s';", $this->getImportUrl()),
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getImportUrl()
    {
        return $this->getUrl('*/import/index', ['id' => $this->getShippingZoneId()]);
    }
}
