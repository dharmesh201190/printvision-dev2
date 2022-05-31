<?php

namespace Biztech\Productdesigner\Model\Adminhtml\Config\Source;

class FontFamily extends \Magento\Config\Block\System\Config\Form\Field {

    protected $_scopeConfig;
    protected $fontsCollection;
    const EnableFont = 'productdesigner/textconfiguration/enablegooglefonts';
    const FontList = 'productdesigner/textconfiguration/googlefontlist';
    public function __construct(
    \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Biztech\Productdesigner\Model\Mysql4\Fonts\CollectionFactory $fontsCollection
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->fontsCollection = $fontsCollection;                
    }

    public function toOptionArray() {
        $option_array = array();
        $model = $this->fontsCollection->create()->addFieldToFilter('status', 1)->setOrder('font_label', 'asc');
        $font_styles = $model->getData();        
        foreach ($font_styles as $font) {
            $option_array[] = array(
                'value' => $font['fonts_id'],
                'label' => $font['font_label']
            );
        }

        return $option_array;
    }

}
