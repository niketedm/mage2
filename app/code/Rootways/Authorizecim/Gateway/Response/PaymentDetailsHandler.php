<?php
namespace Rootways\Authorizecim\Gateway\Response;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order\Payment;
use Rootways\Authorizecim\Helper\Data as CustomHelper;
use Rootways\Authorizecim\Model\Request\Api as CustomApi;
use Rootways\Authorizecim\Observer\DataAssignObserver;

/**
 * Payment Details Handler
 */
class PaymentDetailsHandler implements HandlerInterface
{
    const TRANSID = 'TRANSID';
    const ACCOUNTNUMBER = 'ACCOUNTNUMBER';
    
    /**
     * @var CustomHelper
     */
    private $customHelper;
    
    /**
     * @var CustomApi
     */
    private $customApi;
    
    public function __construct(
        CustomHelper $customHelper,
        CustomApi $customApi
    ) {
        $this->customHelper = $customHelper;
        $this->customApi = $customApi;
    }
    
    /**
     * @inheritdoc
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = SubjectReader::readPayment($handlingSubject);
        $payment = $paymentDO->getPayment();
        $transId = $response['transactionResponse']['transId'];
        $payment->setTransactionId($transId);
        $payment->setCcTransId($transId);
        $payment->setLastTransId($transId);
        $payment->setAdditionalInformation('transaction_id',$transId);
        if (!empty($response['transactionResponse']['authorization'])) {
            $payment->setAdditionalInformation('auth_code', $response['transactionResponse']['authorization']);
        }        
        if (isset($response['transactionResponse']['accountNumber'])) {
            $payment->setCcLast4(substr($response['transactionResponse']['accountNumber'], -4));
        }
        if (!empty($response['transactionResponse']['accountType'])) {
            $ccType = $this->customHelper->getCcCodeByName($response['transactionResponse']['accountType']);
            if ($ccType) {
                $payment->setCcType($ccType);
            }
        }
        $payment->setCcStatus($response['transactionResponse']['responseCode']);
        
        $payment->unsAdditionalInformation(DataAssignObserver::ACCEPTJS_DATA_VALUE);
        $payment->unsAdditionalInformation(DataAssignObserver::ACCEPTJS_DATA_DESCRIPTOR);
        $payment->unsAdditionalInformation(DataAssignObserver::CC_NUMBER);
        $payment->unsAdditionalInformation(DataAssignObserver::CC_CID);
        $payment->unsAdditionalInformation(DataAssignObserver::CC_EXP_MONTH);
        $payment->unsAdditionalInformation(DataAssignObserver::CC_EXP_YEAR);
        $payment->unsAdditionalInformation(DataAssignObserver::G_CAPTCHA);

        $payment->setIsTransactionClosed($this->shouldCloseTransaction());
        $closed = $this->shouldCloseParentTransaction($payment);
        $payment->setShouldCloseParentTransaction($closed);
        
        if ($response['transactionResponse']['responseCode'] == '4') {
            $payment->setIsTransactionPending(true);
        }
    }
    
    /**
     * Whether transaction should be closed
     *
     * @return bool
     */
    protected function shouldCloseTransaction(): bool
    {
        return false;
    }

    /**
     * Whether parent transaction should be closed
     *
     * @param Payment $orderPayment
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function shouldCloseParentTransaction(Payment $payment): bool
    {
        return false;
    }
}
