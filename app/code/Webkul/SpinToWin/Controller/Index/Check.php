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
use Magento\SalesRule\Model\CouponGenerator;
use Magento\SalesRule\Api\Data\CouponGenerationSpecInterfaceFactory;
use Magento\SalesRule\Model\Service\CouponManagementService;
use Magento\Framework\Session\SessionManagerInterface;

class Check extends Action
{
    /**
     * JSON
     *
     * @var \Magento\Framework\Json\Helper\Data
     */
    public $jsonData;

    /**
     * Helper
     *
     * @var \Webkul\SpinToWin\Helper\Data
     */
    public $helper;

    /**
     * Info Model
     *
     * @var \Webkul\SpinToWin\Model\InfoFactory
     */
    public $infoFactory;
    
    /**
     * Reports Model
     *
     * @var \Webkul\SpinToWin\Model\ReportsFactory
     */
    public $reportsFactory;
    
    /**
     * Segment Model
     *
     * @var \Webkul\SpinToWin\Model\SegmentsFactory
     */
    public $segmentsFactory;
    
    /**
     * Logger
     *
     * @var \Webkul\SpinToWin\Logger\Logger
     */
    public $logger;
    
    /**
     * Coupon Generator
     *
     * @var Magento\SalesRule\Model\CouponGenerator
     */
    public $couponGenerator;
    
    /**
     * Cookie Metadata
     *
     * @var Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    public $cookieMetadata;
    
    /**
     * Cookie Manager
     *
     * @var Magento\Framework\Stdlib\CookieManagerInterface
     */
    public $cookieManager;
    
    /**
     * Session Manager
     *
     * @var Magento\Framework\Session\SessionManagerInterface
     */
    public $sessionManager;
    
    /**
     * Construct
     *
     * @param Context $context
     * @param \Magento\Framework\Json\Helper\Data $jsonData
     * @param \Webkul\SpinToWin\Model\InfoFactory $infoFactory
     * @param \Webkul\SpinToWin\Model\ReportsFactory $reportsFactory
     * @param \Webkul\SpinToWin\Model\SegmentsFactory $segmentsFactory
     * @param \Webkul\SpinToWin\Helper\Data $helper
     * @param CouponGenerator $couponGenerator
     * @param CookieMetadataFactory $cookieMetadata
     * @param CookieManagerInterface $cookieManager
     * @param SessionManagerInterface $sessionManager
     * @param \Webkul\SpinToWin\Logger\Logger $logger
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Json\Helper\Data $jsonData,
        \Webkul\SpinToWin\Model\InfoFactory $infoFactory,
        \Webkul\SpinToWin\Model\ReportsFactory $reportsFactory,
        \Webkul\SpinToWin\Model\SegmentsFactory $segmentsFactory,
        \Webkul\SpinToWin\Helper\Data $helper,
        CouponGenerator $couponGenerator,
        CookieMetadataFactory $cookieMetadata,
        CookieManagerInterface $cookieManager,
        SessionManagerInterface $sessionManager,
        \Webkul\SpinToWin\Logger\Logger $logger
    ) {
        $this->jsonData = $jsonData;
        $this->helper = $helper;
        $this->infoFactory = $infoFactory;
        $this->reportsFactory = $reportsFactory;
        $this->segmentsFactory = $segmentsFactory;
        $this->logger = $logger;
        $this->couponGenerator = $couponGenerator;
        $this->cookieMetadata = $cookieMetadata;
        $this->cookieManager = $cookieManager;
        $this->sessionManager = $sessionManager;
        parent::__construct($context);
    }

    /**
     * Checks the Spin
     *
     * @return JSON
     */
    public function execute()
    {
        try {
            $result = [];
            $data = $this->getRequest()->getParams();
            $spinIds = $this->helper->getSpinIds();
            $isSuccess = false;
            $msg = __('Something went wrong.');
            if (in_array($data['spin-wheel-id'], $spinIds)) {
                $spin = $this->infoFactory->create()->load($data['spin-wheel-id']);
                $spinId = $spin->getId();
                $isSpinned = $this->reportsFactory->create()
                                        ->getCollection()
                                        ->addFieldToFilter('spin_id', $spinId)
                                        ->addFieldToFilter('email', $data['spin-wheel-email'])
                                        ->getSize();
                if (!$isSpinned) {
                    $segmentDetail = $this->calculateResult($spin);
                    $segmentId = $segmentDetail['segmentid'];
                    $segmentPos = $segmentDetail['segment'];
                    if ($segmentId) {
                        $segment = $this->segmentsFactory->create()->load($segmentId);
                        $isSuccess = true;
                        $msg = __('Successfully spinned.');
                        $result['type'] = $segment->getType();
                        $result['heading'] = $segment->getHeading();
                        $result['description'] = $segment->getDescription();
                        $result['segmentid'] = $segment->getId();
                        $result['segment'] = $segmentPos;
                        $report = $this->reportsFactory->create();
                        $status = 0;
                        if ($segment->getType()) {
                            $couponSpecData = [
                                'rule_id' => $segment->getRuleId(),
                                'qty' => 1,
                                'length' => 12,
                                'format' => 'alphanum',
                            ];
                            $coupon = $this->couponGenerator->generateCodes($couponSpecData)[0];
                            $report->setCoupon($coupon);
                            $result['coupon'] = $coupon;
                            $result['notify'] = [];
                            $result['notify']['coupon'] = $coupon;
                            $result['notify']['segmentid'] = $segment->getId();
                            $tempName = 'Customer';
                            if (isset($data['spin-wheel-name'])) {
                                $tempName = $data['spin-wheel-name'];
                            }
                            $result['notify']['name'] = $tempName;
                            $result['notify']['email'] = $data['spin-wheel-email'];
                            $this->setWonCouponCookie($spin, $segment, $coupon);
                            $status = 1;
                        }
                        $report->setSpinId($spinId);
                        $report->setEmail($data['spin-wheel-email']);
                        if (isset($data['spin-wheel-name'])) {
                            $report->setName($data['spin-wheel-name']);
                        }
                        $report->setResult($segment->getType());
                        $report->setStatus($status);
                        $report->setSegmentId($segment->getId());
                        $report->setSegmentLabel($segment->getLabel());
                        $report->save();
                        $segment->setAvailed($segment->getAvailed() + 1);
                        $segment->save();
                        $this->setSpinsCookie($spinId);
                        $this->checkIfSpinAvailable($spinId);
                    } else {
                        $infoModel = $this->infoFactory->create()->load($spin->getId());
                        $infoModel->setStatus(0)->save();
                        $msg = __('Something went wrong.');
                    }
                } else {
                    $msg = __('You have already spinned the wheel once.');
                    $this->setSpinsCookie($spinId);
                }
            }
            $this->getResponse()->setHeader('Content-type', 'application/javascript');
            $this->getResponse()->setBody($this->jsonData
                ->jsonEncode(
                    [
                        'success' => $isSuccess,
                        'msg' => $msg,
                        'data' => $result
                    ]
                ));
        } catch (\Exception $e) {
            $this->logger->info("Check.php: ".$e->getMessage());
            $this->getResponse()->setHeader('Content-type', 'application/javascript');
            $this->getResponse()->setBody($this->jsonData
                ->jsonEncode(
                    [
                        'success' => 0,
                        'msg' => __('Something went wrong in getting spin wheel.')
                    ]
                ));
        }
    }

    /**
     * Calculate Result
     *
     * @param \Webkul\SpinToWin\Model\InfoFactory $spin
     * @return array
     */
    public function calculateResult($spin)
    {
        $segmentsArray = [];
        $segmentId = 0;
        $sum = 0;
        $result = [];
        $segments = $spin->getSegments()->setOrder('position', 'ASC');
        $i = 0;
        foreach ($segments as $segment) {
            $i++;
            $segmentsArray[$segment->getId()] = $i;
            if ($segment->getLimits()===null || ($segment->getLimits()!==null && $segment->getAvailed()<$segment->getLimits())) {
                $sum += $segment->getGravity();
                $result[$segment->getId()] = $sum;
            }
        }
        $indx = 0;
        if (!empty($result)) {
            $random = random_int(0, max($result) - 1);
            foreach ($result as $key => $value) {
                if ($random < $value) {
                    $segmentId = $key;
                    $indx = $segmentsArray[$segmentId];
                    break;
                }
            }
        }
        return ['segmentid'=>$segmentId, 'segment'=>$indx];
    }

    //To check if any more spin available
    public function checkIfSpinAvailable($spinId)
    {
        $spin = $this->infoFactory->create()->load($spinId);
        $isAvailable = false;
        $segments = $spin->getSegments();
        foreach ($segments as $segment) {
            if ($segment->getLimits()===null || ($segment->getLimits()!==null && $segment->getAvailed()<$segment->getLimits())) {
                $isAvailable = true;
                break;
            }
        }
        if (!$isAvailable) {
            $spin->setStatus(0)->save();
        }
    }

    /**
     * Set Cookie
     *
     * @param int $spinId
     */
    public function setSpinsCookie($spinId)
    {
        $spins = $this->jsonData->jsonDecode(
            $this->cookieManager->getCookie('spintowin_spins') ?
                $this->cookieManager->getCookie('spintowin_spins'): "[]"
        );
        $spins[] = $spinId;
        $metadata = $this->cookieMetadata->createPublicCookieMetadata()
            ->setDuration(31536000)
            ->setPath($this->sessionManager->getCookiePath())
            ->setDomain($this->sessionManager->getCookieDomain());
        $this->cookieManager->setPublicCookie(
            'spintowin_spins',
            $this->jsonData->jsonEncode($spins),
            $metadata
        );
    }

    /**
     * Set Won Coupon data
     *
     * @param Object $spin
     * @param Object $segment
     * @param Object $coupon
     */
    public function setWonCouponCookie($spin, $segment, $coupon)
    {
        $result = [];
        $result['segment'] = [];
        $result['segment']['label'] = $segment->getLabel();
        $result['segment']['heading'] = $segment->getHeading();
        $result['segment']['description'] = $segment->getDescription();
        $result['segment']['coupon'] = $coupon;
        $result['layout'] = $spin->getLayout()->getData();
        $result['visibility'] = $spin->getVisibility()->getData();
        $result['coupon'] = $spin->getCoupon()->getData();
        $result['result'] = $spin->getResultForm()->getData();
        $result['mediaUrl'] = $this->helper->getMediaDirectory();
        $metadata = $this->cookieMetadata->createPublicCookieMetadata()
                                        ->setDuration(86400*7)
                                        ->setPath($this->sessionManager->getCookiePath())
                                        ->setDomain($this->sessionManager->getCookieDomain());
        $this->cookieManager->setPublicCookie(
            'spintowin_coupon',
            $this->jsonData->jsonEncode($result),
            $metadata
        );
    }
}
