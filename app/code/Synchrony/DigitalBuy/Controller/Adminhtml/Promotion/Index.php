<?php

namespace Synchrony\DigitalBuy\Controller\Adminhtml\Promotion;

use Synchrony\DigitalBuy\Model\Promotion as PromotionNodel;

/**
 * Promotions list controller
 */
class Index extends \Synchrony\DigitalBuy\Controller\Adminhtml\Promotion
{
    /**
     * Index action
     *
     * return void
     */
    public function execute()
    {
        $this->_initAction()->_addBreadcrumb(__('Promotions'), __('Synchrony Promotions'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Synchrony Promotions'));
        $this->_view->renderLayout();
    }
}
