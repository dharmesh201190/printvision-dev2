<?php
namespace Biztech\ThemeColors\Block\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class SaveThemeConfiguration extends Field {

    protected $_template = 'Biztech_ThemeColors::system/config/saveThemeButton.phtml';

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
        return $this->getUrl('biztech_themecolors/system_config/SaveThemeConfiguration');
    }
    public function getCurrentStore(){
        return $this->getRequest()->getParam('store');
    }

    public function getButtonHtml() {
        $button = $this->getLayout()->createBlock(
                        'Magento\Backend\Block\Widget\Button'
                )->setData(
                [
                    'id' => 'save_theme_button',
                    'label' => __('Save Theme'),
                    'onclick' => 'javascript:saveTheme(); return false;'
                ]
        );

        return $button->toHtml();
    }

}
