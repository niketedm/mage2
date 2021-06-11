<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


declare(strict_types = 1);

namespace Amasty\MegaMenu\Setup\Operation;

use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Magento\Framework\DB\Ddl\Table;
use \Magento\Framework\Setup\SchemaSetupInterface;

class AddSubcategoriesContent
{
    public function execute(SchemaSetupInterface $setup): void
    {
        $tableName = $setup->getTable(ItemInterface::TABLE_NAME);

        $setup->getConnection()->addColumn(
            $tableName,
            ItemInterface::SUBMENU_TYPE,
            [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => true,
                'default' => null,
                'comment' => 'Submenu Type',
            ]
        );
        $setup->getConnection()->addColumn(
            $tableName,
            ItemInterface::SUBCATEGORIES_POSITION,
            [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => true,
                'default' => null,
                'comment' => 'Subcategories Position',
            ]
        );
    }
}
