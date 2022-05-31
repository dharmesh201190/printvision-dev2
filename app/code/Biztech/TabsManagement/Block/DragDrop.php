<?php
namespace Biztech\TabsManagement\Block;

class DragDrop extends \Magento\Config\Block\System\Config\Form\Field {


    public function __construct(
    \Magento\Backend\Block\Template\Context $context, array $data = []
    ) {
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
        $html = $element->getElementHtml();
        $value = $element->getData('value');
        $html .= '<script type="text/javascript">
                    require(["jquery"], function ($){
                        jQuery("#productdesigner_layout_general_main_tab").dragOptions({highlight: "►"});
                    });
                  </script>';
        return $html;
    }

}