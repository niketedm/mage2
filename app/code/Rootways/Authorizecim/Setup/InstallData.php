<?php
namespace Rootways\Authorizecim\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Model\Config;
use Magento\Customer\Model\Customer;


class InstallData implements InstallDataInterface
{
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        Config $eavConfig
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
    }
    
    /**
     * @inheritdoc
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
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