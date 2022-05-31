<?php

namespace Biztech\Productdesigner\Controller\Customer;

class checkForLogin extends \Biztech\Productdesigner\Controller\Customer {

    public function execute() {
        try {
            if ($this->customerSession->isLoggedIn()) {
                $customerData = $this->customerSession->getCustomer();
                $customer_id = $customerData->getId();
                $result['customer_login'] = 1;
                $result['customer_id'] = $customer_id;
            } else {
                $result['customer_login'] = 0;
                $result['customer_id'] = null;
            }
            $result["status"] = 'success';
            $this->getResponse()->setBody(json_encode($result));
        } catch (\Exception $e) {
            $response = $this->_infoHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }
    }

}
