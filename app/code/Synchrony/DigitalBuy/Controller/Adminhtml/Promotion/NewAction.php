<?php

namespace Synchrony\DigitalBuy\Controller\Adminhtml\Promotion;

/**
 * Promotion create controller
 */
class NewAction extends \Synchrony\DigitalBuy\Controller\Adminhtml\Promotion
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
