<?php
namespace Biztech\Productdesigner\Controller\Designs;

class deleteDesign extends \Magento\Framework\App\Action\Action {

    protected $_helperData;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Biztech\Productdesigner\Helper\Data $helper
    ) {
        $this->_helperData = $helper;
        parent::__construct($context);
    }

    public function execute() {

        $data = $this->getRequest()->getParams();
        $redirect = true;
        if (!isset($data['design_id'])) {
            $data = json_decode(file_get_contents('php://input'), TRUE);
            $redirect = false;
        }
        $design_id = $data['design_id'];
        
        try {
            $this->_helperData->deleteMyDesign($design_id);
            $this->messageManager->addSuccess('Design was successfully deleted');
            if ($redirect) {
                $this->_redirect('*/designs/mydesigns');
            } else {
                $this->getResponse()->setBody(json_encode(array('status' => 'success')));
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('*/designs/mydesigns');
        }
    }    

}
