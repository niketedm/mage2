<?php
namespace Rootways\Authorizecim\Gateway\Http;

/**
 * Class TransferFactory
 */
class TransferFactory extends AbstractTransferFactory
{
    public function create(array $request)
    {
        $xmlData = $this->convertToXml($request);
        return $this->transferBuilder
            ->setMethod('POST')
            ->setHeaders(['Content-type' => 'Content-Type: text/xml'])
            ->setBody($xmlData)
            ->setUri($this->getUrl())
            ->build();
    }
}
