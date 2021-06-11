<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Api\Data\Menu;

interface LinkInterface
{
    const TABLE_NAME = 'amasty_menu_link';

    const PERSIST_NAME = 'amasty_megamenu_link';

    /**#@+
     * Constants defined for keys of data array
     */
    const ENTITY_ID = 'entity_id';
    const LINK = 'link';
    const PAGE_ID = 'page_id';
    const TYPE = 'link_type';
    /**#@-*/

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @param int $entityId
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\LinkInterface
     */
    public function setEntityId($entityId);

    /**
     * @return string
     */
    public function getLink();

    /**
     * @param string $link
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\LinkInterface
     */
    public function setLink($link);

    /**
     * @return mixed
     */
    public function getPageId();

    /**
     * @param int $pageId
     * @return void
     */
    public function setPageId(int $pageId);

    /**
     * @return mixed
     */
    public function getLinkType();

    /**
     * @param int $linkType
     * @return void
     */
    public function setLinkType(int $linkType);
}
