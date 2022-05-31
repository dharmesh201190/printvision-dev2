<?php

namespace Biztech\NameNumber\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Eav\Setup\EavSetupFactory;

class UpgradeData implements UpgradeDataInterface {

    private $eavSetupFactory;

    public function __construct(
    EavSetupFactory $eavSetupFactory) {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
        if (version_compare($context->getVersion(), '2.0.2', '<')) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
             $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY, 'enable_name_number', [
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Enable Name/Number',
                'input' => 'boolean',
                'class' => '',
                'source' => 'Magento\Catalog\Model\Product\Attribute\Source\Boolean',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'visible' => true,
                'apply_to' => 'configurable',
                'required' => false,
                'group' => 'Product Designer'
                    ]
            );
        }
    }

}
