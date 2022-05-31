<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Customer;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;

class Changepassword extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;
    protected $cartHelper;
    protected $request;
    protected $customerSession;
    protected $storeManager;
    protected $customerModel;
    protected $encryption;
    protected $formKey;

    /**
     * @param Context                                          $context
     * @param JsonFactory                                      $jsonFactory
     * @param \Biztech\Magemobcart\Helper\Data                 $cartHelper
     * @param \Magento\Framework\App\Request\Http              $request
     * @param \Magento\Customer\Model\Session                  $customerSession
     * @param \Magento\Store\Model\StoreManagerInterface       $storeManager
     * @param \Magento\Customer\Model\Customer                 $customerModel
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryption
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Customer $customerModel,
        \Magento\Framework\Encryption\EncryptorInterface $encryption,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_cartHelper = $cartHelper;
        $this->_request = $request;
        $this->_customerSession = $customerSession;
        $this->_storeManager = $storeManager;
        $this->_customerModel = $customerModel;
        $this->_encryption = $encryption;
        $this->formKey = $formKey;
        $this->_request->setParam('form_key', $this->formKey->getFormKey());
        parent::__construct($context);
    }

    /**
     * This function is used for change password for current customer.
     * @return Magento\Framework\Controller\Result\JsonFactory
     */
    public function execute()
    {
        $jsonResult = $this->_jsonFactory->create();
        if ($this->_cartHelper->isEnable()) {
            if (!$this->_cartHelper->getHeaders()) {
                $errorResult = array('status'=> false,'message' => $this->_cartHelper->getHeaderMessage());
                $jsonResult->setData($errorResult);
                return $jsonResult;
            }
            $postData = $this->_request->getParams();
            try {
                $customerid = $postData['customerid'];
                $username = $postData['email'];
                $oldpassword = $postData['old_password'];
                
                if (array_key_exists('new_password', $postData)) {
                    $password = $postData['new_password'];
                }
                if (array_key_exists('confirm_password', $postData)) {
                    $confirmation = $postData['confirm_password'];
                }
                if (!$this->checkPasswordConfirmation($password, $confirmation)) {
                    $message = 'Please make sure your passwords match.';
                    $responseArr['status'] = 'false';
                    $responseArr['message'] = $message;
                    $jsonResult->setData($responseArr);
                    return $jsonResult;
                }
                $storeid = $postData['storeid'];
                $websiteId = $this->_storeManager->getStore($storeid)->getWebsiteId();
                try {
                    $login_customer_result = $this->_customerModel->setWebsiteId($websiteId)->authenticate($username, $oldpassword);
                    $validate = 1;
                } catch (\Exception $e) {
                    $validate = 0;
                    $error_msg = $e->getMessage();
                    $customer_array = array(
                    'entity_id' => $customer->getId(),
                    'status' => 'success',
                    'message' => $error_msg
                    );
                    $jsonResult->setData($customer_array);
                    return $jsonResult;
                }
                
                try {
                    $customer = $this->_customerModel->load($customerid);
                    $hashedPassword = $this->_encryption->getHash($postData['new_password'], true);
                    $customer->setPasswordHash($hashedPassword);
                } catch (\Exception $e) {
                    $message = $e->getMessage();
                    $responseArr['message'] = $message;
                    $jsonResult->setData($responseArr);
                    return $jsonResult;
                }
                if ($validate == 1) {
                    try {
                        $customer->save();
                    } catch (\Exception $e) {
                        $validate = 0;
                        $error_msg = $e->getMessage();
                        $customer_array = array(
                        'entity_id' => $customer->getId(),
                        'status' => 'false',
                        'message' => $error_msg
                        );
                        $jsonResult->setData($customer_array);
                        return $jsonResult;
                    }
                    if ($this->_customerSession->isLoggedIn()) {
                        $this->_customerSession->logout()->renewSession();
                    }
                    $customer_array = array(
                    'status' => 'true',
                    'message' => 'Your Password has been Changed Successfully'
                    );
                    $jsonResult->setData($customer_array);
                    return $jsonResult;
                }
            } catch (\Exception $e) {
                $customer_array = array(
                'status' => 'error',
                'message' => $e->getMessage()
                );
                $jsonResult->setData($customer_array);
                return $jsonResult;
            }
        } else {
            $returnExtensionArray = array('enable' => false);
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
    }
    public function checkPasswordConfirmation($password, $confirmation)
    {
        if ($password != $confirmation) {
            return false;
        }
        return true;
    }
}
