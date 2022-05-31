<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Customer;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;

class Logout extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;
    protected $cartHelper;
    protected $request;
    protected $customerSession;
    protected $deviceDataModel;
    protected $formKey;

    /**
     * @param Context                               $context
     * @param JsonFactory                           $jsonFactory
     * @param \Magento\Framework\App\Request\Http   $request
     * @param \Biztech\Magemobcart\Helper\Data      $cartHelper
     * @param \Magento\Customer\Model\Session       $customerSession
     * @param \Biztech\Magemobcart\Model\Devicedata $deviceDataModel
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        \Magento\Framework\App\Request\Http $request,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Biztech\Magemobcart\Model\Devicedata $deviceDataModel,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_cartHelper = $cartHelper;
        $this->_request = $request;
        $this->_customerSession = $customerSession;
        $this->_deviceDataModel = $deviceDataModel;
        $this->formKey = $formKey;
        $this->_request->setParam('form_key', $this->formKey->getFormKey());
        parent::__construct($context);
    }

    /**
     * This function is used for logout current customer.
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
            $session = $this->_customerSession;
            $responseArr = array();
            $postData = $this->_request->getParams();
            
            if (array_key_exists('devicetoken', $postData)) {
                $deviceToken = $postData['devicetoken'];
                if ($deviceToken != "") {
                    $collections = $this->_deviceDataModel->getCollection()->addFieldToFilter('device_token', array('eq' => $deviceToken));
                    if (!empty($collections->getData())) {
                        foreach ($collections as $user) {
                            $device_token = $user->getDeviceToken();
                            if ($device_token == $deviceToken) {
                                try {
                                    $deviceModelData = $this->_deviceDataModel->load($user->getId());
                                    $deviceModelData->setIsLogout(1);
                                    $deviceModelData->save();

                                    if ($session->isLoggedIn()) {
                                        $session->logout();
                                    }
                                    $responseArr['status'] = 'true';
                                    $responseArr['message'] = 'You have successfuly logged out.';
                                    $jsonResult->setData($responseArr);
                                    return $jsonResult;
                                } catch (\Exception $e) {
                                    $responseArr['status'] = 'false';
                                    $responseArr['message'] = $e->getMessage();
                                    $jsonResult->setData($responseArr);
                                    return $jsonResult;
                                }
                            } else {
                                $responseArr['status'] = 'false';
                                $responseArr['message'] = 'Device token does not match';
                                $jsonResult->setData($responseArr);
                                return $jsonResult;
                            }
                        }
                    } else {
                        $responseArr['status'] = 'false';
                        $responseArr['message'] = 'Customer not found';
                        $jsonResult->setData($responseArr);
                        return $jsonResult;
                    }
                } else {
                    $responseArr['status'] = 'false';
                    $responseArr['message'] = 'Device token should not blank';
                    $jsonResult->setData($responseArr);
                    return $jsonResult;
                }
            } else {
                $responseArr['status'] = 'false';
                $responseArr['message'] = 'Device token is missing';
                $jsonResult->setData($responseArr);
                return $jsonResult;
            }
        } else {
            $returnExtensionArray = array('enable' => false);
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
    }
}
