<?php

namespace Synchrony\DigitalBuy\Gateway\Command;

class AuthorizeCommand extends AbstractGatewayCommand
{
    /**
     * @inheritdoc
     */
    protected function processInvalidResult($result, $response)
    {
        $e = new StatusInquiryCommandException(__('Something went wrong, please try again later'));
        if ($result->getFailCode()) {
            $e->setFailCode($result->getFailCode());
        } else {
            $errorMessage = implode("\n", $result->getFailsDescription());
            $this->logger->critical(
                'Got invalid authorization status response: ' . $errorMessage
                . '. API Endpoint was: ' . $this->getClientApiEndpoint()
                . '. Response was: ' . var_export($response, true)
            );
        }
        throw $e;
    }
}
