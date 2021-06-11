<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_SpinToWin
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\SpinToWin\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;

class Index extends Action
{
    public function __construct(
        Context $context,
        \Magento\Framework\Json\Helper\Data $jsonData,
        \Webkul\SpinToWin\Model\InfoFactory $infoFactory,
        \Webkul\SpinToWin\Helper\Data $helper,
        CookieMetadataFactory $cookieMetadata,
        CookieManagerInterface $cookieManager,
        \Webkul\SpinToWin\Logger\Logger $logger
    ) {
        $this->jsonData = $jsonData;
        $this->helper = $helper;
        $this->infoFactory = $infoFactory;
        $this->logger = $logger;
        $this->cookieMetadata = $cookieMetadata;
        $this->cookieManager = $cookieManager;
        parent::__construct($context);
    }
    
    public function execute()
    {
        try {
            $result = [];
            $data = $this->getRequest()->getParams();
            $spin = $this->helper->getSpin();
            if ($spin->getId()) {
                $result['wheel'] = $this->helper->getWheelData($spin->getId())->getData();
                $result['welcome'] = $spin->getEditForm()->getData();
                $result['result'] = $spin->getResultForm()->getData();
                $result['layout'] = $spin->getLayout()->getData();
                $result['visibility'] = $spin->getVisibility()->getData();
                $result['button'] = $spin->getButton()->getData();
                $result['coupon'] = $spin->getCoupon()->getData();
                $result['mediaUrl'] = $this->helper->getMediaDirectory();
            }
            $this->getResponse()->setHeader('Content-type', 'application/javascript');
            $this->getResponse()->setBody($this->jsonData
                ->jsonEncode(
                    [
                        'success' => 1,
                        'isempty' => empty($result),
                        'data' => $result
                    ]
                ));
        } catch (\Exception $e) {
            $this->logger->info("Index.php: ".$e->getMessage());
            $this->getResponse()->setHeader('Content-type', 'application/javascript');
            $this->getResponse()->setBody($this->jsonData
                ->jsonEncode(
                    [
                        'success' => 0,
                        'message' => __('Something went wrong in getting spin wheel.')
                    ]
                ));
        }
    }
}
