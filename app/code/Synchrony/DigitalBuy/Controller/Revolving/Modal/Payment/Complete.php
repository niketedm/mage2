<?php

namespace Synchrony\DigitalBuy\Controller\Revolving\Modal\Payment;

use Magento\Framework\App\ResponseInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Synchrony\DigitalBuy\Model\Session as SynchronySession;
use Magento\Framework\App\Action\Context;
use Synchrony\DigitalBuy\Gateway\Config\RevolvingConfig as Config;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectFactory;
use Magento\Sales\Api\PaymentFailuresInterface;
use Synchrony\DigitalBuy\Gateway\Helper\SubjectReader as GatewaySubjectReader;
use Synchrony\DigitalBuy\Gateway\Command\StatusInquiryCommandException;
use Synchrony\DigitalBuy\Gateway\Validator\Revolving\AuthorizationResponseValidator;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Sales\Model\Service\PaymentFailuresService;
use Synchrony\DigitalBuy\Model\Sales\Order\Payment\AdditionalInfoManager as PaymentAdditionalInfoManager;
use Psr\Log\LoggerInterface;

/**
 * Class Complete
 *
 */
class Complete extends \Synchrony\DigitalBuy\Controller\Modal\AbstractPayment
{
    /**
     * @var FormKeyValidator
     */
    private $formKeyValidator;

    /**
     * @var PaymentDataObjectFactory
     */
    private $paymentDataObjectFactory;

    /**
     * @var PaymentAdditionalInfoManager
     */
    private $paymentAdditionalInfoManager;

    /**
     * @var PaymentFailuresInterface
     */
    private $paymentFailures;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Constructor
     *
     * @param Context $context
     * @param SynchronySession $synchronySession
     * @param CheckoutSession $checkoutSession
     * @param Config $config
     * @param DateTime $dateTime
     * @param CommandPoolInterface $commandPool
     * @param FormKeyValidator $formKeyValidator,
     * @param PaymentDataObjectFactory $paymentDataObjectFactory
     * @param PaymentFailuresService $paymentFailures
     * @param PaymentAdditionalInfoManager $paymentAdditionalInfoManager
     * @paran LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        SynchronySession $synchronySession,
        CheckoutSession $checkoutSession,
        Config $config,
        DateTime $dateTime,
        CommandPoolInterface $commandPool,
        FormKeyValidator $formKeyValidator,
        PaymentDataObjectFactory $paymentDataObjectFactory,
        PaymentFailuresService $paymentFailures,
        PaymentAdditionalInfoManager $paymentAdditionalInfoManager,
        LoggerInterface $logger
    ) {
        $this->formKeyValidator = $formKeyValidator;
        $this->paymentDataObjectFactory = $paymentDataObjectFactory;
        $this->paymentAdditionalInfoManager = $paymentAdditionalInfoManager;
        $this->paymentFailures = $paymentFailures;
        $this->logger = $logger;
        parent::__construct($context, $synchronySession, $checkoutSession, $config, $dateTime, $commandPool);
    }

    /**
     * @return ResponseInterface
     */
    public function execute()
    {
        if (!$this->validateOrder()) {
            return $this->redirectToCart();
        }

        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $this->redirectToPaymentStart();
        }

        $token = $this->getRequest()->getParam('tokenId');
        if (!$token || $token != $this->synchronySession->getPaymentToken()) {
            return $this->redirectToPaymentStart();
        }

        $order = $this->checkoutSession->getLastRealOrder();
        $payment = $order->getPayment();
        $this->paymentAdditionalInfoManager->setPayment($payment)
            ->setDigitalBuyToken($token);
        $payment = $this->paymentDataObjectFactory->create($order->getPayment());
        $this->synchronySession->clearStorage();

        try {
            $this->commandPool->get('capture_stratergy')->execute([
                GatewaySubjectReader::PAYMENT_KEY => $payment
            ]);
        } catch (\Exception $e) {
            if ($e instanceof StatusInquiryCommandException && $e->getFailCode()) {
                switch ($e->getFailCode()) {
                    case AuthorizationResponseValidator::STATUS_TOKEN_EXPIRED:
                        return $this->redirectToPaymentStart();
                    case AuthorizationResponseValidator::STATUS_ADDRESS_VERIFICATION_FAILED:
                        $this->messageManager->addErrorMessage(__(
                            'Address mismatch, please make sure address matches data on your bank account'
                        ));
                        break;
                    case AuthorizationResponseValidator::STATUS_TERMINATED:
                    case AuthorizationResponseValidator::STATUS_AUTHORIZATION_DECLINED:
                        break;
                    default:
                        $this->messageManager->addErrorMessage(__('Something went wrong, please try again later'));
                        $this->paymentFailures->handle((int)$order->getQuoteId(), $e->getMessage());
                }
            } else {
                $this->messageManager->addErrorMessage(__('Something went wrong, please try again later'));
                try {
                    $this->paymentFailures->handle((int)$order->getQuoteId(), $e->getMessage());
                } catch (\Exception $failureHandlerE) {
                    $this->logger->critical('Unable send payment failure email: ' . $failureHandlerE->getMessage());
                }
            }
            try {
                $this->commandPool->get('cancel_payment')->execute([GatewaySubjectReader::PAYMENT_KEY => $payment]);
            } catch (\Exception $cancelPaymentE) {
                $this->logger->critical(
                    'Unable to cancel order with failed payment: ' . $cancelPaymentE->getMessage()
                );
            }
            return $this->_redirect('checkout/cart');
        }

        return $this->_redirect('checkout/onepage/success');
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    private function redirectToPaymentStart()
    {
        return $this->_redirect('digitalbuy/revolving/modal_payment');
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
