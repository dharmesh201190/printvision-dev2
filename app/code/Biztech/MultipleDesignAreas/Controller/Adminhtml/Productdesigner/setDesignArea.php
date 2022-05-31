<?php

/**
 * Copyright © 2017-2018 AppJetty. All rights reserved.
 */

namespace Biztech\MultipleDesignAreas\Controller\Adminhtml\Productdesigner;
class setDesignArea extends \Magento\Backend\App\Action {

    protected $layoutInterface;
    protected $selectionAreaFactory;
    protected $selectionAreaCollection;
    public function __construct(
        \Magento\Backend\App\Action\Context $context, \Magento\Framework\View\LayoutInterface $layoutInterface, \Biztech\Productdesigner\Model\SelectionareaFactory $selectionAreaFactory, \Biztech\Productdesigner\Model\Mysql4\Selectionarea\CollectionFactory $selectionAreaCollection
    ) {
        $this->layoutInterface = $layoutInterface;
        $this->selectionAreaFactory = $selectionAreaFactory;
        $this->selectionAreaCollection = $selectionAreaCollection;
        parent::__construct($context);
    }

    public function getWidth() {
        //return $this->_scopeConfig->getValue(self::WIDTH,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function execute() {
        $resultPage = $this->layoutInterface->createBlock('Biztech\Productdesigner\Block\Productdesigner');
        try {
            $arr = $this->prepareData();
            $result["status"] = 'success';
            $obj_selectionArea = $this->selectionAreaFactory->create();
            $obj_selectionArea->load($arr['current_design_area_id']);
            $current_selection_data = $obj_selectionArea->getSelectionArea();        

            $result["design_area"] = $resultPage->setData($arr)->setTemplate('Biztech_MultipleDesignAreas::helper/designarea.phtml')->toHtml();
            $result["selection"] = $arr['selection_data'];
            $result["current_design_area_id"] = $arr['current_design_area_id'];
            $result["next_design_area_id"] = $arr['next_design_area_id'];
            $result["image_side"] = $arr['image_side'];
        } catch (\Exception $e) {
            $result["status"] = 'error';
            $result["message"] = $e->getMessage();
        }
        $this->getResponse()->setBody(json_encode($result));
    }

    protected function prepareData() {
        $params = $this->getRequest()->getParams();
        $img_id = $params['imageid'];
        $img_url = $params['image_url'];
        $product_id = $params['product_id'];
        $image_side = $params['image_Side'];
        $current_design_area_id = $params['current_design_area_id'];
        $next_design_area_id = $params['next_design_area_id'];

        $coll_selectionarea = $this->selectionAreaCollection->create()->addFieldToFilter('image_id', $img_id);
        foreach ($coll_selectionarea as $key => $value) {
            $selection_data[$key] = isset($value['selection_area']) ? $value['selection_area'] : '';
            $design_id[$key] = isset($value['design_area_id']) ? $value['design_area_id'] : '';
        }

        $selection_data = (empty($selection_data)) ? '' : $selection_data;
        $design_id = (empty($design_id)) ? '' : $design_id;

        $data = array(
            "image_id" => $img_id,
            "image_side" => $image_side,
            "product_img_url" => $img_url,
            "product_id" => $product_id,
            "selection_data" => $selection_data,
            "current_design_area_id" => $current_design_area_id,
            "next_design_area_id" => $next_design_area_id,
            "design_id"=>$design_id
        );
        return $data;
    }
}
