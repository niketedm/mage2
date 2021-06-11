<?php
namespace Rootways\Authorizecim\Gateway\Response;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Api\Data\OrderPaymentExtensionInterface;
use Magento\Sales\Api\Data\OrderPaymentExtensionInterfaceFactory;
use Magento\Vault\Api\Data\PaymentTokenFactoryInterface;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Rootways\Authorizecim\Observer\DataAssignObserver;

/**
 * Vault Details Handler
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class VaultDetailsHandler implements HandlerInterface
{
    const CUSTOMERPROFILEID = 'CUSTOMERPROFILEID';
    const ACCOUNTTYPE = 'ACCOUNTTYPE';
    const ACCOUNTNUMBER = 'ACCOUNTNUMBER';
    
    /**
     *
     * @var Rootways\Authorizecim\Helper\Data
     */
    public $helper;
    
    /**
     *
     * @var Rootways\Authorizecim\Model\Request\Api
     */
    public $rwApi;
    
    /**
     * @var PaymentTokenFactoryInterface
     */
    protected $paymentTokenFactory;

    /**
     * @var OrderPaymentExtensionInterfaceFactory
     */
    protected $paymentExtensionFactory;

    /**
     * @var Json
     */
    private $serializer;
    
    public function __construct(
        \Rootways\Authorizecim\Helper\Data $helper,
        \Rootways\Authorizecim\Model\Request\Api $rwApi,
        PaymentTokenFactoryInterface $paymentTokenFactory,
        OrderPaymentExtensionInterfaceFactory $paymentExtensionFactory,
        Json $serializer = null
    ) {
        $this->customHelper = $helper;
        $this->rwApi = $rwApi;
        $this->paymentTokenFactory = $paymentTokenFactory;
        $this->paymentExtensionFactory = $paymentExtensionFactory;
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
    }

    /**
     * @inheritdoc
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = SubjectReader::readPayment($handlingSubject);
        $payment = $paymentDO->getPayment();
        
        $saveCard = $payment->getAdditionalInformation(DataAssignObserver::SAVE_CARD);
        if ($saveCard == '1') {
            $cardcollection = $this->customHelper->getSavedCreditCard();
            if (count($cardcollection) > 0 || $cardcollection->getFirstItem()->getGatewayToken() != '') {
                $transId = $response['transactionResponse']['transId'];
                $custProId = $this->customHelper->formatedCustomerId($cardcollection->getFirstItem()->getGatewayToken());
                $newPaymentResponse = $this->rwApi->createPaymentProfileFromTransactionRequest($transId, $custProId);
                $paymentToken = $this->getVaultPaymentTokenExisting($response, $newPaymentResponse, $payment);
            } else {
                $paymentToken = $this->getVaultPaymentToken($response, $payment);
            }
            if (null !== $paymentToken) {
                $extensionAttributes = $this->getExtensionAttributes($payment);
                $extensionAttributes->setVaultPaymentToken($paymentToken);
            }
        }
    }
    
    protected function getVaultPaymentToken($response, $payment)
    {
        $paymentToken = null;
        if (isset($response['profileResponse']['customerProfileId'])) {
            $token = $response['profileResponse']['customerProfileId'].'_'.time();
            if (empty($token)) {
                return null;
            }

            /** @var PaymentTokenInterface $paymentToken */
            $paymentToken = $this->paymentTokenFactory->create(PaymentTokenFactoryInterface::TOKEN_TYPE_CREDIT_CARD);
            $paymentToken->setGatewayToken($token);
            $paymentToken->setExpiresAt('2029-01-01 00:00:00');

            $ccLast4 = substr($response['transactionResponse']['accountNumber'], -4);
            $paymentId = '';
            if (isset($response['profileResponse']['customerPaymentProfileIdList']['numericString'])) {
                $paymentId = $response['profileResponse']['customerPaymentProfileIdList']['numericString'];
            }
            $ccType = $this->customHelper->getCcTypeCodeByName($response['transactionResponse']['accountType']);
            if ($ccType == '') {
                $ccType = $response['transactionResponse']['accountType'];
            }
            $expMon = sprintf("%02d", $payment->getAdditionalInformation(DataAssignObserver::CC_EXP_MONTH));
            $expYr = $payment->getAdditionalInformation(DataAssignObserver::CC_EXP_YEAR);
            $expirationDate = $expMon.'/'.$expYr;
            $paymentToken->setTokenDetails($this->convertDetailsToJSON([
                'type' => $ccType,
                'maskedCC' => $ccLast4,
                'expirationDate' => $expirationDate,
                'paymentProfileId' => $paymentId
            ]));
        }

        return $paymentToken;
    }
    
    protected function getVaultPaymentTokenExisting($response, $newPaymentResponse, $payment)
    {
        $paymentToken = null;
        if (isset($newPaymentResponse['customerProfileId'])) {
            $token = $newPaymentResponse['customerProfileId'].'_'.time();
            if (empty($token)) {
                return null;
            }

            /** @var PaymentTokenInterface $paymentToken */
            $paymentToken = $this->paymentTokenFactory->create(PaymentTokenFactoryInterface::TOKEN_TYPE_CREDIT_CARD);
            $paymentToken->setGatewayToken($token);
            $paymentToken->setExpiresAt('2029-01-01 00:00:00');

            $ccLast4 = substr($response['transactionResponse']['accountNumber'], -4);
            $paymentId = '';
            if (isset($newPaymentResponse['customerPaymentProfileIdList']['numericString'])) {
                $paymentId = $newPaymentResponse['customerPaymentProfileIdList']['numericString'];
            }
            $ccType = $this->customHelper->getCcTypeCodeByName($response['transactionResponse']['accountType']);
            if ($ccType == '') {
                $ccType = $response['transactionResponse']['accountType'];
            }
            $expMon = sprintf("%02d", $payment->getAdditionalInformation(DataAssignObserver::CC_EXP_MONTH));
            $expYr = $payment->getAdditionalInformation(DataAssignObserver::CC_EXP_YEAR);
            $expirationDate = $expMon.'/'.$expYr;
            $paymentToken->setTokenDetails($this->convertDetailsToJSON([
                'type' => $ccType,
                'maskedCC' => $ccLast4,
                'expirationDate' => $expirationDate,
                'paymentProfileId' => $paymentId
            ]));
        }
        return $paymentToken;
    }
    
    private function convertDetailsToJSON($details)
    {
        $json = $this->serializer->serialize($details);
        return $json ? $json : '{}';
    }
    
    private function getExtensionAttributes(InfoInterface $payment)
    {
        $extensionAttributes = $payment->getExtensionAttributes();
        if (null === $extensionAttributes) {
            $extensionAttributes = $this->paymentExtensionFactory->create();
            $payment->setExtensionAttributes($extensionAttributes);
        }
        return $extensionAttributes;
    }
}
