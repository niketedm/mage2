<?php 
/**
 * Anowave Magento 2 Extra Fee
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * http://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Anowave
 * @package 	Anowave_Fee
 * @copyright 	Copyright (c) 2020 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */
 
namespace Anowave\Fee\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Updates DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        
        /**
         * @version 2.0.1
         */
        if (version_compare($context->getVersion(), '2.0.1') < 0)
        {
        	$sql = [];
        	
        	$sql[] = "SET foreign_key_checks = 0";
        	
        	if ($setup->getConnection()->tableColumnExists($setup->getTable('sales_invoice'), 'fee') === false)
        	{
        		$sql[] = "ALTER TABLE {$setup->getTable('sales_invoice')} ADD fee DECIMAL(10,2) NOT NULL DEFAULT '0'";
        	}
        	
        	if ($setup->getConnection()->tableColumnExists($setup->getTable('sales_creditmemo'), 'fee') === false)
        	{
        		$sql[] = "ALTER TABLE {$setup->getTable('sales_creditmemo')} ADD fee DECIMAL(10,2) NOT NULL DEFAULT '0'";
        	}

        	$sql[] = "SET foreign_key_checks = 1";
        	
        	foreach ($sql as $query)
        	{
        		$setup->run($query);
        	}
        }
        
        /**
         * @version 2.0.3
         */
        if (version_compare($context->getVersion(), '2.0.3') < 0)
        {
        	$sql = [];

        	$sql[] = "CREATE TABLE IF NOT EXISTS {$setup->getTable('ae_fee_rules')} (rule_id int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Rule Id',name varchar(255) DEFAULT NULL COMMENT 'Name',description text COMMENT 'Description',from_date date DEFAULT NULL COMMENT 'From',to_date date DEFAULT NULL COMMENT 'To',is_active smallint(6) NOT NULL DEFAULT '0' COMMENT 'Is Active',conditions_serialized mediumtext COMMENT 'Conditions Serialized',sort_order int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Sort Order (Priority)',PRIMARY KEY (rule_id),KEY AE_FEE_RULES_SORT_ORDER_IS_ACTIVE_TO_DATE_FROM_DATE (sort_order,is_active,to_date,from_date)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Extra Fee Rules'";
        	
        	foreach ($sql as $query)
        	{
        		$setup->run($query);
        	}
        }
        
        /**
         * @version 2.0.9
         */
        if (version_compare($context->getVersion(), '2.0.9') < 0)
        {
        	$sql = [];
        	
        	if ($setup->getConnection()->tableColumnExists($setup->getTable('ae_fee_rules'), 'rule_default') === false)
        	{
        		$sql[] = "ALTER TABLE {$setup->getTable('ae_fee_rules')} ADD rule_default BOOLEAN NOT NULL DEFAULT FALSE AFTER rule_id";
        	}
        	
        	if ($setup->getConnection()->tableColumnExists($setup->getTable('ae_fee_rules'), 'rule_fee_id') === false)
        	{
        		$sql[] = "ALTER TABLE {$setup->getTable('ae_fee_rules')} ADD rule_fee_id INT(6) NULL DEFAULT NULL AFTER rule_default";
        	}

        	foreach ($sql as $query)
        	{
        		$setup->run($query);
        	}
        }
        
        /**
         * @version 3.0.4
         */
        if (version_compare($context->getVersion(), '3.0.4') < 0)
        {
        	$sql = [];
        	
        	$sql[] = "SET foreign_key_checks = 0";
        	
        	if ($setup->getConnection()->tableColumnExists($setup->getTable('sales_order'), 'fee_tax') === false)
        	{
        		$sql[] = "ALTER TABLE {$setup->getTable('sales_order')} ADD fee_tax DECIMAL(10,2) NOT NULL DEFAULT '0'";
        	}
        	
        	if ($setup->getConnection()->tableColumnExists($setup->getTable('sales_creditmemo'), 'fee_tax') === false)
        	{
        		$sql[] = "ALTER TABLE {$setup->getTable('sales_creditmemo')} ADD fee_tax DECIMAL(10,2) NOT NULL DEFAULT '0'";
        	}
        	
        	if ($setup->getConnection()->tableColumnExists($setup->getTable('sales_invoice'), 'fee_tax') === false)
        	{
        		$sql[] = "ALTER TABLE {$setup->getTable('sales_invoice')} ADD fee_tax DECIMAL(10,2) NOT NULL DEFAULT '0'";
        	}
        	
        	$sql[] = "SET foreign_key_checks = 1";
        	
        	foreach ($sql as $query)
        	{
        		$setup->run($query);
        	}
        }
        
        if (version_compare($context->getVersion(), '3.0.5') < 0)
        {
        	$sql = [];
        	
        	$sql[] = "SET foreign_key_checks = 0";
        	
        	$sql[] = "CREATE TABLE IF NOT EXISTS {$setup->getTable('ae_fee')} (fee_id int(6) NOT NULL AUTO_INCREMENT,fee_name varchar(255) DEFAULT NULL,fee decimal(10,2) DEFAULT NULL,fee_type enum('F','P','PP','PF','FC','PC','OC','OP','OQ') DEFAULT 'P',fee_multiply_quantity tinyint(1) NOT NULL DEFAULT '0',fee_status tinyint(1) NOT NULL DEFAULT '0',fee_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (fee_id)) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8";

        	$sql[] = "SET foreign_key_checks = 1";
        	
        	foreach ($sql as $query)
        	{
        		$setup->run($query);
        	}
        }
        
        /**
         * Upgrade
         * 
         * @version 3.0.5
         */
        if (version_compare($context->getVersion(), '3.0.6') < 0)
        {
        	$sql = [];
        	
        	$sql[] = "SET foreign_key_checks = 0";
        	
        	if ($setup->getConnection()->tableColumnExists($setup->getTable('ae_fee_rules'), 'rule_fee_website_id') === false)
        	{
        		$sql[] = "ALTER TABLE {$setup->getTable('ae_fee_rules')} ADD rule_fee_website_id INT(6) NULL AFTER rule_fee_id";
        	}
        	
        	if ($setup->getConnection()->tableColumnExists($setup->getTable('ae_fee_rules'), 'rule_fee_store_id') === false)
        	{
        		$sql[] = "ALTER TABLE {$setup->getTable('ae_fee_rules')} ADD rule_fee_store_id INT(6) NULL DEFAULT NULL AFTER rule_fee_website_id";
        	}
        	
        	$sql[] = "SET foreign_key_checks = 1";
        	
        	foreach ($sql as $query)
        	{
        		$setup->run($query);
        	}
        }
        
        if (version_compare($context->getVersion(), '3.0.7') < 0)
        {
        	$sql = [];
        	
        	$sql[] = "SET foreign_key_checks = 0";
        	
        	$sql[] = "ALTER TABLE {$setup->getTable('ae_fee')} CHANGE fee_type fee_type VARCHAR(4) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'P'";
        	
        	$sql[] = "SET foreign_key_checks = 1";
        	
        	foreach ($sql as $query)
        	{
        		$setup->run($query);
        	}
        }
        
        if (version_compare($context->getVersion(), '100.0.2') < 0)
        {
            $setup->getConnection()->addColumn(...[$setup->getTable('ae_fee'), 'fee_apply_logged_only',
            [
                'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                'length'    => 1,
                'nullable'  => true,
                'after'     => 'fee_multiply_quantity',
                'comment'   => 'Apply on logged only'
            ]]);
        }
        
        if (version_compare($context->getVersion(), '100.0.3') < 0)
        {
            $setup->getConnection()->addColumn(...[$setup->getTable('ae_fee'), 'fee_apply_group_only',
            [
                'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'length'    => 10,
                'nullable'  => true,
                'after'     => 'fee_apply_logged_only',
                'comment'   => 'Apply on group only'
            ]]);
        }
        
        if (version_compare($context->getVersion(), '100.1.1') < 0)
        {
            $sql = [];
            
            $sql[] = "SET foreign_key_checks = 0";
            
            $sql[] = "ALTER TABLE {$setup->getTable('ae_fee')} CHANGE fee fee DECIMAL(10,4) NULL DEFAULT NULL";
            
            $sql[] = "SET foreign_key_checks = 1";
            
            foreach ($sql as $query)
            {
                $setup->run($query);
            }
        }
        
        if (version_compare($context->getVersion(), '100.1.2') < 0)
        {
            $setup->getConnection()->addColumn(...[$setup->getTable('ae_fee'), 'fee_calculate_per_product',
            [
                'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                'length'    => 1,
                'nullable'  => true,
                'default'   => 0,
                'after'     => 'fee_apply_group_only',
                'comment'   => 'Calculate per product'
            ]]);
        }

        $setup->endSetup();
    }
}


