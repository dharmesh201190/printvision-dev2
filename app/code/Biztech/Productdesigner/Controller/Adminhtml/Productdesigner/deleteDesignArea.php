<?php

namespace Biztech\Productdesigner\Controller\Adminhtml\Productdesigner;
class deleteDesignArea extends \Magento\Backend\App\Action {

    protected $selectionAreaFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context, \Biztech\Productdesigner\Model\SelectionareaFactory $selectionAreaFactory
    ) {
        $this->selectionAreaFactory = $selectionAreaFactory;
        parent::__construct($context);
    }

    public function execute() {
        try{
            $result = array();
            $result["status"]  = 'success';
            $obj_selectionArea = $this->selectionAreaFactory->create();
            $design_id = $this->getRequest()->getPost('design_id');
            $selectionArea = $obj_selectionArea->load($design_id);
            $selectionArea->delete();
        }
        catch (\Exception $e) {

            $result["status"]  = 'error';
            $result["message"] = $e->getMessage();
        }
        $jsonData = json_encode($result);    
        $this->getResponse()->setBody($jsonData); 
    }
}
