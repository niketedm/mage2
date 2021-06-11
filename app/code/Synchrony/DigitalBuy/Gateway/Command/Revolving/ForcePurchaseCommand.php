<?php

namespace Synchrony\DigitalBuy\Gateway\Command\Revolving;

class ForcePurchaseCommand extends \Synchrony\DigitalBuy\Gateway\Command\AbstractGatewayCommand
{
    /**
     * @inheritdoc
     */
    protected function processInvalidResult($result, $response)
    {
        $errorMessage = implode("\n", $result->getFailsDescription());
        $e = new ForcePurchaseCommandExeption(__('Force Purchase failed: %1', $errorMessage));
        if ($result->getFailCode()) {
            $e->setFailCode($result->getFailCode());
        } else {
            $this->logger->critical(
                'Got invalid force purchase response: ' . $errorMessage
                . '. API Endpoint was: ' . $this->getClientApiEndpoint()
                . '. Response was: ' . var_export($response, true)
            );
        }
        throw $e;
    }
}
