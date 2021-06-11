<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-gdpr
 * @version   1.1.1
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\GdprConsent\Api\Data;

interface ConsentInterface
{
    const TYPE_COOKIE       = 'cookie';
    const TYPE_REGISTRATION = 'registration';
    const TYPE_SUBSCRIPTION = 'subscription';
    const TYPE_CONTACT_US   = 'contact_us';
    const TYPE_CHECKOUT     = 'checkout';

    const STATUS_ALLOW = 'allow';

    const TABLE_NAME = 'mst_gdpr_consent';

    const ID          = 'consent_id';
    const CUSTOMER_ID = 'customer_id';
    const REMOTE_ADDR = 'remote_addr';
    const CREATED_AT  = 'created_at';
    const TYPE        = 'type';
    const STATUS      = 'status';

    public function getId();

    /**
     * @return int
     */
    public function getCustomerId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setCustomerId($value);

    /**
     * @return string
     */
    public function getRemoteAddr();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setRemoteAddr($value);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setType($value);

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setStatus($value);
}
