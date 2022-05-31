<?php
namespace Biztech\Productdesigner\Block\Adminhtml\Tabs\Renderer;

class Maintab extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {

    public function render(\Magento\Framework\DataObject $row) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $allMainTabs = $objectManager->create('Biztech\Productdesigner\Model\Adminhtml\Config\Source\Maintab')->toOptionArray();
        $column_value = $row->getId();
        foreach ($allMainTabs as $value) {
            if ($value['value'] == $column_value) {
                return $value['label'];
            }
        }
    }

}
