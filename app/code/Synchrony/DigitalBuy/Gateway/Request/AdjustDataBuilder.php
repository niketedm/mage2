<?php

namespace Synchrony\DigitalBuy\Gateway\Request;

use Synchrony\DigitalBuy\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Class AdjustDataBuilder
 */
class AdjustDataBuilder implements BuilderInterface
{
    const ADJUSTMENT_AMOUNT_KEY = 'AdjustmentAmount';

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        return [
            self::ADJUSTMENT_AMOUNT_KEY => SubjectReader::readAmount($buildSubject)
        ];
    }
}
