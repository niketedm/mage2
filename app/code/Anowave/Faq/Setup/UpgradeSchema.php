<?php 
/**
 * Anowave Magento 2 FAQ
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
 * @package 	Anowave_Faq
 * @copyright 	Copyright (c) 2018 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */
 
namespace Anowave\Faq\Setup;

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
         * Upgrade 
         * 
         * @version 2.0.1
         */
        if (version_compare($context->getVersion(), '2.0.1') < 0) 
        {
        	$sql = array();
        	
        	$sql[] = "SET foreign_key_checks = 0";
        	
        	$sql[] = "ALTER TABLE {$setup->getTable('ae_faq')} DROP FOREIGN KEY faq";

        	$sql[] = "SET foreign_key_checks = 1";
        	
        	foreach ($sql as $query)
        	{
        		$setup->run($query);
        	}
        }
        
        /**
         * Upgrade 
         * 
         * @version 2.0.2
         */
        if (version_compare($context->getVersion(), '2.0.2') < 0)
        {
        	$sql = array();
        	
        	$sql[] = "SET foreign_key_checks = 0";
        	
        	$sql[] = "ALTER TABLE {$setup->getTable('ae_faq')} ADD faq_position INT(6) NOT NULL DEFAULT '0' AFTER faq_content";
        	
        	$sql[] = "SET foreign_key_checks = 1";
        	
        	foreach ($sql as $query)
        	{
        		$setup->run($query);
        	}
        }

        /**
         * Upgrade 
         * 
         * @version 2.0.3
         */
        if (version_compare($context->getVersion(), '2.0.3') < 0)
        {
        	$sql = array();
        	
        	$sql[] = "SET foreign_key_checks = 0";
        	
        	$sql[] = "ALTER TABLE {$setup->getTable('ae_faq')} ADD faq_enable BOOLEAN NOT NULL DEFAULT TRUE AFTER faq_position";
        	
        	$sql[] = "SET foreign_key_checks = 1";
        	
        	foreach ($sql as $query)
        	{
        		$setup->run($query);
        	}
        }
        
        $setup->endSetup();
    }
}


