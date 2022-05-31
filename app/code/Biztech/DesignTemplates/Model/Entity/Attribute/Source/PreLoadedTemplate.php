<?php

namespace Biztech\DesignTemplates\Model\Entity\Attribute\Source;

class PreLoadedTemplate extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource {

    /**
     * Retrieve all options array
     *
     * @return array
     */
    protected $request;
    protected $_options = [];
    protected $_designtemplates;

    public function __construct(
    \Magento\Framework\App\Request\Http $request, \Biztech\DesignTemplates\Model\Mysql4\Designtemplates\Collection $designtemplates
    ) {
        $this->request = $request;
        $this->_designtemplates = $designtemplates;
    }

    public function getAllOptions() {
        $product_id = $this->request->getParam('id');
        if ($product_id) {
            $designtemplates = $this->_designtemplates->addFieldToFilter(['product_id', 'associated_product_id'], [
                        ['eq' => $product_id],
                        ['eq' => $product_id]
                    ])->getData();

            if (empty($this->_options)) {
                $this->_options = array();
                $nodata = array(
                    'label' => __('Select Default template for this product'),
                    'value' => ""
                );
                array_push($this->_options, $nodata);
                if (!empty($designtemplates)) {
                    foreach ($designtemplates as $designtemplate) {
                        if ($designtemplate['template_title'] && $designtemplate['status'] == 1) {
                            $data = array(
                                'label' => $designtemplate['template_title'],
                                'value' => $designtemplate['designtemplates_id']
                            );
                            array_push($this->_options, $data);
                        }
                    }
                }
            }
        }
        return $this->_options;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray() {
        $_options = array();
        foreach ($this->getAllOptions() as $option) {
            $_options[$option['value']] = $option['label'];
        }
        return $_options;
    }

    /**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return string
     */
    public function getOptionText($value) {
        $options = $this->getAllOptions();
        foreach ($options as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }

}
