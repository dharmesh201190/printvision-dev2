<?php

namespace Biztech\Productdesigner\Block\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Locale extends Field {

    protected $_template = 'Biztech_Productdesigner::system/config/locale.phtml';

   
    public function __construct(
    Context $context, array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function render(AbstractElement $element) {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    protected function _getElementHtml(AbstractElement $element) {
        return $this->_toHtml();
    }

    public function getAjaxUrl() {
        return $this->getUrl('biztech_productdesigner/system_config/locale');
    }

    public function getButtonHtml() {
        $button = $this->getLayout()->createBlock(
                        'Magento\Backend\Block\Widget\Button'
                )->setData(
                [
                    'id' => 'collect_button',
                    'label' => __('Translate'),
                    'onclick' => 'javascript:changelocale(); return false;'
                ]
        );

        return $button->toHtml();
    }

}
