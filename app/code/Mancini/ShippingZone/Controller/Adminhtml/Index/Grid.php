<?php

namespace Mancini\ShippingZone\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;

class Grid extends Action
{
    /** @var LayoutFactory */
    protected $resultLayoutFactory;

    /** @var PageFactory */
    protected $resultPageFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->resultLayoutFactory = $resultLayoutFactory;
    }

    public function execute()
    {
        return $this->resultLayoutFactory->create();
    }
}
