<?php
namespace Rootways\Authorizecim\Gateway\Http;

/**
 * Class TransferFactory
 */
class ReviewPaymentTransferFactory extends AbstractTransferFactory
{
    public function create(array $request)
    {
        $rootElement = '<?xml version="1.0" encoding="utf-8"?><updateHeldTransactionRequest/>';
        $xmlData = $this->convertToXml($request, $rootElement);
        return $this->transferBuilder
            ->setMethod('POST')
            ->setHeaders(['Content-type' => 'Content-Type: text/xml'])
            ->setBody($xmlData)
            ->setUri($this->getUrl())
            ->build();
    }
}
