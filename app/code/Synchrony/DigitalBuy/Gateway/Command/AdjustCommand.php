<?php

namespace Synchrony\DigitalBuy\Gateway\Command;

use Magento\Payment\Gateway\Command\CommandException;

class AdjustCommand extends AbstractGatewayCommand
{
    /**
     * @inheritdoc
     */
    protected function processInvalidResult($result, $response)
    {
        $errorMessage='';
        if (count($result->getFailsDescription())) {
            $errorMessage = implode("\n", $result->getFailsDescription());
        }
        if (!$result->getFailCode()) {
            $this->logger->critical(
                'Got invalid refund api response: ' . $errorMessage
                . '. API Endpoint was: ' . $this->getClientApiEndpoint()
                . '. Response was: ' . var_export($response, true)
            );
        }
        throw new CommandException(
            ($errorMessage)
                ? __($errorMessage)
                : __('Transaction has been declined. Please try again later.')
        );
    }
}
