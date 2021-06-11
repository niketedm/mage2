<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


declare(strict_types = 1);

namespace Amasty\MegaMenu\Model\Menu;

use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenu\Model\OptionSource\SubcategoriesPosition;
use Amasty\MegaMenu\Model\OptionSource\SubmenuType;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Item extends AbstractModel implements ItemInterface, IdentityInterface
{
    const CACHE_TAG = 'amasty_mega_menu';

    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Amasty\MegaMenu\Model\ResourceModel\Menu\Item::class);
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG, self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->_getData(ItemInterface::ID);
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        $this->setData(ItemInterface::ID, $id);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getEntityId()
    {
        return $this->_getData(ItemInterface::ENTITY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setEntityId($entityId)
    {
        $this->setData(ItemInterface::ENTITY_ID, $entityId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return $this->_getData(ItemInterface::TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setType($type)
    {
        $this->setData(ItemInterface::TYPE, $type);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStoreId()
    {
        return $this->_getData(ItemInterface::STORE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setStoreId($storeId)
    {
        $this->setData(ItemInterface::STORE_ID, $storeId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->_getData(ItemInterface::NAME);
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->setData(ItemInterface::NAME, $name);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return $this->_getData(ItemInterface::LABEL);
    }

    /**
     * @inheritdoc
     */
    public function setLabel($label)
    {
        $this->setData(ItemInterface::LABEL, $label);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLabelTextColor()
    {
        return $this->_getData(ItemInterface::LABEL_TEXT_COLOR);
    }

    /**
     * @inheritdoc
     */
    public function setLabelTextColor($labelColor)
    {
        $this->setData(ItemInterface::LABEL_TEXT_COLOR, $labelColor);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLabelBackgroundColor()
    {
        return $this->_getData(ItemInterface::LABEL_BACKGROUND_COLOR);
    }

    /**
     * @inheritdoc
     */
    public function setLabelBackgroundColor($labelColor)
    {
        $this->setData(ItemInterface::LABEL_BACKGROUND_COLOR, $labelColor);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->_getData(ItemInterface::STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status)
    {
        $this->setData(ItemInterface::STATUS, $status);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getContent()
    {
        $content = $this->_getData(ItemInterface::CONTENT);
        if ($this->getType() === 'category' && $content === null) {
            $content = '{{child_categories_content}}';
        }
        return $content;
    }

    /**
     * @inheritdoc
     */
    public function setContent($content)
    {
        $this->setData(ItemInterface::CONTENT, $content);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getWidth()
    {
        return $this->_getData(ItemInterface::WIDTH);
    }

    /**
     * @inheritdoc
     */
    public function setWidth($width)
    {
        $this->setData(ItemInterface::WIDTH, $width);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getWidthValue()
    {
        return $this->_getData(ItemInterface::WIDTH_VALUE);
    }

    /**
     * @inheritdoc
     */
    public function setWidthValue($width)
    {
        $this->setData(ItemInterface::WIDTH_VALUE, $width);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getColumnCount()
    {
        return $this->_getData(ItemInterface::COLUMN_COUNT);
    }

    /**
     * @inheritdoc
     */
    public function setColumnCount($columnCount)
    {
        $this->setData(ItemInterface::COLUMN_COUNT, $columnCount);

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->_getData(ItemInterface::ICON);
    }

    public function setIcon($icon): ItemInterface
    {
        $this->setData(ItemInterface::ICON, $icon);

        return $this;
    }

    public function getSubmenuType(): ?int
    {
        return $this->getData(ItemInterface::SUBMENU_TYPE) !== null
            ? (int) $this->getData(ItemInterface::SUBMENU_TYPE)
            : null;
    }

    public function setSubmenuType($submenuType): void
    {
        $this->setData(ItemInterface::SUBMENU_TYPE, $submenuType);
    }

    public function getSubcategoriesPosition(): ?int
    {
        return $this->getData(ItemInterface::SUBCATEGORIES_POSITION) !== null
            ? (int) $this->getData(ItemInterface::SUBCATEGORIES_POSITION)
            : null;
    }

    public function setSubcategoriesPosition($subcategoriesPosition): ItemInterface
    {
        $this->setData(ItemInterface::SUBCATEGORIES_POSITION, $subcategoriesPosition);

        return $this;
    }
}
