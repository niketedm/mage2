<?php
namespace Synchrony\DigitalBuy\Model\Source;

use Magento\Framework\Option\ArrayInterface;

class PaymentAction implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => \Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE,
                'label' => __('Authorize Only'),
            ],
            [
                'value' => \Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE_CAPTURE,
                'label' => __('Authorize and Capture')
            ]
        ];
    }
}
