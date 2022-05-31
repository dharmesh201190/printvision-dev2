<?php
namespace Biztech\EditablePdf\Controller\Adminhtml\Productdesigner;

use \Magento\Framework\View\LayoutFactory;

class viewDesign extends \Biztech\Productdesigner\Controller\Adminhtml\Productdesigner\viewDesign {

    public function execute() {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('View Design'));
        return $resultPage;
    }

}
