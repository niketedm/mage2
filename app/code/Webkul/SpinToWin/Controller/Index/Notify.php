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

class Notify extends Action
{
    public function __construct(
        Context $context,
        \Magento\Framework\Json\Helper\Data $jsonData,
        \Webkul\SpinToWin\Model\SegmentsFactory $segmentsFactory,
        \Webkul\SpinToWin\Helper\Data $helper,
        \Webkul\SpinToWin\Helper\Email $emailHelper,
        CookieMetadataFactory $cookieMetadata,
        CookieManagerInterface $cookieManager,
        \Webkul\SpinToWin\Logger\Logger $logger
    ) {
        $this->jsonData = $jsonData;
        $this->helper = $helper;
        $this->emailHelper = $emailHelper;
        $this->segmentsFactory = $segmentsFactory;
        $this->logger = $logger;
        $this->cookieMetadata = $cookieMetadata;
        $this->cookieManager = $cookieManager;
        parent::__construct($context);
    }
    
    public function execute()
    {
        try {
            $data = $this->getRequest()->getParams();
            $model = $this->segmentsFactory->create()->load($data['segmentid']);
            $segment = [];
            $segment['coupon'] = $data['coupon'];
            $segment['label'] = $model->getLabel();
            $segment['heading'] = $model->getHeading();
            $segment['description'] = $model->getDescription();
            $this->emailHelper->sendCouponNotification($data['email'], $data['name'], $segment);
        } catch (\Exception $e) {
            $this->logger->info("Notify.php: ".$e->getMessage());
        }
    }
}
