<?php

namespace Biztech\DPI\Setup;

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
                \Magento\Catalog\Model\Product::ENTITY, 'output_width', [
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'Custom output width (px)',
            'input' => 'text',
            'class' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'apply_to' => 'configurable,simple',
            'required' => false,
            'group' => 'Product Designer'
                ]
        );
        $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'output_height', [
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'Custom output height (px)',
            'input' => 'text',
            'class' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'apply_to' => 'configurable,simple',
            'required' => false,
            'group' => 'Product Designer'
                ]
        );
    }

}
