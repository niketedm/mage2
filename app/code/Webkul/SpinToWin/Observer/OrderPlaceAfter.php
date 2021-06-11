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

 namespace Webkul\SpinToWin\Observer;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;

class OrderPlaceAfter implements \Magento\Framework\Event\ObserverInterface
{
    public function __construct(
        \Webkul\SpinToWin\Logger\Logger $logger,
        CustomerSession $customerSession,
        CheckoutSession $checkoutSession,
        \Webkul\SpinToWin\Model\SegmentsFactory $segmentsFactory,
        \Webkul\SpinToWin\Model\ReportsFactory $reportsFactory
    ) {
        $this->logger = $logger;
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->segmentsFactory = $segmentsFactory;
        $this->reportsFactory = $reportsFactory;
    }

    /**
     * Main
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $orders = [];
            if ($observer->getOrder()) {
                $orders = [$observer->getOrder()];
            } elseif ($observer->getOrders()) {
                $orders = $observer->getOrders();
            }
            $spinRuleId = $this->checkoutSession->getWkSpinRule();
            foreach ($orders as $order) {
                $lastOrderId = $order->getId();
                $quoteId = $order->getQuoteId();
                $originalAppliedRules = explode(",", $order->getAppliedRuleIds());
                $orderItems = $order->getAllVisibleItems();
                if (in_array($spinRuleId, $originalAppliedRules)) {
                    $segment = $this->segmentsFactory->create()->load($spinRuleId, 'rule_id');
                    if ($segment) {
                        $segmentId = $segment->getId();
                        $couponCode = $order->getCouponCode();
                        $reports = $this->reportsFactory->create()
                                                    ->getCollection()
                                                    ->addFieldToFilter('segment_id', $segmentId)
                                                    ->addFieldToFilter('coupon', $couponCode);
                        if ($reports->getSize()) {
                            $report = $reports->getLastItem();
                            $total = $order->getBaseGrandTotal();
                            $report->setStatus(2)
                            ->setOrderId($lastOrderId)
                            ->setOrderAmount($total)
                            ->save();
                        }
                    }
                }
            }
            $this->checkoutSession->unsWkSpinRule();
        } catch (\Exception $e) {
            $this->checkoutSession->unsWkSpinRule();
            $this->logger->info($e->getMessage());
            $this->logger->info('Issue with Order id ', $lastOrderId);
        }
    }
}
