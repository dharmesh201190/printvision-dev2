<?php

namespace Biztech\BackgroundPatterns\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Eav\Setup\EavSetupFactory;

class UpgradeData implements UpgradeDataInterface
{
    private $eavSetupFactory;
   
    public function __construct(
        EavSetupFactory $eavSetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
    }

	public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{
        if (version_compare($context->getVersion(), '2.0.3', '<')) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'default_background_pattern_category', [
                    'type' => 'varchar',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Set Default Background Pattern Category',
                    'attribute_set' => '',
                    'input' => 'select',
                    'visible' => true,
                    'required' => false,
                    'apply_to' => 'configurable,simple',
                    'source' => 'Biztech\BackgroundPatterns\Model\Entity\Attribute\Source\ClipartCategories',
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                    'group' => 'Product Designer'
                ]
            );
        }       
	}
}
