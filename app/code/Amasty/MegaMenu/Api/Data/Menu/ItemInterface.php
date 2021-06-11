<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


declare(strict_types = 1);

namespace Amasty\MegaMenu\Api\Data\Menu;

interface ItemInterface
{
    const TABLE_NAME = 'amasty_menu_item_content';

    /**#@+
     * Constants defined for keys of data array
     */
    const ID = 'id';

    const ENTITY_ID = 'entity_id';

    const TYPE = 'type';

    const STORE_ID = 'store_id';

    const NAME = 'name';

    const LABEL = 'label';

    const LABEL_GROUP = 'label_group';

    const LABEL_TEXT_COLOR = 'label_text_color';

    const LABEL_BACKGROUND_COLOR = 'label_background_color';

    const STATUS = 'status';

    const CONTENT = 'content';

    const WIDTH = 'width';

    const WIDTH_VALUE = 'width_value';

    const COLUMN_COUNT = 'column_count';

    const ICON = 'icon';

    const SUBCATEGORIES_POSITION = 'subcategories_position';

    const SUBMENU_TYPE = 'submenu_type';

    const SORT_ORDER = 'sort_order';

    const CATEGORY_TYPE = 'category';
    const CUSTOM_TYPE = 'custom';

    /**#@-*/

    const FIELDS_BY_STORE_CUSTOM = [
        'general'               => [
            self::NAME,
            self::WIDTH,
            self::WIDTH_VALUE,
            self::STATUS,
            self::LABEL,
            self::LABEL_TEXT_COLOR,
            self::LABEL_BACKGROUND_COLOR
        ],
        'am_mega_menu_fieldset' => [
            self::CONTENT
        ]
    ];

    const FIELDS_BY_STORE_CATEGORY = [
        'am_mega_menu_fieldset' => [
            self::WIDTH,
            self::WIDTH_VALUE,
            self::COLUMN_COUNT,
            self::CONTENT,
            self::LABEL,
            self::LABEL_TEXT_COLOR,
            self::LABEL_BACKGROUND_COLOR,
            self::ICON,
            self::SUBMENU_TYPE,
            self::SUBCATEGORIES_POSITION
        ]
    ];

    const DEFAULT_VALUES = [
        self::WIDTH => 0,
        self::WIDTH_VALUE => 0,
        self::COLUMN_COUNT => 4,
        self::CONTENT => '{{child_categories_content}}',
        self::LABEL => '',
        self::LABEL_TEXT_COLOR => '',
        self::LABEL_BACKGROUND_COLOR => ''
    ];

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\ItemInterface
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @param int $entityId
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\ItemInterface
     */
    public function setEntityId($entityId);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\ItemInterface
     */
    public function setType($type);

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $storeId
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\ItemInterface
     */
    public function setStoreId($storeId);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\ItemInterface
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param string $label
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\ItemInterface
     */
    public function setLabel($label);

    /**
     * @return string
     */
    public function getLabelTextColor();

    /**
     * @param string $labelColor
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\ItemInterface
     */
    public function setLabelTextColor($labelColor);

    /**
     * @return string
     */
    public function getLabelBackgroundColor();

    /**
     * @param string $labelColor
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\ItemInterface
     */
    public function setLabelBackgroundColor($labelColor);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $status
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\ItemInterface
     */
    public function setStatus($status);

    /**
     * @return string|null
     */
    public function getContent();

    /**
     * @return int|null
     */
    public function getWidth();

    /**
     * @param int|null $width
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\ItemInterface
     */
    public function setWidth($width);

    /**
     * @return int|null
     */
    public function getWidthValue();

    /**
     * @param int|null $width
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\ItemInterface
     */
    public function setWidthValue($width);

    /**
     * @return int|null
     */
    public function getColumnCount();

    /**
     * @param int|null $columnCount
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\ItemInterface
     */
    public function setColumnCount($columnCount);

    /**
     * @return string|null
     */
    public function getIcon(): ?string;

    /**
     * @param string|null $icon
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\ItemInterface
     */
    public function setIcon($icon): ItemInterface;

    /**
     * @return int|null
     */
    public function getSubmenuType(): ?int;

    /**
     * @param int|null $submenuType
     *
     * @return void
     */
    public function setSubmenuType($submenuType): void;

    /**
     * @return int|null
     */
    public function getSubcategoriesPosition(): ?int;

    /**
     * @param int|null $subcategoriesPosition
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\ItemInterface
     */
    public function setSubcategoriesPosition($subcategoriesPosition): ItemInterface;
}
