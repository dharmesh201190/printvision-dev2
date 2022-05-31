<?php
namespace Printvision\ProductInquiry\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    protected $_eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->_eavSetupFactory = $eavSetupFactory;
    }
    
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $groupName = 'Product Inquiry';

        $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'is_product_inquiry_enable',
            [
                'type' => 'int',
                'input' => 'boolean',
                'label' => 'Enable',
                'required' => false,
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                'default' => '0',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'user_defined' => true,
                'sort_order' => 1,
                'group' => $groupName
            ]
        );
         $eavSetup->addAttribute(
             \Magento\Catalog\Model\Product::ENTITY,
             'product_inquiry_label',
             [
                'type' => 'varchar',
                'input' => 'text',
                'label' => 'Label',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'user_defined' => true,
                'sort_order' => 2,
                'group' => $groupName,
                'note' => 'Leave label as blank if you just want to hide price without having any inquiry button.'
             ]
         );
         $eavSetup->addAttribute(
             \Magento\Catalog\Model\Product::ENTITY,
             'is_addtocart_allowed',
             [
                'type' => 'int',
                'input' => 'boolean',
                'label' => 'Allow Add To Cart',
                'required' => false,
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                'default' => '1',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'user_defined' => true,
                'sort_order' => 3,
                'group' => $groupName
             ]
         );

         $eavSetup->addAttribute(
             \Magento\Catalog\Model\Product::ENTITY,
             'is_product_price_disclosed',
             [
                'type' => 'int',
                'input' => 'boolean',
                'label' => 'Disclose Product Price',
                'required' => false,
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                'default' => '1',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'user_defined' => true,
                'sort_order' => 3,
                'group' => $groupName
             ]
         );

        $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $attributeSetId = $eavSetup->getAttributeSetId($entityTypeId, 'Default');

        $eavSetup->addAttributeToGroup($entityTypeId, $attributeSetId, $groupName, 'is_product_inquiry_enable');
        $eavSetup->addAttributeToGroup($entityTypeId, $attributeSetId, $groupName, 'product_inquiry_label');
        $eavSetup->addAttributeToGroup($entityTypeId, $attributeSetId, $groupName, 'is_addtocart_allowed');
        $eavSetup->addAttributeToGroup($entityTypeId, $attributeSetId, $groupName, 'is_product_price_disclosed');
    }
}
