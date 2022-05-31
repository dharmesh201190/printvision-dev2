<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Notification;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;

class Removenotification extends \Magento\Framework\App\Action\Action
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
                if (array_key_exists('notification_history_id', $postData)) {
                    $historyId = $postData['notification_history_id'];
                    $customerId = $postData['customer_id'];
                    if ($historyId == "all") {
                        $notificationHistoryData = $this->_notificationHistoryModel->getCollection();
                        $notificationHistoryData->addFieldToFilter('customer_id', $customerId);
                        if (!empty($notificationHistoryData->getData())) {
                            foreach ($notificationHistoryData->getData() as $key => $value) {
                                $setReadModel = $this->_notificationHistoryModel->load($value['notification_history_id'])->delete();
                            }
                            $notification_array = array(
                            'status' => 'success',
                            'message' => 'All Notification removed successfully'
                            );
                            $jsonResult->setData($notification_array);
                            return $jsonResult;
                        } else {
                            $notification_array = array(
                            'status' => 'error',
                            'message' => 'Notification not found'
                            );
                            $jsonResult->setData($notification_array);
                            return $jsonResult;
                        }
                    }
                    $notificationHistoryData = $this->_notificationHistoryModel->getCollection();
                    $notificationHistoryData->addFieldToFilter('customer_id', $customerId);
                    $notificationHistoryData->addFieldToFilter('notification_history_id', $historyId);
                    if (!empty($notificationHistoryData->getData())) {
                        $setReadModel = $this->_notificationHistoryModel->load($historyId)->delete();
                    } else {
                        $notification_array = array(
                        'status' => 'error',
                        'message' => 'Notification not found'
                        );
                        $jsonResult->setData($notification_array);
                        return $jsonResult;
                    }
                    $notification_array = array(
                    'status' => 'success',
                    'message' => 'Notification removed successfully'
                    );
                    $jsonResult->setData($notification_array);
                    return $jsonResult;
                }
            } catch (\Exception $e) {
                $notification_array = array(
                'status' => 'error',
                'message' => $e->getMessage()
                );
                $jsonResult->setData($notification_array);
                return $jsonResult;
            }
        } else {
            $returnExtensionArray = array('enable' => false);
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
    }
}
