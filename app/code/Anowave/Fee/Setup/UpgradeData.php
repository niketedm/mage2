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

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Catalog\Model\Category;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
 
class UpgradeData implements UpgradeDataInterface
{
	/**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

   	/**
   	 * Constructor 
   	 * 
   	 * @param EavSetupFactory $eavSetupFactory
   	 */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }
	
 
    /**
     * Upgrade
     * 
     * @see \Magento\Framework\Setup\UpgradeDataInterface::upgrade()
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if ($context->getVersion() && version_compare($context->getVersion(), '2.0.8') < 0) 
        {
        	try 
        	{
	        	/** @var EavSetup $eavSetup */
	        	$eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
	        	
	        	$eavSetup->addAttribute
	        	(
	        		Category::ENTITY, \Anowave\Fee\Helper\Data::FEE_ATTRIBUTE,
	        		[
	        			'type' 			=> 'varchar',
	        			'label' 		=> 'Extra fee',
	        			'input' 		=> 'text',
	        			'required' 		=> false,
	        			'sort_order' 	=> 100,
	        			'global' 		=> ScopedAttributeInterface::SCOPE_STORE,
	        			'group' 		=> 'General Information'
	        		]
	        	);
        	}
        	catch (\Exception $e){}
        }
 
        $setup->endSetup();
    }
}