<?php
namespace Biztech\Productdesigner\Block\Adminhtml\Subtabs\Renderer;

class Subtab extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {

    public function render(\Magento\Framework\DataObject $row) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $allSubTabs = $objectManager->create('Biztech\Productdesigner\Model\Adminhtml\Config\Source\Maintab')->toOptionArray();
        $column_value = $row->getSubtabs();

        $render=[];
        $column_value_array = explode(",", $column_value);
        foreach ($allSubTabs as $value) {
            if (in_array($value['value'], $column_value_array)) {
                $render[] = $value['label'];
            }
        }
        return implode(", ", $render);
    }

}
