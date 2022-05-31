<?php

namespace Biztech\DesignTemplates\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface {

    private $eavSetupFactory;

    public function __construct(
        EavSetupFactory $eavSetupFactory) {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'design_templates_category', [
                'type' => 'varchar',
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'frontend' => '',
                'label' => 'Design Templates Category',
                'input' => 'multiselect',
                'visible' => true,
                'required' => false,
                'apply_to' => 'configurable,simple',
                'source' => 'Biztech\DesignTemplates\Model\Entity\Attribute\Source\DesigntemplatesCategory',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'group' => 'Product Designer'
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'pre_loaded_template', [
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Pre Loaded Template',
                'input' => 'select',
                'visible' => true,
                'required' => false,
                'apply_to' => 'configurable,simple',
                'source' => 'Biztech\DesignTemplates\Model\Entity\Attribute\Source\PreLoadedTemplate',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'group' => 'Product Designer'
            ]
        );
    }
}
