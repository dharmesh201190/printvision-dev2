<?php

namespace Biztech\ImageSlider\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Eav\Setup\EavSetupFactory;

class InstallData implements InstallDataInterface {

    private $eavSetupFactory;
    protected $_directoryList;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->_directoryList = $directoryList;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);                               
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'enable_image_thumbnails', [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Enable Image Thumbnails',
                'input' => 'select',
                'class' => '',
                'source' => 'Biztech\Productdesigner\Model\Source\Statusyesno',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'visible' => true,
                'apply_to' => 'configurable,simple',
                'required' => false,
                'group' => 'Product Designer',
                'note' => 'Select Yes to enable image thumbnails at product level.'
            ]
        );    
    }

}
