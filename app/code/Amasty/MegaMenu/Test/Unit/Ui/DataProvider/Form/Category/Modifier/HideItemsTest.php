<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


// @codingStandardsIgnoreFile

declare(strict_types=1);

namespace Amasty\MegaMenu\Test\Unit\Ui\DataProvider\Form\Category\Modifier;

use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenu\Model\Menu\Subcategory;
use Amasty\MegaMenu\Model\Provider\FieldsToHideProvider;
use Amasty\MegaMenu\Test\Unit\Traits;
use Amasty\MegaMenu\Ui\DataProvider\Form\Category\Modifier\HideItems;
use Magento\Catalog\Model\Category;

/**
 * Class HideItemsTest
 * test HideItems DataProvider
 *
 * @see HideItems
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class HideItemsTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers HideItems::modifyMeta
     *
     * @dataProvider modifyMetaDataProvider
     *
     * @throws \ReflectionException
     */
    public function testModifyMeta(
        array $meta,
        int $parentId,
        int $level,
        bool $hasChildren,
        bool $isObjectNew,
        bool $isShowSubcategories,
        array $expectedResult
    ): void {
        $entity = $this->createPartialMock(Category::class, ['getLevel', 'hasChildren', 'isObjectNew', 'getParentCategory']);
        $entity->expects($this->any())->method('getLevel')->willReturn($level);
        $entity->expects($this->any())->method('hasChildren')->willReturn($hasChildren);
        $entity->expects($this->any())->method('isObjectNew')->willReturn($isObjectNew);
        $entity->expects($this->any())->method('getParentCategory')->willReturn($this->createMock(Category::class));

        $subcategory = $this->createPartialMock(Subcategory::class, ['isShowSubcategories']);
        $subcategory->expects($this->any())->method('isShowSubcategories')->willReturn($isShowSubcategories);

        $fieldsToHideProvider = $this->createPartialMock(FieldsToHideProvider::class, []);
        $this->setProperty($fieldsToHideProvider, 'subcategory', $subcategory, FieldsToHideProvider::class);

        $hideItems = $this->createPartialMock(HideItems::class, []);
        $this->setProperty($hideItems, 'fieldsToHideProvider', $fieldsToHideProvider, HideItems::class);
        $this->setProperty($hideItems, 'entity', $entity, HideItems::class);
        $this->setProperty($hideItems, 'parentId', $parentId, HideItems::class);

        $actualResult = $hideItems->modifyMeta($meta);

        $this->assertEquals($expectedResult, array_keys($actualResult['am_mega_menu_fieldset']['children']));
    }

    /**
     * Data provider for modifyMeta test
     * @return array
     */
    public function modifyMetaDataProvider(): array
    {
        return [
            [
                [],
                0,
                1,
                true,
                true,
                true,
                [ItemInterface::WIDTH, ItemInterface::WIDTH_VALUE, ItemInterface::COLUMN_COUNT, ItemInterface::LABEL, ItemInterface::LABEL_GROUP, ItemInterface::CONTENT, ItemInterface::SUBMENU_TYPE, ItemInterface::SUBCATEGORIES_POSITION, ItemInterface::ICON]
            ],
            [
                [],
                0,
                1,
                true,
                false,
                false,
                [ItemInterface::WIDTH, ItemInterface::WIDTH_VALUE, ItemInterface::COLUMN_COUNT, ItemInterface::LABEL, ItemInterface::LABEL_GROUP, ItemInterface::CONTENT, ItemInterface::SUBMENU_TYPE, ItemInterface::SUBCATEGORIES_POSITION, ItemInterface::ICON]
            ],
            [
                [],
                0,
                1,
                false,
                true,
                true,
                [ItemInterface::WIDTH, ItemInterface::WIDTH_VALUE, ItemInterface::COLUMN_COUNT, ItemInterface::LABEL, ItemInterface::LABEL_GROUP, ItemInterface::CONTENT, ItemInterface::SUBMENU_TYPE, ItemInterface::SUBCATEGORIES_POSITION, ItemInterface::ICON]
            ],
            [
                [],
                0,
                1,
                false,
                false,
                false,
                [ItemInterface::WIDTH, ItemInterface::WIDTH_VALUE, ItemInterface::COLUMN_COUNT, ItemInterface::LABEL, ItemInterface::LABEL_GROUP, ItemInterface::CONTENT, ItemInterface::SUBMENU_TYPE, ItemInterface::SUBCATEGORIES_POSITION, ItemInterface::ICON]
            ],
            [
                [],
                0,
                2,
                true,
                true,
                true,
                [FieldsToHideProvider::CATEGORY_LEVEL_ERROR, ItemInterface::SUBMENU_TYPE]
            ],
            [
                [],
                0,
                2,
                true,
                false,
                false,
                [FieldsToHideProvider::CATEGORY_LEVEL_ERROR]
            ],
            [
                [],
                0,
                2,
                false,
                true,
                true,
                [FieldsToHideProvider::CATEGORY_LEVEL_ERROR, ItemInterface::SUBCATEGORIES_POSITION, ItemInterface::SUBMENU_TYPE]
            ],
            [
                [],
                0,
                2,
                false,
                false,
                false,
                [FieldsToHideProvider::CATEGORY_LEVEL_ERROR, ItemInterface::SUBCATEGORIES_POSITION, ItemInterface::SUBMENU_TYPE]
            ],
            [
                [],
                0,
                3,
                true,
                true,
                true,
                [ItemInterface::WIDTH, ItemInterface::WIDTH_VALUE, ItemInterface::COLUMN_COUNT, ItemInterface::SUBMENU_TYPE, FieldsToHideProvider::CATEGORY_LEVEL_ERROR]
            ],
            [
                [],
                0,
                3,
                true,
                true,
                false,
                [ItemInterface::WIDTH, ItemInterface::WIDTH_VALUE, ItemInterface::COLUMN_COUNT, ItemInterface::LABEL, ItemInterface::LABEL_GROUP, ItemInterface::CONTENT, ItemInterface::SUBMENU_TYPE, ItemInterface::SUBCATEGORIES_POSITION]
            ],
            [
                [],
                0,
                3,
                true,
                false,
                true,
                [ItemInterface::WIDTH, ItemInterface::WIDTH_VALUE, ItemInterface::COLUMN_COUNT, ItemInterface::SUBMENU_TYPE, FieldsToHideProvider::CATEGORY_LEVEL_ERROR]
            ],
            [
                [],
                0,
                3,
                true,
                false,
                false,
                [ItemInterface::WIDTH, ItemInterface::WIDTH_VALUE, ItemInterface::COLUMN_COUNT, ItemInterface::LABEL, ItemInterface::LABEL_GROUP, ItemInterface::CONTENT, ItemInterface::SUBMENU_TYPE, ItemInterface::SUBCATEGORIES_POSITION]
            ],
            [
                [],
                0,
                3,
                false,
                true,
                true,
                [ItemInterface::WIDTH, ItemInterface::WIDTH_VALUE, ItemInterface::COLUMN_COUNT, ItemInterface::SUBMENU_TYPE, FieldsToHideProvider::CATEGORY_LEVEL_ERROR, ItemInterface::SUBCATEGORIES_POSITION]
            ],
            [
                [],
                0,
                3,
                false,
                true,
                false,
                [ItemInterface::WIDTH, ItemInterface::WIDTH_VALUE, ItemInterface::COLUMN_COUNT, ItemInterface::LABEL, ItemInterface::LABEL_GROUP, ItemInterface::CONTENT, ItemInterface::SUBMENU_TYPE, ItemInterface::SUBCATEGORIES_POSITION]
            ]
            ,
            [
                [],
                0,
                3,
                false,
                false,
                true,
                [ItemInterface::WIDTH, ItemInterface::WIDTH_VALUE, ItemInterface::COLUMN_COUNT, ItemInterface::SUBMENU_TYPE, FieldsToHideProvider::CATEGORY_LEVEL_ERROR, ItemInterface::SUBCATEGORIES_POSITION]
            ]
        ];
    }
}
