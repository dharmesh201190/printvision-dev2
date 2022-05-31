<?php

namespace Biztech\AdvancedImageEffects\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeData implements UpgradeDataInterface
{

	public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{
		if (version_compare($context->getVersion(), '2.0.1', '<')) {
			$imageEffects = [];
            $data = json_encode(array('min' => -2, 'max' => 2, 'step' => 0.002, 'value' => 0));
            $second_data = json_encode(array('min' => 2, 'max' => 20, 'step' => '', 'value' => 4));
            $third_data = json_encode(array('min' => 0, 'max' => 1, 'step' => 0.01, 'value' => 0.1));
            $imageEffects[] = [
                'effect_name' => 'BlackWhite',
                'value' => 0,
                'label' => 'BlackWhite',
                'default_value' => null,
                'is_filter' => 1
            ];
            $imageEffects[] = [
                'effect_name' => 'Vintage',
                'value' => 0,
                'label' => 'Vintage',
                'default_value' => null,
                'is_filter' => 1
            ];
            $imageEffects[] = [
                'effect_name' => 'Kodachrome',
                'value' => 0,
                'label' => 'Kodachrome',
                'default_value' => null,
                'is_filter' => 1
            ];
            $imageEffects[] = [
                'effect_name' => 'Technicolor',
                'value' => 0,
                'label' => 'Technicolor',
                'default_value' => null,
                'is_filter' => 1
            ];
            $imageEffects[] = [
                'effect_name' => 'Polaroid',
                'value' => 0,
                'label' => 'Polaroid',
                'default_value' => null,
                'is_filter' => 1
            ];
            $imageEffects[] = [
                'effect_name' => 'Hue',
                'value' => 1,
                'label' => 'rotation',
                'default_value' => $data,
                'is_filter' => 0
            ];
            $imageEffects[] = [
                'effect_name' => 'Pixelate',
                'value' => 5,
                'label' => 'blocksize',
                'default_value' => $second_data,
                'is_filter' => 0
            ];
            $imageEffects[] = [
                'effect_name' => 'Blur',
                'value' => 0.5,
                'label' => 'blur',
                'default_value' => $third_data,
                'is_filter' => 0
            ];
            $imageEffects[] = [
                'effect_name' => 'Sharpen',
                'value' => 0,
                'label' => 'matrix',
                'default_value' => null,
                'is_filter' => 1
            ];
            
            $imageEffects[] = [
                'effect_name' => 'Emboss',
                'value' => 0,
                'label' => 'matrix',
                'default_value' => null,
                'is_filter' => 1
            ];

	        foreach ($imageEffects as $key => $value) {
                $setup->getConnection()->update(
                    $setup->getTable('productdesigner_imageeffects'),
                    ['is_filter' => $value['is_filter']],
                    ['effect_name = ?' => $value['effect_name']]
                );
	        }
		}
	}
}
