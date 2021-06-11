<?php

namespace Synchrony\DigitalBuy\Controller\Modal;

use Magento\Framework\App\Action\Context;
use Synchrony\DigitalBuy\Model\Session as SynchronySession;
use Magento\Checkout\Model\Session as CheckoutSession;
use Synchrony\DigitalBuy\Gateway\Config\AbstractPaymentConfig as Config;
use Magento\Sales\Model\Order;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Payment\Gateway\Command\CommandPoolInterface;

abstract class AbstractPayment extends \Magento\Framework\App\Action\Action
{
    /**
     * @var SynchronySession
     */
    protected $synchronySession;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var CommandPoolInterface
     */
    protected $commandPool;

    /**
     * AbstractPayment constructor.
     * @param Context $context
     * @param SynchronySession $synchronySession
     * @param CheckoutSession $checkoutSession
     * @param Config $config
     * @param DateTime $dateTime
     * @param CommandPoolInterface $commandPool
     */
    public function __construct(
        Context $context,
        SynchronySession $synchronySession,
        CheckoutSession $checkoutSession,
        Config $config,
        DateTime $dateTime,
        CommandPoolInterface $commandPool
    ) {
        $this->synchronySession = $synchronySession;
        $this->checkoutSession = $checkoutSession;
        $this->config = $config;
        $this->date = $dateTime;
        $this->commandPool = $commandPool;
        parent::__construct($context);
    }

    /**
     * Validate current order
     *
     * @return bool
     */
    protected function validateOrder()
    {
        $order = $this->checkoutSession->getLastRealOrder();
        if (!$order->getId()
            || !$order->getPayment() || $order->getPayment()->getMethod() != $this->getMethodCode()
            || $order->getState() != Order::STATE_PENDING_PAYMENT) {
            return false;
        }

        return true;
    }

    /**
     * Get code of payment method in question
     *
     * @return string
     */
    abstract protected function getMethodCode();

    /**
     * Redirect to cart
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function redirectToCart()
    {
        return $this->_redirect('checkout/cart');
    }
}
