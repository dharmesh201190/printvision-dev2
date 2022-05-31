<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Customer;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;

class Getavailableregion extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;
    protected $cartHelper;
    protected $request;
    protected $customerSession;
    protected $regionModel;
    protected $formKey;

    /**
     * @param Context                             $context
     * @param JsonFactory                         $jsonFactory
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Biztech\Magemobcart\Helper\Data    $cartHelper
     * @param \Magento\Customer\Model\Session     $customerSession
     * @param \Magento\Directory\Model\Region     $regionModel
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        \Magento\Framework\App\Request\Http $request,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Directory\Model\Region $regionModel,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_cartHelper = $cartHelper;
        $this->_request = $request;
        $this->_customerSession = $customerSession;
        $this->_regionModel = $regionModel;
        $this->formKey = $formKey;
        $this->_request->setParam('form_key', $this->formKey->getFormKey());
        parent::__construct($context);
    }

    /**
     * This function is used for get available regions of given country.
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
            $sessionId = '';
            $postData = $this->_request->getParams();
            if (isset($postData['session_id']) && $postData['session_id'] != null) {
                $sessionId = $postData['session_id'];
                if (!$this->_customerSession->isLoggedIn()) {
                    $customer_id = explode("_", $sessionId);
                    $this->_cartHelper->relogin($customer_id[0]);
                }
            }
            
            if (array_key_exists('country_id', $postData)) {
                $countryId = $postData['country_id'];
                $regionsArray = array();
                if ($countryId != "") {
                    $regions = $this->_regionModel->getResourceCollection()->addCountryFilter($countryId)->load();
                    foreach ($regions as $region) {
                        $regionsArray[] = array(
                        'region_id' => $region->getRegionId(),
                        'country_id' => $region->getCountryId(),
                        'code' => $region->getCode(),
                        'name' => $region->getName(),
                        );
                    }
                    $regionsJson = array('regions' => $regionsArray);
                    $jsonResult->setData($regionsJson);
                    return $jsonResult;
                } else {
                    $regionsJson = array('status' => 'false', 'message' => "Country id should not blank");
                    $jsonResult->setData($regionsJson);
                    return $jsonResult;
                }
            } else {
                $regionsJson = array('status' => 'false', 'message' => "Country id is missing");
                $jsonResult->setData($regionsJson);
                return $jsonResult;
            }
        } else {
            $returnExtensionArray = array('enable' => false);
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
    }
}
