<?php

namespace Biztech\TabsManagement\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Eav\Setup\EavSetupFactory;

class InstallData implements InstallDataInterface {

    private $eavSetupFactory;

    public function __construct(
        EavSetupFactory $eavSetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);               
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'main_tabs', [
                'type' => 'text',
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'frontend' => '',
                'label' => 'Main Tabs',
                'input' => 'multiselect',
                'class' => '',
                'source' => 'Biztech\TabsManagement\Model\Entity\Attribute\Source\MainTabs',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'visible' => true,
                'apply_to' => 'configurable,simple',
                'required' => false,
                'group' => 'Product Designer'
            ]
        );
    }
}
