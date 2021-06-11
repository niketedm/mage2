<?php

namespace Synchrony\DigitalBuy\Gateway\Request;

use Synchrony\DigitalBuy\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Class PromoCodeDataBuilder
 */
class PromoCodeDataBuilder implements BuilderInterface
{
    const PROMO_CODE_KEY = 'PromoCode';

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        return [
            self::PROMO_CODE_KEY => SubjectReader::readPromoCode($buildSubject),
        ];
    }
}
