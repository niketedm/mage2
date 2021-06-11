<?php

namespace Synchrony\DigitalBuy\Controller\Revolving\Modal;

use Magento\Checkout\Model\Session as CheckoutSession;
use Synchrony\DigitalBuy\Model\Session as SynchronySession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Model\MethodInterface;
use Synchrony\DigitalBuy\ViewModel\Modal\Revolving\Auth as ViewModel;
use Magento\Payment\Gateway\Command\CommandPoolInterface;

/**
 * Class Auth
 * @package Synchrony\DigitalBuy\Controller\Modal
 */
class Auth extends \Magento\Framework\App\Action\Action
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var SynchronySession
     */
    private $synchronySession;

    /**
     * @var MethodInterface
     */
    private $paymentMethod;

    /**
     * @var ViewModel
     */
    private $viewModel;

    /**
     * @var CommandPoolInterface
     */
    private $commandPool;

    /**
     * Auth constructor.
     * @param Context $context
     * @param CheckoutSession $checkoutSession
     * @param SynchronySession $synchronySession
     * @param MethodInterface $paymentMethod
     * @param CommandPoolInterface $commandPool
     * @param ViewModel $viewModel
     */
    public function __construct(
        Context $context,
        CheckoutSession $checkoutSession,
        SynchronySession $synchronySession,
        MethodInterface $paymentMethod,
        CommandPoolInterface $commandPool,
        ViewModel $viewModel
    ) {
        $this->paymentMethod = $paymentMethod;
        $this->checkoutSession = $checkoutSession;
        $this->synchronySession = $synchronySession;
        $this->commandPool = $commandPool;
        $this->viewModel = $viewModel;
        parent::__construct($context);
    }

    /**
     * @return void
     */
    public function execute()
    {
        //Verify payment method available
        $quote = $this->checkoutSession->getQuote();
        if (!$this->paymentMethod->isAvailable($quote)
            || !$quote->getItemsCount() || !$this->paymentMethod->canUseForCart($quote)) {
            return $this->_redirect('checkout/cart/');
        }

        try {
            $result = $this->commandPool->get('get_token')->execute([
                \Synchrony\DigitalBuy\Gateway\Helper\SubjectReader::STORE_ID_KEY => $quote->getStoreId()
            ]);
            $token = $result->get()[\Synchrony\DigitalBuy\Gateway\Response\AuthenticationReader::CLIENT_TOKEN_KEY];
            $this->synchronySession->setPreAuthToken($token);
            $this->viewModel->setDigitalBuyTokenToRegistry($token);
        } catch (\Exception $e) {
            $message = $e instanceof LocalizedException
                ? $e->getMessage() : __('Something went wrong, please try again later');
            $this->messageManager->addErrorMessage($message);
            return $this->_redirect('checkout/cart/');
        }

        $this->_view->loadLayout($this->_view->getDefaultLayoutHandle(), true, true, false);
        $this->_view->renderLayout();
    }
}
