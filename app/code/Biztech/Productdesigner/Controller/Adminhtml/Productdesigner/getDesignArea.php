<?php
namespace Biztech\Productdesigner\Controller\Adminhtml\Productdesigner;
class getDesignArea extends \Magento\Backend\App\Action {

    protected $selectionAreaCollection;

    public function __construct(
        \Magento\Backend\App\Action\Context $context, \Biztech\Productdesigner\Model\Mysql4\Selectionarea\CollectionFactory $selectionAreaCollection
    ) {
        $this->selectionAreaCollection = $selectionAreaCollection;
        parent::__construct($context);
    }

    public function execute() {
        $params = $this->getRequest()->getParams();
        $model = $this->selectionAreaCollection->create();
        $model1 = $model->addFieldToFilter('image_id',
            $params['imageid']);
        $alldesignAreaIds = array();
        foreach ($model1 as $key => $value) {
            $alldesignAreaIds[] = $value['design_area_id'];
        }
        $jsonData = (json_encode($alldesignAreaIds));
        $this->getResponse()->setBody($jsonData);
    }
}
