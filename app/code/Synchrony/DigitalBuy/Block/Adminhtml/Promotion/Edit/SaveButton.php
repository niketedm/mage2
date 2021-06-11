<?php

namespace Synchrony\DigitalBuy\Block\Adminhtml\Promotion\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class Save and continue Button
 */
class SaveButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     * @codeCoverageIgnore
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->canRender('save')) {
            $data = [
                'label' => __('Save'),
                'class' => 'save primary',
                'on_click' => '',
            ];
        }
        return $data;
    }
}
