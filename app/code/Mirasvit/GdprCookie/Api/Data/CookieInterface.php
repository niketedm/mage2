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

interface CookieInterface
{
    const TABLE_NAME = 'mst_gdpr_cookie';

    const ID          = 'cookie_id';
    const NAME        = 'name';
    const CODE        = 'code';
    const IS_ACTIVE   = 'is_active';
    const DESCRIPTION = 'description';
    const LIFETIME    = 'lifetime';
    const GROUP_ID    = 'group_id';
    const STORE_IDS   = 'store_ids';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $value
     *
     * @return mixed
     */
    public function setName($value);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCode($value);

    /**
     * @return int
     */
    public function isActive();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setActive($value);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setDescription($value);

    /**
     * @return int
     */
    public function getLifetime();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setLifetime($value);

    /**
     * @return int
     */
    public function getGroupId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setGroupId($value);

    /**
     * @return string
     */
    public function getStoreIds();

    /**
     * @param array $value
     *
     * @return $this
     */
    public function setStoreIds($value);
}
