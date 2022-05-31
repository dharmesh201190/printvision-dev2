<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Product;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Action\Context;

class Getavailrating extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;
    protected $request;
    protected $storeManager;
    protected $cartHelper;
    protected $ratingModel;
    protected $formKey;
    
    /**
     * @param Context                             $context
     * @param JsonFactory                         $jsonFactory
     * @param Http                                $request
     * @param \Biztech\Magemobcart\Helper\Data    $cartHelper
     * @param \Magento\Review\Model\RatingFactory $ratingModel
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        Http $request,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Magento\Review\Model\RatingFactory $ratingModel,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->_request = $request;
        $this->_jsonFactory = $jsonFactory;
        $this->_cartHelper = $cartHelper;
        $this->_ratingModel = $ratingModel;
        $this->formKey = $formKey;
        $this->_request->setParam('form_key', $this->formKey->getFormKey());
        parent::__construct($context);
    }

    /**
     * This function is used for get all available ratings for the product review and ratings.
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
            
            if (array_key_exists('storeid', $postData)) {
                $storeId = $postData['storeid'];
            } else {
                $ratingResponse = array(
                    'status' => 'false',
                    'message' => 'store id is missing'
                );
                $jsonResult->setData($ratingResponse);
                return $jsonResult;
            }
            try {
                $ratingCollection = $this->_ratingModel->create()->getResourceCollection()->addEntityFilter('product')
                                ->setPositionOrder()->addRatingPerStoreName($storeId)
                                ->setStoreFilter($storeId)->load()->addOptionToItems();
                $ratingResponse = array();
                $i = 0;
                foreach ($ratingCollection as $ratingData) {
                    $ratingResponse[$i] = array('rating_id' => $ratingData->getId(), 'rating_code' => $ratingData->getRatingCode());
                    foreach ($ratingData->getOptions() as $ratingOption) {
                        $ratingResponse[$i]['rating_options'][] = array('option_id' => $ratingOption->getId(), 'option_value' => $ratingOption->getValue());
                    }
                    $i++;
                }
            } catch (\Exception $e) {
                $ratingResponse = array(
                    'status' => 'false',
                    'message' => $e->getMessage()
                );
            }
            $jsonResult->setData($ratingResponse);
            return $jsonResult;
        } else {
            $returnExtensionArray = array('enable' => false);
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
    }
}
