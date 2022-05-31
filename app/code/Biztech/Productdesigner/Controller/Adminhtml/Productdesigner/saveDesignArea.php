<?php
namespace Biztech\Productdesigner\Controller\Adminhtml\Productdesigner;

class saveDesignArea extends \Magento\Backend\App\Action {

    protected $selectionAreaFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context, \Biztech\Productdesigner\Model\SelectionareaFactory $selectionAreaFactory
    ) {
        $this->selectionAreaFactory = $selectionAreaFactory;
        parent::__construct($context);
    }

    public function execute() {
        // Variable declaration
        $params = $this->getRequest()->getParams();
        $img_id = $params['image_id'];
        $maskig_id = $params['maskig_id'];
        $current_design_area_id = $params['current_design_area_id'];
        $product_id = $params['product_id'];
        $image_side = $params['image_side'];

        // prepare selection area array using function
        $selection_area_arr = $this->prepareData();

        // get selection area model
        $selectionAreaModel = $this->selectionAreaFactory->create();

        // load current design area id if exists
        if ($current_design_area_id) {
            $selectionAreaModel->load($current_design_area_id);
        }

        // prepare selection area object(json)
        $selection_area = json_encode($selection_area_arr);

        // perform save opearation
        try {
            $selectionAreaModel->setImageId($img_id)
            ->setMaskingImageId($maskig_id)
            ->setSelectionArea($selection_area)
            ->setProductId($product_id)
            ->setImagesideId($image_side);
            $selectionAreaModel->save();
            $result["status"] = 'success';
            $jsonData = (json_encode($result));
            $jsonData = (json_encode($result));
            $this->getResponse()->setBody($jsonData);
        } catch (\Exception $e) {
            $result["status"] = 'error';
            $result["message"] = $e->getMessage();
        }

    }
    protected function prepareData()
    {
        $params = $this->getRequest()->getParams();
        $img_id = $params['image_id'];
        $x1 = $params['x1'];
        $y1 = $params['y1'];
        $x2 = $params['x2'];
        $y2 = $params['y2'];
        $width = $params['w'];
        $height = $params['h'];

        $selection_area_arr = array(
            'width' => $width,
            'height' => $height,
            'x1' => $x1,
            'y1' => $y1,
            'x2' => $x2,
            'y2' => $y2,
        );
        $selection_area_arr['image_id'] = $img_id;

        return $selection_area_arr;
    }

}
