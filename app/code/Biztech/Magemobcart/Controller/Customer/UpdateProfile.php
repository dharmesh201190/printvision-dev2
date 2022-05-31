<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Customer;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;

class UpdateProfile extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;
    protected $cartHelper;
    protected $request;
    protected $customerSession;
    protected $customerModel;
    protected $newsletterModel;
    protected $formKey;

    /**
     * @param Context                              $context
     * @param JsonFactory                          $jsonFactory
     * @param \Biztech\Magemobcart\Helper\Data     $cartHelper
     * @param \Magento\Framework\App\Request\Http  $request
     * @param \Magento\Customer\Model\Session      $customerSession
     * @param \Magento\Customer\Model\Customer     $customerModel
     * @param \Magento\Newsletter\Model\Subscriber $newsletterModel
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Customer $customerModel,
        \Magento\Newsletter\Model\Subscriber $newsletterModel,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_cartHelper = $cartHelper;
        $this->_request = $request;
        $this->_customerSession = $customerSession;
        $this->_customerModel = $customerModel;
        $this->_newsletterModel = $newsletterModel;
        $this->formKey = $formKey;
        $this->_request->setParam('form_key', $this->formKey->getFormKey());
        parent::__construct($context);
    }

    /**
     * This function is used for update customer profile data.
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
            $sessionId = '';
            if (isset($postData['session_id']) && $postData['session_id'] != null) {
                $sessionId = $postData['session_id'];
                if (!$this->_customerSession->isLoggedIn()) {
                    $customer_id = explode("_", $sessionId);
                    $this->_cartHelper->relogin($customer_id[0]);
                }
            }
            
            $customerArray = array();
            if (array_key_exists('customer_id', $postData)) {
                $customerId = $postData['customer_id'];
                if ($customerId != "") {
                    try {
                        $customer = $this->_customerModel->load($customerId);
                        if (!$customer->getId()) {
                            $customerArray = array(
                            'entity_id' => $customerId,
                            'status' => 'error',
                            'message' => 'No customer is associated with this id'
                            );
                            $jsonResult->setData($customerArray);
                            return $jsonResult;
                        }

                        foreach ($postData as $attributeCode => $attribute) {
                            if (isset($postData[$attributeCode]) && $attribute != '') {
                                $customer->setData($attributeCode, $attribute);
                            }
                        }

                        $customer->save();
                        if ($postData['is_subscribed'] == 1) {
                            $subscriber = $this->_newsletterModel->subscribe($postData['email']);
                        }
                        if ($postData['is_subscribed'] == 0) {
                            $unsubscriber = $this->_newsletterModel->loadByEmail($postData['email']);
                            if ($unsubscriber->getId()) {
                                $unsubscriber->unsubscribe();
                            }
                        }
                        $customerArray = array(
                        'entity_id' => $customer->getId(),
                        'status' => 'success',
                        'message' => 'Profile updated successfully'
                        );
                        $jsonResult->setData($customerArray);
                        return $jsonResult;
                    } catch (\Exception $e) {
                        $customerArray = array(
                        'status' => 'error',
                        'message' => $e->getMessage()
                        );
                        $jsonResult->setData($customerArray);
                        return $jsonResult;
                    }
                } else {
                    $customerArray = array(
                        'status' => 'error',
                        'message' => 'Customer should not blank'
                        );
                    $jsonResult->setData($customerArray);
                    return $jsonResult;
                }
            } else {
                $customerArray = array(
                        'status' => 'error',
                        'message' => 'Customer is missing'
                    );
                $jsonResult->setData($customerArray);
                return $jsonResult;
            }
        } else {
            $returnExtensionArray = array('enable' => false);
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
    }
}
