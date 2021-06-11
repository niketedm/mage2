<?php
namespace Rootways\Authorizecim\Gateway\Http\Client;

use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Framework\HTTP\ZendClient;
use Magento\Payment\Gateway\Http\ConverterInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
//use Magento\Payment\Model\Method\Logger;

/**
 * Class Zend
 */
class Zend extends \Magento\Payment\Gateway\Http\Client\Zend
{
    /**
     * @var ZendClientFactory
     */
    private $clientFactory;

    /**
     * @var ConverterInterface | null
     */
    private $converter;
    
    /**
     * @var \Rootways\Authorizecim\Helper\Data
     */
    private $customHelper;

    /**
     * @param ZendClientFactory $clientFactory
     * @param ConverterInterface | null $converter
     * @param \Rootways\Authorizecim\Helper\Data $customHelper
     */
    public function __construct(
        ZendClientFactory $clientFactory,
        ConverterInterface $converter = null,
        \Rootways\Authorizecim\Helper\Data $customHelper
    ) {
        $this->clientFactory = $clientFactory;
        $this->converter = $converter;
        $this->customHelper = $customHelper;
    }

    /**
     * @inheritdoc
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        $logInfo = [
            'request_body' => $transferObject->getBody(),
            'request_url' => $transferObject->getUri()
        ];
        $resultNew = [];
        /** @var ZendClient $client */
        $client = $this->clientFactory->create();
        $client->setConfig($transferObject->getClientConfig());
        $client->setMethod($transferObject->getMethod());
        $client->setRawData($transferObject->getBody());

        $client->setHeaders($transferObject->getHeaders());
        //$client->setUrlEncodeBody($transferObject->shouldEncode());
        $client->setUri($transferObject->getUri());

        try {
            $response = $client->request();
            $new = simplexml_load_string($response->getBody(),'SimpleXMLElement', LIBXML_NOWARNING);
            $con = json_encode($new);
            $resultNew = json_decode($con, true);
            $logInfo['response_body'] = $resultNew;
        } catch (\Zend_Http_Client_Exception $e) {
            throw new \Magento\Payment\Gateway\Http\ClientException(
                __($e->getMessage())
            );
        } catch (\Magento\Payment\Gateway\Http\ConverterException $e) {
            throw $e;
        } finally {
            if ($this->customHelper->getConfig('payment/rootways_authorizecim_basic/debug')) {
                $reqOut = $this->delete_all_between('<merchantAuthentication>', '</merchantAuthentication>', $transferObject->getBody());
                $reqOut = $this->delete_all_between('<payment>', '</payment>', $reqOut);
                $resOut = $resultNew;
                $this->rwLogger($reqOut, $resOut);
            }
            
        }

        return $resultNew;
    }
    
    function delete_all_between($beginning, $end, $string)
    {
        $beginningPos = strpos($string, $beginning);
        $endPos = strpos($string, $end);
        if ($beginningPos === false || $endPos === false) {
            return $string;
        }
        
        $textToDelete = substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos);
        
        return $this->delete_all_between($beginning, $end, str_replace($textToDelete, '', $string)); // recursion to ensure all occurrences are replaced
    }
    
    public function rwLogger($req, $res)
    {
        $logger = new \Zend\Log\Logger();
        $rwLog = new \Zend\Log\Writer\Stream(BP.'/var/log/rw_authorizenet.log');
        $logger->addWriter($rwLog);
        $logger->info("#######Request#######");
        $logger->info($req);
        $logger->info("#######Response#######");
        $logger->info($res);
    }
}
