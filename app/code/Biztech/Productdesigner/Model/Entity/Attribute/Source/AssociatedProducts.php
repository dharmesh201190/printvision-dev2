<?php

namespace Biztech\Productdesigner\Model\Entity\Attribute\Source;

class AssociatedProducts extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource {
    protected $request;
    protected $_options = [];
    protected $productModel;
    protected $productTypeModel;

    public function __construct(
    \Magento\Framework\App\Request\Http $request, \Magento\Catalog\Model\ProductFactory $productModel, \Magento\ConfigurableProduct\Model\Product\Type\ConfigurableFactory $productTypeModel
    ) {
        $this->request = $request;
        $this->productModel = $productModel;
        $this->productTypeModel = $productTypeModel;
    }

    public function getAllOptions() {
        $product_id = $this->request->getParam('id');
        if ($product_id) {
            $product = $this->productModel->create()->load($product_id);
            $product_type = $product->getTypeId();
            if ($product_type == 'configurable') {
                $childProducts = $this->productTypeModel->create()
                        ->getChildrenIds($product_id);
                if (empty($this->_options)) {
                    $this->_options = array();
                    $nodata = array(
                        'label' => __('Select Default Product'),
                        'value' => "",
                    );
                    array_push($this->_options, $nodata);
                    foreach ($childProducts[0] as $childProductId) {
                        $childProduct = $this->productModel->create()->load($childProductId);
                        $data = array(
                            'label' => $childProduct->getName(),
                            'value' => $childProductId,
                        );
                        array_push($this->_options, $data);
                    }
                }
            }
        }
        return $this->_options;
    }

    public function getOptionArray() {
        $_options = array();
        foreach ($this->getAllOptions() as $option) {
            $_options[$option['value']] = $option['label'];
        }
        return $_options;
    }

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
