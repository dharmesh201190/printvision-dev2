<?php

namespace Biztech\Magemobcart\Model;

class Category
{
    // public static function getOptionArray()
    // {
    //     // $optionArray = array();
    //     $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    //     $rootcategory=$objectManager->create('Magento\Catalog\Model\Category')->getCollection()->addAttributeToSelect('*')->addFieldToFilter('level', 1);
    //     foreach ($rootcategory as $root) {
    //         $root_id=$root->getEntityId();
    //         $maincategory=$objectManager->create('Magento\Catalog\Model\Category')->getCollection()->addAttributeToSelect('*')->addFieldToFilter('parent_id', $root_id)->addFieldToFilter('level', 2);
    //         foreach ($maincategory as $main) {
    //             $main_id=$main->getEntityId();
    //             $optionArray[]=array('label' => $main->getName(),'value' => $main_id);
    //             $subcategory=$objectManager->create('Magento\Catalog\Model\Category')->getCollection()->addAttributeToSelect('*')->addFieldToFilter('parent_id', $main_id)->addFieldToFilter('level', 3);
    //             foreach ($subcategory as $sub) {
    //                 $sub_id=$sub->getEntityId();
    //                 $optionArray[]=array('label' => $sub->getName(),'value' => $sub_id);
    //             }
    //         }
    //     }
    //     $result = array();
    //     foreach ($optionArray as $key => $value) {
    //         $result[$value['value']] = $value['label'];
    //     }
    //     return $result;
    // }
    public static function getOptionArray()
    {
        // $optionArray = array();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $rootcategory=$objectManager->create('Magento\Catalog\Model\Category')->getCollection()->addAttributeToSelect('*');
        foreach ($rootcategory as $root) {
            $root_id=$root->getEntityId();
            if ($root_id != 1) {
                $optionArray[]=array('label' => $root->getName(),'value' => $root_id);
            }
        }
        $result = array();
        foreach ($optionArray as $key => $value) {
            $result[$value['value']] = $value['label'];
        }
        return $result;
    }
}
