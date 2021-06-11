<?php
namespace PowerReviews\ReviewDisplay\Block;

class Beacon extends \Magento\Sales\Block\Order\Totals
{
    protected $checkoutSession;
    protected $customerSession;
    protected $_orderFactory;

    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->_orderFactory = $orderFactory;
        parent::__construct($context, $registry, $data);
    }

    public function getOrder()
    {
        return $this->_orderFactory->create()
                    ->loadByIncrementId($this->checkoutSession->getLastRealOrderId());
    }

    public function getCustomerId()
    {
        return $this->customerSession->getCustomer()->getId();
    }
}
