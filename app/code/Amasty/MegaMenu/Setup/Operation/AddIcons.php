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

class AddIcons
{
    public function execute(SchemaSetupInterface $setup): void
    {
        $tableName = $setup->getTable(ItemInterface::TABLE_NAME);

        $setup->getConnection()->addColumn(
            $tableName,
            ItemInterface::ICON,
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'default' => null,
                'comment' => 'Icon',
            ]
        );
    }
}
