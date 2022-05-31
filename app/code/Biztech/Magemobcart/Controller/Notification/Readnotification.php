<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Notification;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;

class Readnotification extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;
    protected $cartHelper;
    protected $request;
    protected $notificationHistoryModel;
    protected $formKey;

    /**
     * @param Context                                        $context
     * @param JsonFactory                                    $jsonFactory
     * @param \Biztech\Magemobcart\Helper\Data               $cartHelper
     * @param \Magento\Framework\App\Request\Http            $request
     * @param \Biztech\Magemobcart\Model\Notificationhistory $notificationHistoryModel
     * @param \Magento\Framework\Data\Form\FormKey           $formKey
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Magento\Framework\App\Request\Http $request,
        \Biztech\Magemobcart\Model\Notificationhistory $notificationHistoryModel,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_cartHelper = $cartHelper;
        $this->_request = $request;
        $this->_notificationHistoryModel = $notificationHistoryModel;
        $this->formKey = $formKey;
        $this->_request->setParam('form_key', $this->formKey->getFormKey());
        parent::__construct($context);
    }

    /**
     * This function is used for get all categories list with tree structure.
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
                if (array_key_exists('customer_id', $postData)) {
                    if (array_key_exists('notification_history_id', $postData)) {
                        $customerId = $postData['customer_id'];
                        $notificationHistoryId = $postData['notification_history_id'];
                        if (isset($notificationHistoryId)) {
                            try {
                                $setReadModel = $this->_notificationHistoryModel->load($notificationHistoryId);
                                if (empty($setReadModel->getData())) {
                                    $customerArray = array(
                                    'status' => 'error',
                                    'message' => 'Notification not found'
                                    );
                                    $jsonResult->setData($customerArray);
                                    return $jsonResult;
                                } else {
                                    $setReadModel->setIsRead(1);
                                    $setReadModel->save();
                                    $customerArray = array(
                                    'status' => 'true',
                                    );
                                    $jsonResult->setData($customerArray);
                                    return $jsonResult;
                                }
                            } catch (\Exception $e) {
                                $customerArray = array(
                                'status' => 'error',
                                'message' => $e->getMessage()
                                );
                                $jsonResult->setData($customerArray);
                                return $jsonResult;
                            }
                        }
                    }
                } else {
                    $customerArray = array(
                    'status' => 'error',
                    'message' => 'Customer Id is missing'
                    );
                    $jsonResult->setData($customerArray);
                    return $jsonResult;
                }
                return;
            } catch (\Exception $e) {
                $customerArray = array(
                'status' => 'error',
                'message' => $e->getMessage()
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
