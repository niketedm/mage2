<?php
namespace Rootways\Authorizecim\Gateway\Http;

use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;

/**
 * Class AbstractTransferFactory
 */
abstract class AbstractTransferFactory implements TransferFactoryInterface
{
    protected $transferBuilder;
    protected $generator;
    private $helper;
    
    public function __construct(
        TransferBuilder $transferBuilder,
        \Rootways\Authorizecim\Helper\Data $helper
    ) {
        $this->transferBuilder = $transferBuilder;
        $this->customHelper = $helper;
    }
    
    public function getUrl($additionalPath = '')
    {
         return $this->customHelper->getGatewayUrl();
    }
    
    protected function convertToXml($array, $rootElement = '<?xml version="1.0" encoding="utf-8"?><createTransactionRequest/>', $xml = null)
    {
        $_xml = $xml; 
      
        // If there is no Root Element then insert root 
        if ($_xml === null) {
            $_xml = new \SimpleXMLElement($rootElement !== null ? $rootElement : '<root/>');
            $_xml->addAttribute('xmlns', 'AnetApi/xml/v1/schema/AnetApiSchema.xsd');
        }
        
        // Visit all key value pair
        foreach ($array as $k => $v) {
            // If there is nested array then
            if (is_array($v)) {
                if ($k == 'lineItem') {
                    foreach($v as $item) {
                        $this->convertToXml($item, 'lineItem', $_xml->addChild('lineItem'));
                    }
                } else {
                    // Call function for nested array
                    $this->convertToXml($v, $k, $_xml->addChild($k));    
                }
            } else {
                $v = htmlspecialchars($v);
                // Simply add child element.
                $_xml->addChild($k, $v);
            }
        }
        
        return $_xml->asXML();
    }
}
