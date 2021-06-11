<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


declare(strict_types=1);

namespace Amasty\MegaMenu\Test\Unit\Ui\DataProvider\Form\Category\Modifier;

use Amasty\MegaMenu\Model\Menu\Subcategory;
use Amasty\MegaMenu\Model\OptionSource\SubcategoriesPosition;
use Amasty\MegaMenu\Test\Unit\Traits;
use Amasty\MegaMenu\Ui\DataProvider\Form\Category\Modifier\Level;
use Magento\Catalog\Model\Category;

/**
 * Class LevelTest
 * test Level DataProvider
 *
 * @see Level
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class LevelTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers Level::modifyMeta
     *
     * @dataProvider modifyMetaDataProvider
     *
     * @throws \ReflectionException
     */
    public function testModifyMeta(
        array $meta,
        int $parentId,
        int $categoryLevel,
        int $parentCategoryLevel,
        bool $isObjectNew,
        bool $isShowSubcategories,
        array $expectedResult
    ): void {
        $subcategoriesPosition = $this->createPartialMock(SubcategoriesPosition::class, []);

        $parentCategory = $this->createPartialMock(Category::class, ['getLevel']);
        $parentCategory->expects($this->any())->method('getLevel')->willReturn($parentCategoryLevel);

        $entity = $this->createPartialMock(Category::class, ['getLevel', 'isObjectNew', 'getParentCategory']);
        $entity->expects($this->any())->method('isObjectNew')->willReturn($isObjectNew);
        $entity->expects($this->any())->method('getLevel')->willReturn($categoryLevel);
        $entity->expects($this->any())->method('getParentCategory')->willReturn($parentCategory);

        $subcategory = $this->createPartialMock(Subcategory::class, ['isShowSubcategories']);
        $subcategory->expects($this->any())->method('isShowSubcategories')->willReturn($isShowSubcategories);

        $level = $this->createPartialMock(Level::class, []);
        $this->setProperty($level, 'entity', $entity, Level::class);
        $this->setProperty($level, 'subcategoriesPosition', $subcategoriesPosition, Level::class);
        $this->setProperty($level, 'subcategory', $subcategory, Level::class);
        $this->setProperty($level, 'parentId', $parentId, Level::class);

        $actualResult = $level->modifyMeta($meta);

        $this->assertEquals($expectedResult, $this->prepareData($actualResult));
    }

    private function prepareData(array $actualResult): array
    {
        $fields = $actualResult['am_mega_menu_fieldset']['children'];
        $subcategories_position = array_column(
            $fields['subcategories_position']['arguments']['data']['options'],
            'value'
        );

        $data['submenu_type'] = $fields['submenu_type']['arguments']['data']['config']['switcherConfig']['enabled'];
        $data['subcategories_position'] = $subcategories_position;

        $config = $fields['content']['arguments']['data']['config'] ?? null;
        if (isset($config['notice']) && isset($config['default'])) {
            $data['notice'] = $config['notice'];
            $data['default'] = $config['default'];
        }

        return $data;
    }

    /**
     * Data provider for modifyMeta test
     * @return array
     */
    public function modifyMetaDataProvider(): array
    {
        $config['am_mega_menu_fieldset']['children']['content']['arguments']['data']['config']['notice'] = 1;
        $config['am_mega_menu_fieldset']['children']['content']['arguments']['data']['config']['default'] = 1;

        return [
            [
                $config,
                2,
                1,
                0,
                true,
                true,
                [
                    'submenu_type' => false,
                    'subcategories_position' => [1, 2],
                    'notice' => 1,
                    'default' => 1
                ]
            ],
            [
                $config,
                0,
                1,
                0,
                false,
                true,
                [
                    'submenu_type' => false,
                    'subcategories_position' => [1, 2],
                    'notice' => 1,
                    'default' => 1
                ]
            ],
            [
                $config,
                2,
                2,
                1,
                true,
                true,
                [
                    'submenu_type' => true,
                    'subcategories_position' => [1, 2]
                ]
            ],
            [
                $config,
                2,
                2,
                1,
                false,
                false,
                [
                    'submenu_type' => true,
                    'subcategories_position' => [1, 2],
                    'notice' => 1,
                    'default' => 1
                ]
            ],
            [
                $config,
                2,
                2,
                2,
                true,
                true,
                [
                    'submenu_type' => false,
                    'subcategories_position' => [1, 2, 0]
                ]
            ],
            [
                $config,
                2,
                3,
                1,
                false,
                false,
                [
                    'submenu_type' => false,
                    'subcategories_position' => [1, 2, 0],
                    'notice' => 1,
                    'default' => 1
                ]
            ]
        ];
    }
}
