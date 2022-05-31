<?php

namespace Biztech\Productdesigner\Controller\Adminhtml\Productdesigner;
class getDesignAreaImage extends \Magento\Backend\App\Action {

    public function __construct(
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($context);
    }

    public function execute() {
        $params  = $this->getRequest()->getParams();        
        $current_design_area_id = $params['current_design_area_id'];
        $alldesignAreaIds = json_decode($params['alldesignarea']);
        $id = $params['current_design_area_id'];
        foreach($alldesignAreaIds as $key=>$value){
            if($current_design_area_id == $alldesignAreaIds[$key]){
                $id = $key + 1;
            }
        }
        $next_design_area_id = false;
        $new1 = true;
        if(array_key_exists($id, $alldesignAreaIds)){
            $next_design_area_id = $alldesignAreaIds[$id];
            $new1 = false;
        }
        $data = array();
        $data['next_design_area_id'] = $next_design_area_id;
        $data['new1'] = $new1;
        $jsonData = (json_encode($data));
        $this->getResponse()->setBody($jsonData);
    }
}
