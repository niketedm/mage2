<?php

namespace Synchrony\DigitalBuy\Block\Adminhtml\Promotion\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class Recent Button
 */
class ResetButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     * @codeCoverageIgnore
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->canRender('reset')) {
            $data = [
                'label' => __('Reset'),
                'class' => 'reset',
                'on_click' => 'location.reload();',
                'sort_order' => 30,
            ];
        }
        return $data;
    }
}
