<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


declare(strict_types=1);

namespace Amasty\MegaMenu\Controller\Adminhtml\Builder;

use Amasty\MegaMenu\Model\ResourceModel\Menu\Item\Position;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;

class Index extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Amasty_MegaMenu::menu_builder';

    /**
     * @var Position
     */
    private $positionResource;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    public function __construct(
        Action\Context $context,
        Position $positionResource,
        StoreManagerInterface $storeManager,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->positionResource = $positionResource;
        $this->storeManager = $storeManager;
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute(): Page
    {
        $storeId = $this->getRequest()->getParam('store', $this->storeManager->getDefaultStoreView()->getId());
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->positionResource->importCategoryPositions($storeId);

        $resultPage->setActiveMenu(self::ADMIN_RESOURCE);
        $resultPage->getConfig()->getTitle()->prepend(__('Menu Builder'));

        return $resultPage;
    }
}
