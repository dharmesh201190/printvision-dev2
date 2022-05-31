<?php

namespace Biztech\Productdesigner\Controller\Customer;

class Logout extends \Biztech\Productdesigner\Controller\Customer {
	
	
	public function execute() {
		try {
			$result = array();
			$result['status'] = "success";
			if($this->customerSession->getId()) {
				$this->customerSession->logout();
				$result['message'] = "Customer logout successfully";
			} else {
				$result['message'] = "Customer is not login";
			}
			$this->getResponse()->setBody(json_encode($result));
		} catch(\Exception $e) {
			$this->setCatch($e);
		}
	}

	protected function setCatch($e) {
		$response = $this->_infoHelper->throwException($e, self::class);		
		$this->getResponse()->setBody(json_encode($response));
	}
}
