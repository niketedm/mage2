<?php

namespace Synchrony\DigitalBuy\Gateway\Response;

use Magento\Framework\DataObject;

class AuthenticationReader extends DataObject
{
    const CLIENT_TOKEN_KEY = 'clientToken';
    const POSTBACK_ID_KEY = 'postbackid';

    /**
     * Check if response contains client token
     *
     * @return bool
     */
    public function hasClientToken()
    {
        return $this->hasData(self::CLIENT_TOKEN_KEY);
    }

    /**
     * Retrieve client token from response
     *
     * @param bool $silent flag to either throw an exception or not if key not found in response
     * @return string|null
     */
    public function getClientToken()
    {
        return $this->getData(self::CLIENT_TOKEN_KEY);
    }

    /**
     * Check if response contains postback id
     *
     * @return bool
     */
    public function hasPostbackId()
    {
        return $this->hasData(self::POSTBACK_ID_KEY);
    }

    /**
     * Retrieve postback id from response
     *
     * @return string|null
     */
    public function getPostbackId()
    {
        return $this->getData(self::POSTBACK_ID_KEY);
    }
}
