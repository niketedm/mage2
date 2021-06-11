<?php

namespace Synchrony\DigitalBuy\Gateway\Http;

use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Synchrony\DigitalBuy\Gateway\Request\StoreIdDataBuilder;

class TransferFactory implements TransferFactoryInterface
{
    /**
     * @var TransferBuilder
     */
    private $transferBuilder;

    /**
     * @param TransferBuilder $transferBuilder
     */
    public function __construct(
        TransferBuilder $transferBuilder
    ) {
        $this->transferBuilder = $transferBuilder;
    }

    /**
     * Builds gateway transfer object
     *
     * @param array $request
     * @return TransferInterface
     */
    public function create(array $request)
    {
        $storeId = null;
        if (array_key_exists(StoreIdDataBuilder::STORE_ID_KEY, $request)) {
            $storeId = $request[StoreIdDataBuilder::STORE_ID_KEY];
            unset($request[StoreIdDataBuilder::STORE_ID_KEY]);
        }

        return $this->transferBuilder
            ->setClientConfig([StoreIdDataBuilder::STORE_ID_KEY => $storeId])
            ->setBody($request)
            ->build();
    }
}
