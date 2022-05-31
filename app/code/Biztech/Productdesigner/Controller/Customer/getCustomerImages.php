<?php
namespace Biztech\Productdesigner\Controller\Customer;

class getCustomerImages extends \Biztech\Productdesigner\Controller\Customer {

    public function execute() {
        try {
            if ($this->customerSession->isLoggedIn()) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $customerData = $this->customerSession->getCustomer();
                $customer_id = $customerData->getId();
                $limit = $params['limit'];
                $customer_images = $this->_helper->fetchCustomerUploadImages($customer_id, $limit, $params['page']);
                $result['images'] = $customer_images['imageData'];
                $result['loadMore'] = $customer_images['loadMore'];
            } else {
                $result['images'] = [];
                $result['loadMore'] = 0;
            }
            $result["status"] = 'success';
            $this->getResponse()->setBody(json_encode($result));
        } catch (\Exception $e) {
            $response = $this->_infoHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }
    }

}
