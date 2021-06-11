<?php

namespace Synchrony\DigitalBuy\Model;

class Session extends \Magento\Framework\Session\SessionManager
{
    /**
     * Set authentication token (the one confirmed as authenticated)
     *
     * @param string $token
     * @return $this
     */
    public function setAuthToken($token)
    {
        $this->storage->setData('token', $token);
        return $this;
    }

    /**
     * Unset authentication token
     *
     * @return $this
     */
    public function unsAuthToken()
    {
        $this->storage->unsetData('token');
        return $this;
    }

    /**
     * Get authentication token
     *
     * @return string|null
     */
    public function getAuthToken()
    {
        return $this->storage->getData('token');
    }

    /**
     * Set authentication token timesamp
     *
     * @param int $timestamp
     * @return $this
     */
    public function setAuthTokenTimestamp($timestamp)
    {
        $this->storage->setData('token_timestamp', (int)$timestamp);
        return $this;
    }

    /**
     * Unset authentication token timesamp
     *
     * @return $this
     */
    public function unsAuthTokenTimestamp()
    {
        $this->storage->unsetData('token_timestamp');
        return $this;
    }

    /**
     * Get authentication token timestamp
     *
     * @return int|null
     */
    public function getAuthTokenTimestamp()
    {
        return $this->storage->getData('token_timestamp');
    }

    /**
     * Set synchrony account number
     *
     * @param string $accountNumber
     * @return $this
     */
    public function setAccountNumber($accountNumber)
    {
        $this->storage->setData('account_number', $accountNumber);
        return $this;
    }

    /**
     * Unset synchrony account number
     *
     * @return $this
     */
    public function unsAccountNumber()
    {
        $this->storage->unsetData('account_number');
        return $this;
    }

    /**
     * Get synchrony account number
     *
     * @return string|null
     */
    public function getAccountNumber()
    {
        return $this->storage->getData('account_number');
    }

    /**
     * Unset consumer authentication releated data
     *
     * @return $this
     */
    public function resetAuth()
    {
        $this->unsAuthToken()
            ->unsAuthTokenTimestamp()
            ->unsAccountNumber();
        return $this;
    }

    /**
     * Set authentication token
     *
     * @param string $token
     * @return $this
     */
    public function setPreAuthToken($token)
    {
        $this->storage->setData('pre_auth_token', $token);
        return $this;
    }

    /**
     * Unset authentication token
     *
     * @return $this
     */
    public function unsPreAuthToken()
    {
        $this->storage->unsetData('pre_auth_token');
        return $this;
    }

    /**
     * Get authentication token
     *
     * @return string|null
     */
    public function getPreAuthToken()
    {
        return $this->storage->getData('pre_auth_token');
    }

    /**
     * Set payment token (one used for order completion/combined modal)
     *
     * @param string $token
     * @return $this
     */
    public function setPaymentToken($token)
    {
        $this->storage->setData('payment_token', $token);
        return $this;
    }

    /**
     * Unset payment token
     *
     * @return $this
     */
    public function unsPaymentToken()
    {
        $this->storage->unsetData('payment_token');
        return $this;
    }

    /**
     * Get payment token
     *
     * @return string|null
     */
    public function getPaymentToken()
    {
        return $this->storage->getData('payment_token');
    }
}
