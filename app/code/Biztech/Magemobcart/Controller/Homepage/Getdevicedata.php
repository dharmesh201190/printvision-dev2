<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Homepage;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Action\Context;

class Getdevicedata extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;
    protected $request;
    protected $cartHelper;
    protected $deviceDataModel;
    protected $formKey;

    /**
     * @param Context                               $context
     * @param JsonFactory                           $jsonFactory
     * @param Http                                  $request
     * @param \Biztech\Magemobcart\Helper\Data      $cartHelper
     * @param \Biztech\Magemobcart\Model\Devicedata $deviceDataModel
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        Http $request,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Biztech\Magemobcart\Model\Devicedata $deviceDataModel,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->_request = $request;
        $this->_jsonFactory = $jsonFactory;
        $this->_cartHelper = $cartHelper;
        $this->_deviceDataModel = $deviceDataModel;
        $this->formKey = $formKey;
        $this->_request->setParam('form_key', $this->formKey->getFormKey());
        parent::__construct($context);
    }
    /**
     * This function is used for register the device information.
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
            $model = $this->_deviceDataModel;
            if (array_key_exists('device_id', $postData) && array_key_exists('device_token', $postData)) {
                try {
                    $device = $model->getCollection();
                    $existData = $device->addFieldToFilter('device_token', $postData['device_token'])
                            ->addFieldToFilter('device_id', $postData['device_id'])->getData();

                    if (count($existData) >= 1) {
                        $deviceResponse = array(
                        'status' => 'true',
                        'message' => 'Device details already exists.'
                        );
                    } else {
                        $singleModel = $this->_deviceDataModel;
                        $singleModel->setData($postData);
                        $singleModel->save();
                        // }
                        $deviceResponse = array(
                        'status' => 'true',
                        'message' => 'Device details successfully added.'
                        );
                    }
                } catch (\Exception $e) {
                    $deviceResponse = array(
                    'status' => 'false',
                    'message' => $e->getMessage()
                    );
                }
            } else {
                $deviceResponse = array(
                'status' => 'false',
                'message' => 'Data Not found.'
                );
            }
            $jsonResult->setData($deviceResponse);
            return $jsonResult;
        } else {
            $returnExtensionArray = array('enable' => false);
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
    }
}
