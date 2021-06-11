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

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
    	$setup->startSetup();
    	
    	$sql = [];
    	
    	$sql[] = "SET foreign_key_checks = 0";
    	
    	if ($setup->getConnection()->tableColumnExists($setup->getTable('quote'), 'fee') === false)
    	{
    		$sql[] = "ALTER TABLE {$setup->getTable('quote')} ADD fee DECIMAL(10,2) NOT NULL DEFAULT '0'";
    	}
    	
    	if ($setup->getConnection()->tableColumnExists($setup->getTable('quote_address'), 'fee') === false)
    	{
    		$sql[] = "ALTER TABLE {$setup->getTable('quote_address')} ADD fee DECIMAL(10,2) NOT NULL DEFAULT '0'";
    	}
    	
    	if ($setup->getConnection()->tableColumnExists($setup->getTable('sales_order'), 'fee') === false)
    	{
    		$sql[] = "ALTER TABLE {$setup->getTable('sales_order')} ADD fee DECIMAL(10,2) NOT NULL DEFAULT '0'";
    	}

    	$sql[] = "SET foreign_key_checks = 1";
    	
    	foreach ($sql as $query)
    	{
    		$setup->run($query);
    	}

    	$setup->endSetup();
    }
}


