<?php
namespace Rootways\Authorizecim\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Rootways\Authorizecim\Helper\Data;

/**
 * Class CustomerIpDataBuilder
 */
class CustomerIpDataBuilder implements BuilderInterface
{
    /**
     * @var Data
     */
    private $helper;
    
    /**
     * @param Data $helper
     */
    public function __construct(
        Data $helper
    )
    {
        $this->customHelper = $helper;
    }
    
    /**
     * Build request by adding IP address of the customer.
     */
    public function build(array $buildSubject)
    {
        $result['transactionRequest'] = [
            'customerIP' => $this->customHelper->getCustomerIp(),
            'transactionSettings' => [
                'setting' => [
                    'settingName' => 'duplicateWindow',
                    'settingValue' => \Rootways\Authorizecim\Model\SampleConfigProvider::DUPLICATE_WINDOW
                ]
            ]
        ];

        return $result;
    }
}
