<?php

namespace Synchrony\DigitalBuy\Controller\Installment\Modal;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Synchrony\DigitalBuy\Gateway\Config\InstallmentConfig as Config;
use Synchrony\DigitalBuy\Model\Session as SynchronySession;
use Synchrony\DigitalBuy\ViewModel\Modal\Installment\Payment as ViewModel;

/**
 * Class Payment
 * @package Synchrony\DigitalBuy\Controller\Modal
 */
class Payment extends \Synchrony\DigitalBuy\Controller\Modal\AbstractPayment
{
    /**
     * @var ViewModel
     */
    private $viewModel;

    /**
     * Payment constructor.
     * @param Context $context
     * @param SynchronySession $synchronySession
     * @param CheckoutSession $checkoutSession
     * @param Config $config
     * @param DateTime $dateTime
     * @param CommandPoolInterface $commandPool
     * @param ViewModel $viewModel
     */
    public function __construct(
        Context $context,
        SynchronySession $synchronySession,
        CheckoutSession $checkoutSession,
        Config $config,
        DateTime $dateTime,
        CommandPoolInterface $commandPool,
        ViewModel $viewModel
    ) {
        $this->viewModel = $viewModel;
        parent::__construct($context, $synchronySession, $checkoutSession, $config, $dateTime, $commandPool);
    }

    /**
     * @return void
     */
    public function execute()
    {
        if (!$this->validateOrder()) {
            return $this->redirectToCart();
        }
        // Installment Modal
        $order = $this->checkoutSession->getLastRealOrder();
        try {
            $result = $this->commandPool->get('get_token')->execute([
                \Synchrony\DigitalBuy\Gateway\Helper\SubjectReader::STORE_ID_KEY => $order->getStoreId()
            ]);
            $token = $result->get()[\Synchrony\DigitalBuy\Gateway\Response\AuthenticationReader::CLIENT_TOKEN_KEY];
        } catch (\Exception $e) {
            $message = $e instanceof LocalizedException
                ? $e->getMessage() : __('Something went wrong, please try again later');
            $this->messageManager->addErrorMessage($message);
            return $this->redirectToCart();
        }

        $this->synchronySession->setPaymentToken($token);
        $this->viewModel->setDigitalBuyTokenToRegistry($token);

        $this->_view->loadLayout($this->_view->getDefaultLayoutHandle(), true, true, false);
        $this->_view->renderLayout();
    }

    /**
     * Get payment method code
     *
     * @return string
     */
    protected function getMethodCode()
    {
        return Config::METHOD_CODE;
    }
}
