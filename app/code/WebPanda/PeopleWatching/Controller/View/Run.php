<?php
/**
 * @author      WebPanda
 * @package     WebPanda_PeopleWatching
 * @copyright   Copyright (c) WebPanda (https://webpanda-solutions.com/)
 * @license     https://webpanda-solutions.com/license-agreement
 */
namespace WebPanda\PeopleWatching\Controller\View;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Session\SessionManagerInterface;
use WebPanda\PeopleWatching\Model\ResourceModel\View as ViewResourceModel;
use WebPanda\PeopleWatching\Helper\Config as ConfigHelper;

/**
 * Class Run
 * @package WebPanda\PeopleWatching\Controller\View
 */
class Run extends \Magento\Framework\App\Action\Action
{
    /**
     * @var SessionManagerInterface
     */
    protected $session;

    /**
     * @var ViewResourceModel
     */
    protected $viewResourceModel;

    /**
     * @var ConfigHelper
     */
    protected $configHelper;

    /**
     * Run constructor.
     * @param Context $context
     * @param SessionManagerInterface $session
     * @param ViewResourceModel $viewResourceModel
     * @param ConfigHelper $configHelper
     */
    public function __construct(
        Context $context,
        SessionManagerInterface $session,
        ViewResourceModel $viewResourceModel,
        ConfigHelper $configHelper
    ) {
        parent::__construct($context);
        $this->session = $session;
        $this->viewResourceModel = $viewResourceModel;
        $this->configHelper = $configHelper;
    }

    public function execute()
    {
        if (!$this->configHelper->getEnabled()) {
            $this->getResponse()->setBody(
                json_encode(['show' => false])
            );
            return;
        }
        $productId = $this->getRequest()->getParam('product_id');
        // register view
        $this->viewResourceModel->addProductView($this->session->getSessionId(), $productId);
        $viewCount = $this->viewResourceModel->getViewCount($this->configHelper->getLifetime(), $productId, $this->session->getSessionId());
        $viewCount += (int)$this->configHelper->getNumberInflate();
        if ($viewCount < $this->configHelper->getMinimumViews()) {
            $this->getResponse()->setBody(
                json_encode(['show' => false])
            );
            return;
        }

        $this->getResponse()->setBody(
            json_encode(['show' => true, 'message' => $this->configHelper->getFinalMessage($viewCount)])
        );
    }
}
