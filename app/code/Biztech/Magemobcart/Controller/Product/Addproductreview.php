<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Product;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Action\Context;

class Addproductreview extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;
    protected $request;
    protected $storeManager;
    protected $cartHelper;
    protected $reviewFactory;
    protected $ratingFactory;
    protected $formKey;
    /**
     * @param Context                                    $context
     * @param JsonFactory                                $jsonFactory
     * @param Http                                       $request
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Category            $categoryModel
     * @param \Biztech\Magemobcart\Helper\Data           $cartHelper
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        Http $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magento\Review\Model\RatingFactory $ratingFactory,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->_request = $request;
        $this->_jsonFactory = $jsonFactory;
        $this->_storeManager = $storeManager;
        $this->_cartHelper = $cartHelper;
        $this->_reviewFactory = $reviewFactory;
        $this->_ratingFactory = $ratingFactory;
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
            $storeId = $postData['storeid'];
            try {
                $productId = $postData['id'];
                $ratingsData = json_decode($postData['ratings'], true);
                
                foreach ($ratingsData as $key => $value) {
                    $reviewFinalData['ratings'][$key] = $value;
                    $reviewFinalData['ratings'][$key] = $value;
                    $reviewFinalData['ratings'][$key] = $value;
                }
                
                $reviewFinalData['customer_id'] = $postData['customer_id'];
                $reviewFinalData['nickname'] = $postData['nickname'];
                $reviewFinalData['title'] = $postData['title'];
                $reviewFinalData['detail'] = $postData['detail'];
                $review = $this->_reviewFactory->create()->setData($reviewFinalData);
                $review->unsetData('review_id');
                $review->setEntityId($review->getEntityIdByCode(\Magento\Review\Model\Review::ENTITY_PRODUCT_CODE))
                ->setEntityPkValue($productId)
                ->setStatusId(\Magento\Review\Model\Review::STATUS_PENDING)
                ->setStoreId($this->_storeManager->getStore()->getId())
                ->setStores([$this->_storeManager->getStore()->getId()])
                ->save();

                foreach ($reviewFinalData['ratings'] as $ratingId => $optionId) {
                    $this->_ratingFactory->create()
                    ->setRatingId($ratingId)
                    ->setReviewId($review->getId())
                    ->addOptionVote($optionId, $productId);
                }
                $review->aggregate();
                $reviewResponse['status'] = 'true';
                $reviewResponse['message'] = 'Your review has been accepted for moderation.';
            } catch (\Exception $e) {
                $reviewResponse = array(
                    'status' => 'false',
                    'message' => $e->getMessage()
                );
            }
            $jsonResult->setData($reviewResponse);
            return $jsonResult;
        } else {
            $returnExtensionArray = array('enable' => false);
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
    }
}
