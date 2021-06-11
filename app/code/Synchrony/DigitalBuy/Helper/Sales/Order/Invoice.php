<?php

namespace Synchrony\DigitalBuy\Helper\Sales\Order;

use Magento\Framework\Message\MessageInterface;
use Synchrony\DigitalBuy\Gateway\Config\RevolvingConfig as Config;

/**
 * Class Invoice Helper
 */
class Invoice
{
    /**
     * Message model factory
     *
     * @var \Magento\Framework\Message\Factory
     */
    private $messageFactory;

    /**
     * Message collection factory
     *
     * @var \Magento\Framework\Message\CollectionFactory
     */
    private $messageCollectionFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry = null;

    /**
     * @param \Magento\Framework\Message\Factory $messageFactory
     * @param \Magento\Framework\Message\CollectionFactory $messageCollectionFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\Message\Factory $messageFactory,
        \Magento\Framework\Message\CollectionFactory $messageCollectionFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->messageFactory = $messageFactory;
        $this->messageCollectionFactory = $messageCollectionFactory;
        $this->coreRegistry = $registry;
    }

    /**
     * Add a notice if partial invoice cannot be created
     *
     * @return \Magento\Framework\Message\Collection
     */
    public function addPartialInvoiceNotice()
    {
        $messageCollection = $this->messageCollectionFactory->create();
        $invoice = $this->getInvoice();
        $payment = $invoice->getOrder()->getPayment();
        if ($invoice instanceof \Magento\Sales\Model\Order\Invoice
            && $this->isSynchronyPayment($payment) && !$payment->canCapturePartial()) {
            $notice = __(
                'NOTE: Because multiple Synchrony promotions have been applied to this order,'
                . ' partial invoicing is not available.'
            );
            $message = $this->messageFactory->create(MessageInterface::TYPE_NOTICE, $notice);
            $messageCollection->addMessage($message);
        }
        return $messageCollection;
    }

    /**
     * Retrieve invoice model instance from registry
     *
     * @return \Magento\Sales\Model\Order\Invoice
     */
    private function getInvoice()
    {
        return $this->coreRegistry->registry('current_invoice');
    }

    /**
     * Check if it's synchrony payment method
     *
     * @param \Magento\Sales\Model\Order\Payment $payment
     * @return bool
     */
    private function isSynchronyPayment($payment)
    {
        return $payment->getMethod() == Config::METHOD_CODE;
    }
}
