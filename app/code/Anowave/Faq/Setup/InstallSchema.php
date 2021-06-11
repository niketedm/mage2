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

    	$sql = array();

		$sql[] = "SET foreign_key_checks = 0";
		
		$sql[] = "CREATE TABLE IF NOT EXISTS " . $setup->getTable('ae_faq') . " (faq_id int(6) NOT NULL AUTO_INCREMENT,faq_store_id smallint(5) unsigned NOT NULL DEFAULT '0',faq_product_id int(10) unsigned DEFAULT NULL,faq text,faq_content text,PRIMARY KEY (faq_id),KEY faq_product_id (faq_product_id),KEY faq_store_id (faq_store_id),CONSTRAINT faq FOREIGN KEY (faq_product_id) REFERENCES " . $setup->getTable('catalog_product_entity') . " (entity_id) ON DELETE CASCADE ON UPDATE CASCADE,CONSTRAINT faq_store FOREIGN KEY (faq_store_id) REFERENCES " . $setup->getTable('store') . " (store_id) ON DELETE CASCADE ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8";
		
		$sql[] = "SET foreign_key_checks = 1";
		
		foreach ($sql as $query)
		{
			$setup->run($query);
		}
		        

        $setup->endSetup();
    }
}




