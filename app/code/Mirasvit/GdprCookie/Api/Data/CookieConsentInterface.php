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



namespace Mirasvit\GdprCookie\Api\Data;

interface CookieConsentInterface
{
    const TABLE_NAME = 'mst_gdpr_cookie_consent';

    const ID          = 'consent_id';
    const CUSTOMER_ID = 'customer_id';
    const STORE_ID    = 'store_id';
    const REMOTE_ADDR = 'remote_addr';
    const GROUP_IDS   = 'group_ids';
    const CREATED_AT  = 'created_at';

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
     * @return int
     */
    public function getStoreId();

    /**
     * @return string
     */
    public function getGroupsIds();

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

}
