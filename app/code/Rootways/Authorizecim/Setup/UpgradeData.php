<?php
namespace Rootways\Authorizecim\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Customer\Model\Customer;

class UpgradeData implements UpgradeDataInterface
{
    public function __construct(
        EavSetupFactory $eavSetupFactory
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }
    
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.1')) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->addAttribute(
                Customer::ENTITY,
                'rw_authorizecim_customer_profile_id',
                [
                    'type' => 'varchar',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Authorize.Net Customer Profile Id (by Rootways)',
                    'input' => 'text',
                    'class' => '',
                    'source' => '',
                    'visible' => false,
                    'required' => false,
                    'user_defined' => true,
                    'default' => '',
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'unique' => false,
                ]
            );
        }
    }
}
