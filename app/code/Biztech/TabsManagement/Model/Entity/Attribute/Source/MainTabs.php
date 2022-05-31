<?php

namespace Biztech\TabsManagement\Model\Entity\Attribute\Source;

class MainTabs extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource {

    protected $request;
    protected $_options = [];
    protected $_objectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->request = $request;
        $this->_objectManager = $objectmanager;
    }
    public function getAllOptions() {

        // get request params
        $params = $this->request->getParams();

        // get product id
        $productId = (isset($params['id'])) ? $params['id'] : null;

        // init product type variable
        $productType = '';

        // if product id found
        if($productId) {

            // get product data by product id
            $productData = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($productId);

            // get product type
            $productType = $productData->getTypeId();
        }
        
        $model = $this->_objectManager->create('Biztech\Productdesigner\Model\Mysql4\TabsData\Collection');
        $collection = $model->getData();
        $tabs_array = array();
        foreach ($collection as $allmaintabs) {

            // if tab id is 6(ie. Name number) and product type is simple
            // don't push
            if($productType == 'simple' && $allmaintabs['id'] == 6) continue;
            $tabs_array[] = array(
                'label' => $allmaintabs['label'],
                'value' => $allmaintabs['id']
            );            
        }
        return $tabs_array;
    }

}
