<?php

namespace Biztech\Productdesigner\Controller\Customer;

use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\EmailNotConfirmedException;

class Login extends \Biztech\Productdesigner\Controller\Customer {

    public function execute() {
        try {

            // if user already logged in
            if ($this->customerSession->isLoggedIn() || !$this->formKeyValidator->validate($this->getRequest())) {
                $result['status'] = "fail";
                $result['log'] = "User already logged in";
                $this->getResponse()->setBody(json_encode($result));
            }

            // get post request payload
            $loginInput = json_decode(file_get_contents('php://input'), TRUE);

            // if payload is empty
            if (!$loginInput || $loginInput == 'undefined' || $loginInput == null) {
                $result['status'] = "Failed";
                $result['log'] = "Empty Payload!";
                return $this->getResponse()->setBody(json_encode($result));
            }

            // if payload is not empty
            if (!empty($loginInput['customer_email']) && !empty($loginInput['customer_password'])) {
                $result = $this->loginCustomer($loginInput);
                if (!isset($result['customer_id'])) {
                    $result = array('log' => 'Invalid Credentials', 'status' => 'failed', 'message' => 'Something went wrong');
                }
                $this->getResponse()->setBody(json_encode($result));
            } else {
                $message = __('A login and a password are required.');
                $result['log'] = $message;
                $result['status'] = 'fail';
                $this->getResponse()->setBody(json_encode($result));
            }
        } catch (\Exception $e) {
            $this->setCatch($e);
        }
    }

    protected function loginCustomer($loginInput) {
        try {
            $customer = $this->customerAccountManagement->authenticate($loginInput['customer_email'], base64_decode($loginInput['customer_password']));
            $customer_id = $customer->getId();
            if ($customer_id != NULL) {
                $result['customer_id'] = $customer_id;
                $result['customer_login'] = 1;
                $result["status"] = 'success';
            }
            $message = __('You have loggged in');
            $this->customerSession->setCustomerDataAsLoggedIn($customer);
            $this->customerSession->regenerateId();
            $result['status'] = 'success';
            return $result;
        } catch (EmailNotConfirmedException $e) {
            $this->setCatch($e);
        } catch (AuthenticationException $e) {
            $this->setCatch($e);
        } catch (\Exception $e) {
            $this->setCatch($e);
        }
    }

    protected function setCatch($e) {
        $response = $this->_infoHelper->throwException($e, self::class);
        $this->getResponse()->setBody(json_encode($response));
    }

}
