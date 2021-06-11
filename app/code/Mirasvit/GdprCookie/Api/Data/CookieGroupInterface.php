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

interface CookieGroupInterface
{
    const TABLE_NAME = 'mst_gdpr_cookie_group';

    const ID          = 'group_id';
    const NAME        = 'name';
    const PRIORITY    = 'priority';
    const IS_ACTIVE   = 'is_active';
    const IS_REQUIRED = 'is_required';
    const DESCRIPTION = 'description';
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
     * @return $this
     */
    public function setName($value);

    /**
     * @return int
     */
    public function getPriority();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setPriority($value);

    /**
     * @return int
     */
    public function isActive();

    /**
     * @param int $active
     *
     * @return $this
     */
    public function setIsActive($active);

    /**
     * @return int
     */
    public function isRequired();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setIsRequired($value);

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
