<?php

namespace Mancini\ShippingZone\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $contextVersion = $context->getVersion();
        if ($contextVersion) {
            if (version_compare($context->getVersion(), '1.0.2') < 0) {
                $this->addApplyFreeStandardColumn($setup);
            }
            if (version_compare($context->getVersion(), '1.0.3') < 0) {
                $setup->getConnection()->dropColumn($setup->getTable('shipping_zone'), 'days');
                $setup->getConnection()->dropColumn($setup->getTable('shipping_zone'), 'trucks');
                $setup->getConnection()->dropColumn($setup->getTable('shipping_zone'), 'same_day');
                $setup->getConnection()->dropColumn($setup->getTable('shipping_zone'), 'premium_days');
                $setup->getConnection()->dropColumn($setup->getTable('shipping_zone'), 'premium_trucks');
            }
        }

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    protected function addApplyFreeStandardColumn(SchemaSetupInterface $setup)
    {
        //shipping_zone tables
        $setup->getConnection()
            ->addColumn(
                $setup->getTable('shipping_zone'),
                'apply_free_standard',
                [
                    'type' => Table::TYPE_SMALLINT,
                    'length'=>'1',
                    'default' => 0,
                    'nullable' => false,
                    'comment' =>'Apply Free Standard'
                ]
            );
    }
}
