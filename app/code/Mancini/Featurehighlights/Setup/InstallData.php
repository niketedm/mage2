<?php
namespace Mancini\Featurehighlights\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
* @codeCoverageIgnore
*/
class InstallData implements InstallDataInterface
{
    /**
     * Eav setup factory
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Init
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(\Magento\Eav\Setup\EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create();

        /** attribute -Suitable For Adjustable Base*/
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'suitable_for_adjustable_base',
            [
                'group' => 'Mattress attribute set',
                'type' => 'varchar',
                'label' => 'Suitable For Adjustable Base',
                'input' => 'boolean',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'frontend' => '',
                'backend' => '',
                'required' => false,
                'sort_order' => 100,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'visible' => true,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => true
            ]
        );

        /** attribute -Motion Seperation*/
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'motion_seperation',
            [
                'group' => 'Mattress attribute set',
                'type' => 'varchar',
                'label' => 'Motion Seperation',
                'input' => 'boolean',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'frontend' => '',
                'backend' => '',
                'required' => false,
                'sort_order' => 101,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'visible' => true,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => true
            ]
        );

        /** attribute -Cooling Technology*/
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'cooling_technology',
            [
                'group' => 'Mattress attribute set',
                'type' => 'varchar',
                'label' => 'Cooling Technology',
                'input' => 'boolean',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'frontend' => '',
                'backend' => '',
                'required' => false,
                'sort_order' => 102,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'visible' => true,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => true
            ]
        );

        /** attribute -Breathable*/
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'breathable',
            [
                'group' => 'Mattress attribute set',
                'type' => 'varchar',
                'label' => 'Breathable',
                'input' => 'boolean',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'frontend' => '',
                'backend' => '',
                'required' => false,
                'sort_order' => 103,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'visible' => true,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => true
            ]
        );

        /** attribute -Ecofriendly */
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'ecofrindly',
            [
                'group' => 'Mattress attribute set',
                'type' => 'varchar',
                'label' => 'Ecofriendly',
                'input' => 'boolean',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'frontend' => '',
                'backend' => '',
                'required' => false,
                'sort_order' => 104,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'visible' => true,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => true
            ]
        );

        /** attribute -Hypoallergenic */
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'hypoallergenic',
            [
                'group' => 'Mattress attribute set',
                'type' => 'varchar',
                'label' => 'Hypoallergenic',
                'input' => 'boolean',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'frontend' => '',
                'backend' => '',
                'required' => false,
                'sort_order' => 105,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'visible' => true,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => true
            ]
        );

        /** attribute -Support */
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'support',
            [
                'group' => 'Mattress attribute set',
                'type' => 'varchar',
                'label' => 'Support',
                'input' => 'boolean',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'frontend' => '',
                'backend' => '',
                'required' => false,
                'sort_order' => 106,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'visible' => true,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => true
            ]
        );
    }
}
