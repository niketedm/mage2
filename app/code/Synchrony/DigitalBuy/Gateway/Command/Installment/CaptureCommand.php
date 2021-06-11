<?php

namespace Synchrony\DigitalBuy\Gateway\Command\Installment;

use Synchrony\DigitalBuy\Gateway\Helper\SubjectReader;
use Synchrony\DigitalBuy\Gateway\Command\AbstractGatewayCommand;

/**
 * Class CaptureCommand
 *
 * @package Synchrony\DigitalBuy\Gateway\Command
 */
class CaptureCommand extends AbstractGatewayCommand
{
    /**
     * Executes Capture Gateway request
     *
     * @param array $commandSubject
     * @return void
     */
    public function execute(array $commandSubject)
    {
        $paymentDO = SubjectReader::readPayment($commandSubject);
        $payment = $paymentDO->getPayment();
        $payment->setTransactionId(null);
        $payment->resetTransactionAdditionalInfo();
        $payment->setIsTransactionClosed(false);
        parent::execute($commandSubject);
    }

    /**
     * Process API error response and throw exception
     *
     * @param $result
     * @param $response
     * @throws InstallmentCaptureCommandException
     */
    protected function processInvalidResult($result, $response)
    {
        $errorMessage = implode("\n", $result->getFailsDescription());
        $e = new InstallmentCaptureCommandException(__('Installment Capture failed: %1', $errorMessage));
        if ($result->getFailCode()) {
            $e->setFailCode($result->getFailCode());
        } else {
            $this->logger->critical(
                'Recieved invalid Capture API response: ' . $errorMessage
                . '. API Endpoint was: ' . $this->getClientApiEndpoint()
                . '. Response was: ' . var_export($response, true)
            );
        }
        throw $e;
    }
}
