<?php

namespace Biztech\AdvancedImageEffects\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Eav\Setup\EavSetupFactory;

class InstallData implements InstallDataInterface {

    private $eavSetupFactory;
    protected $_directoryList;
    protected $_fileSystem;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem $filesystem
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->_directoryList = $directoryList;
        $this->_fileSystem = $filesystem;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);                               
        $imageEffects = [];
        $data = json_encode(array('min' => -2, 'max' => 2, 'step' => 0.002, 'value' => 0));
        $second_data = json_encode(array('min' => 2, 'max' => 20, 'step' => '', 'value' => 4));
        $third_data = json_encode(array('min' => 0, 'max' => 1, 'step' => 0.01, 'value' => 0.1));
        $imageEffects[] = [
            'effect_name' => 'BlackWhite',
            'value' => 0,
            'label' => 'BlackWhite',
            'default_value' => null,
            'effect_image' => 'productdesigner/effects/BlackWhite.png'
        ];
        $imageEffects[] = [
            'effect_name' => 'Kodachrome',
            'value' => 0,
            'label' => 'Kodachrome',
            'default_value' => null,
            'effect_image' => 'productdesigner/effects/Kodachrome.png'
        ];
        $imageEffects[] = [
            'effect_name' => 'Technicolor',
            'value' => 0,
            'label' => 'Technicolor',
            'default_value' => null,
            'effect_image' => 'productdesigner/effects/Technicolor.png'
        ];
        $imageEffects[] = [
            'effect_name' => 'Polaroid',
            'value' => 0,
            'label' => 'Polaroid',
            'default_value' => null,
            'effect_image' => 'productdesigner/effects/Polaroid.png'
        ];
        $imageEffects[] = [
            'effect_name' => 'Hue',
            'value' => 1,
            'label' => 'rotation',
            'default_value' => $data,
            'effect_image' => 'productdesigner/effects/Hue.png'
        ];
        $imageEffects[] = [
            'effect_name' => 'Pixelate',
            'value' => 5,
            'label' => 'blocksize',
            'default_value' => $second_data,
            'effect_image' => 'productdesigner/effects/Pixelate.png'
        ];
        $imageEffects[] = [
            'effect_name' => 'Blur',
            'value' => 0.5,
            'label' => 'blur',
            'default_value' => $third_data,
            'effect_image' => 'productdesigner/effects/Blur.png'
        ];
        $imageEffects[] = [
            'effect_name' => 'Sharpen',
            'value' => 0,
            'label' => 'matrix',
            'default_value' => null,
            'effect_image' => 'productdesigner/effects/Sharpen.png'
        ];
        $imageEffects[] = [
            'effect_name' => 'Emboss',
            'value' => 0,
            'label' => 'matrix',
            'default_value' => null,
            'effect_image' => 'productdesigner/effects/Emboss.png'
        ];
        if (count($imageEffects)) {
            $setup->getConnection()->insertMultiple($setup->getTable('productdesigner_imageeffects'), $imageEffects);
        }

        $brightness_data = json_encode(array('min' => -1, 'max' => 1, 'step' => 0.003921, 'value' => 0.1));
        $contrast_data = json_encode(array('min' => -1, 'max' => 1, 'step' => 0.003921, 'value' => 0));
        $noise_data = json_encode(array('min' => 0, 'max' => 1000, 'step' => '', 'value' => 100));
        $setup->getConnection()->update(
            $setup->getTable('productdesigner_imageeffects'),
            ['default_value' => $brightness_data],
            ['effect_name = ?' => 'Brightness']
        );

        $setup->getConnection()->update(
            $setup->getTable('productdesigner_imageeffects'),
            ['default_value' => $contrast_data],
            ['effect_name = ?' => 'Contrast']
        );

        $setup->getConnection()->update(
            $setup->getTable('productdesigner_imageeffects'),
            ['default_value' => $noise_data],
            ['effect_name = ?' => 'Noise']
        );

        $setup->getConnection()->update(
            $setup->getTable('productdesigner_imageeffects'),
            ['default_value' => $contrast_data],
            ['effect_name = ?' => 'Saturation']
        );

        $this->storeMediaData();
    }

    protected function storeMediaData() {
        $appPath = $this->_directoryList->getPath('app');
        $sampleDataPath = $appPath . "/code/Biztech/AdvancedImageEffects/SampleData/";
        $file = $sampleDataPath . "effects.zip";
        if(file_exists($file)) {
            $reader = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
            $extractPath = $reader->getAbsolutePath() . 'productdesigner/';
            $path =$reader->getAbsolutePath();
            if(!is_dir($path. 'productdesigner')){
                mkdir($path . "productdesigner");
            }
            // if (!is_dir($extractPath . "effects")) {
            //     mkdir($extractPath . "effects");
            // }
            $this->unpack($file, $extractPath);
        }
    }

    public function unpack($source, $destination)
    {
        $zip = new \ZipArchive();
        $res = $zip->open($source);
        if ($res === TRUE) {
          $zip->extractTo($destination);
          $zip->close();
      }
  }
}
