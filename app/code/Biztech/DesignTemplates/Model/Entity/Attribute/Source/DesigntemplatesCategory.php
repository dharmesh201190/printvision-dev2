<?php

namespace Biztech\DesignTemplates\Model\Entity\Attribute\Source;

class DesigntemplatesCategory extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource {

    /**
     * Retrieve all options array
     *
     * @return array
     */
    protected $request;
    protected $_options = [];
    protected $_designtemplatecategory;

    public function __construct(
    \Magento\Framework\App\Request\Http $request, \Biztech\DesignTemplates\Model\Designtemplatecategory $designtemplatecategory
    ) {
        $this->request = $request;
        $this->_designtemplatecategory = $designtemplatecategory;
    }

    public function getAllOptions() {
        $storeid = $this->request->getParam('store');
        $obj_product = $this->_designtemplatecategory->getCollection()->addFieldToFilter('store_id', array(
            array('finset' => array('0')),
            array('finset' => array($storeid)),
        ))->addFieldToFilter('status',array('eq' => 1));
        if (sizeof($this->_options) <= 0) {
            $this->_options = array();
            foreach ($obj_product as $obj) {
                $dashLevel = '';
                for ($i = 0; $i < $obj->getLevel(); $i++) {
                    $dashLevel .= '-';
                }
                $dashLevel .= ' ';
                $data = array(
                    'label' => $dashLevel . $obj->getCategoryTitle(),
                    'value' => $obj->getId()
                );
                array_push($this->_options, $data);
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
