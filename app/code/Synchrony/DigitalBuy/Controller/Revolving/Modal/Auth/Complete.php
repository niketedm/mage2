<?php

namespace Synchrony\DigitalBuy\Controller\Revolving\Modal\Auth;

use Magento\Framework\View\Result\PageFactory;
use Magento\Checkout\Model\Session as CheckoutSession;
use Synchrony\DigitalBuy\Model\Session as SynchronySession;
use Magento\Framework\App\Action\Context;
use Synchrony\DigitalBuy\Gateway\Config\RevolvingConfig as Config;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Synchrony\DigitalBuy\Gateway\Helper\SubjectReader as GatewaySubjectReader;
use Magento\Framework\DataObject;
use Synchrony\DigitalBuy\Gateway\Command\StatusInquiryCommandException;
use Synchrony\DigitalBuy\Gateway\Validator\AuthenticationStatusInquiryResponseValidator;

/**
 * Class Complete
 * @package Synchrony\DigitalBuy\Controller\Modal\Revolving\Auth
 */
class Complete extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * @var DateTime
     */
    private $date;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    private $formKeyValidator;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var SynchronySession
     */
    private $synchronySession;

    /**
     * @var Config
     */
    private $synchronyConfig;

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var CommandPoolInterface
     */
    private $commandPool;

    /**
     * Complete constructor.
     * @param Context $context
     * @param PageFactory $pageFactory
     * @param DateTime $dateTime
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param CheckoutSession $checkoutSession
     * @param SynchronySession $synchronySession
     * @param Config $synchronyConfig
     * @param \Magento\Framework\Registry $coreRegistry
     * @param CommandPoolInterface $commandPool
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        DateTime $dateTime,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        CheckoutSession $checkoutSession,
        SynchronySession $synchronySession,
        Config $synchronyConfig,
        \Magento\Framework\Registry $coreRegistry,
        CommandPoolInterface $commandPool
    ) {
        $this->pageFactory = $pageFactory;
        $this->date = $dateTime;
        $this->formKeyValidator = $formKeyValidator;
        $this->checkoutSession = $checkoutSession;
        $this->synchronySession = $synchronySession;
        $this->synchronyConfig = $synchronyConfig;
        $this->coreRegistry = $coreRegistry;
        $this->commandPool = $commandPool;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $this->pageFactory->create();
        }

        $token = $this->getRequest()->getParam('tokenId');
        if (!$token || $token != $this->synchronySession->getPreAuthToken()) {
            return $this->pageFactory->create();
        }

        $tokenTimestamp = (int)$this->getRequest()->getParam('timestamp') ?: $this->date->gmtTimestamp();

        // Syntetic check for token expiration
        if ($tokenTimestamp > 0 && $this->synchronyConfig->getTokenExpInterval() > 0
            && $this->synchronyConfig->getTokenExpInterval() < ($this->date->gmtTimestamp() - $tokenTimestamp)) {
            $this->messageManager->addErrorMessage(__('Authentication session expired, please try again'));
            return $this->pageFactory->create();
        }

        $quote = $this->checkoutSession->getQuote();
        $this->synchronySession->resetAuth();
        $addressData = new DataObject();

        try {
            $this->commandPool->get('authenticate')->execute([
                GatewaySubjectReader::TOKEN_ID_KEY => $token,
                GatewaySubjectReader::TOKEN_TIMESTAMP_KEY => $tokenTimestamp,
                GatewaySubjectReader::QUOTE_KEY => $quote,
                GatewaySubjectReader::SESSION_KEY => $this->synchronySession,
                GatewaySubjectReader::UPDATED_ADDRESS_STORAGE_KEY => $addressData
            ]);
        } catch (\Exception $e) {
            if ($e instanceof StatusInquiryCommandException && $e->getFailCode()) {
                if ($e->getFailCode() == AuthenticationStatusInquiryResponseValidator::STATUS_TOKEN_EXPIRED) {
                    $this->messageManager->addErrorMessage(__('Authentication session expired, please try again'));
                }
            } else {
                $this->messageManager->addErrorMessage(__('Something went wrong, please try again later'));
            }
            return $this->pageFactory->create();
        }

        $this->coreRegistry->register(
            \Synchrony\DigitalBuy\ViewModel\Modal\Revolving\Auth::AUTH_SUCCESS_REGISTRY_KEY,
            true,
            true
        );
        $this->coreRegistry->register(
            \Synchrony\DigitalBuy\ViewModel\Modal\Revolving\Auth::ADDRESS_DATA_REGISTRY_KEY,
            $addressData->getData(),
            true
        );

        return $this->pageFactory->create();
    }
}
