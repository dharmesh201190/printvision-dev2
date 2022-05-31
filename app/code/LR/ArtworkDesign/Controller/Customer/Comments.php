<?php 
namespace LR\ArtworkDesign\Controller\Customer;  
class Comments extends \Magento\Framework\App\Action\Action { 
	public function execute() { 
		$this->_view->loadLayout(); 
		$this->_view->renderLayout(); 
	} 
} 
