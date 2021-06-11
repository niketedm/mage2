<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Controller\Pager;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultInterface;

class Change extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    private $layoutFactory;

    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->layoutFactory = $layoutFactory;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        $widgetData = $this->getRequest()->getParam('widget_data');
        $resultJson = $this->resultJsonFactory->create();

        if ($widgetData) {
            $block = $this->getBlock($widgetData);
            $html = $block->toHtml();
        }
        $result['block'] = $html ?? '';

        return $resultJson->setData($result);
    }

    /**
     * @param array $widgetData
     * @return \Magento\Framework\View\Element\BlockInterface
     */
    public function getBlock(array $widgetData)
    {
        $layout = $this->layoutFactory->create();

        return $layout->createBlock(
            \Amasty\MegaMenu\Block\Product\ProductsSlider::class,
            $widgetData['name'],
            ['data' => $widgetData['data']]
        );
    }
}
