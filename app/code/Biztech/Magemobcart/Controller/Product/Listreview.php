<?php

/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Biztech\Magemobcart\Controller\Product;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Action\Context;

class Listreview extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;
    protected $request;
    protected $cartHelper;
    protected $reviewFactory;
    protected $ratingFactory;
    protected $ratingVote;
    protected $formKey;

    /**
     * @param Context                                  $context
     * @param JsonFactory                              $jsonFactory
     * @param Http                                     $request
     * @param \Biztech\Magemobcart\Helper\Data         $cartHelper
     * @param \Magento\Review\Model\ReviewFactory      $reviewFactory
     * @param \Magento\Review\Model\RatingFactory      $ratingFactory
     * @param \Magento\Review\Model\Rating\Option\Vote $ratingVote
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        Http $request,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magento\Review\Model\RatingFactory $ratingFactory,
        \Magento\Review\Model\Rating\Option\Vote $ratingVote,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->_request = $request;
        $this->_jsonFactory = $jsonFactory;
        $this->_cartHelper = $cartHelper;
        $this->_reviewFactory = $reviewFactory;
        $this->_ratingFactory = $ratingFactory;
        $this->_ratingVote = $ratingVote;
        $this->formKey = $formKey;
        $this->_request->setParam('form_key', $this->formKey->getFormKey());
        parent::__construct($context);
    }

    /**
     * This function is used for get all review list.
     * @return Magento\Framework\Controller\Result\JsonFactory
     */
    public function execute()
    {
        $jsonResult = $this->_jsonFactory->create();
        if ($this->_cartHelper->isEnable()) {
            if (!$this->_cartHelper->getHeaders()) {
                $errorResult = array('status' => false, 'message' => $this->_cartHelper->getHeaderMessage());
                $jsonResult->setData($errorResult);
                return $jsonResult;
            }
            $postData = $this->_request->getParams();
            $limit = $postData['limit'];
            $page = $postData['page'] ? $postData['page'] : 1;
            $productId = $postData['productid'];
            $storeId = $postData['storeid'];
            $filterBy = $postData['filter_by'];
            try {
                $reviewFactory = $this->_reviewFactory->create();
                $ratingFactory = $this->_ratingFactory->create();
                $reviewsCollection = $reviewFactory->getCollection()
                    ->addStoreFilter($storeId)
                    ->addStatusFilter('Magento\Review\Model\Review'::STATUS_APPROVED)
                    ->addEntityFilter('product', $productId)->setDateOrder();
                $i = 0;
                $ratingsValue = array();
                $ratingResponse = array();
                $posCount = array();
                $negCount = array();
                $ratingResponseAll = array();
                foreach ($reviewsCollection->getItems() as $reviewData) {
                    $ratingResponse[$i]['review'] = array(
                        'review_id' => $reviewData->getId(), 'created_at' => $reviewData->getCreatedAt(),
                        'title' => $reviewData->getTitle(), 'detail' => $reviewData->getDetail(), 'nickname' => $reviewData->getNickname()
                    );

                    $votesCollection = $this->_ratingVote->getResourceCollection()->setReviewFilter($reviewData->getReviewId())
                        ->addOptionInfo()->load()->addRatingOptions();

                    if (count($votesCollection) > 0) {
                        foreach ($votesCollection as $vote) {
                            $ratings[] = $vote->getPercent();
                            $ratingsValue[] = $vote->getPercent();
                            $ratingCodes = $ratingFactory->getCollection()->addFieldToFilter('rating_id', $vote->getRatingId())->getFirstItem();
                            $ratingResponse[$i]['rating_details'][] = array(
                                'rating_code' => $ratingCodes->getRatingCode(),
                                'option_value' => $vote->getValue()
                            );

                            $ratingResponse[$i]['average_rating'] = round(array_sum($ratings) / count($ratings));
                        }
                        $ratings = array();
                    }
                    $i++;
                }

                $stars = array();
                $stars = $ratingsValue;
                $star1 = array();
                $star2 = array();
                $star3 = array();
                $star4 = array();
                $star5 = array();
                foreach ($stars as $star) {
                    if ($star > 80) {
                        $star5[] = $star;
                    } elseif ($star > 60 && $star <= 80) {
                        $star4[] = $star;
                    } elseif ($star > 40 && $star <= 60) {
                        $star3[] = $star;
                    } elseif ($star > 20 && $star <= 40) {
                        $star2[] = $star;
                    } else {
                        $star1[] = $star;
                    }
                }
                $reviewStar = array(
                    '1' => count($star1),
                    '2' => count($star2),
                    '3' => count($star3),
                    '4' => count($star4),
                    '5' => count($star5)
                );

                foreach ($ratingResponse as $key => $value) {
                    if ($value['average_rating'] >= 60) {
                        $posCount[] = $value;
                    }

                    if ($value['average_rating'] <= 60) {
                        $negCount[] = $value;
                    }
                }
                $i = 0;
                $posetiveResponse = array();
                foreach ($ratingResponse as $key => $value) {
                    if ($value['average_rating'] >= 60) {
                        $i++;
                        if ($page == 1) {
                            $posetiveResponse[] = $value;
                            if ($i == $limit) {
                                break;
                            }
                        } elseif ($page != 1 && $i >= ((($page - 1) * $limit) + 1)) {
                            $posetiveResponse[] = $value;
                            if ($i == (($page - 1) * $limit) + $limit) {
                                break;
                            }
                        }
                    }
                }

                $i = 0;
                $negativeResponse = [];
                foreach ($ratingResponse as $key => $value) {
                    if ($value['average_rating'] < 60) {
                        $i++;
                        if ($page == 1) {
                            $negativeResponse[] = $value;
                            if ($i == $limit) {
                                break;
                            }
                        } elseif ($page != 1 && $i >= ((($page - 1) * $limit) + 1)) {
                            $negativeResponse[] = $value;
                            if ($i == (($page - 1) * $limit) + $limit) {
                                break;
                            }
                        }
                    }
                }
                $i = 0;
                foreach ($ratingResponse as $key => $value) {
                    $i++;
                    if ($page == 1) {
                        $ratingResponseAll[] = $value;
                        if ($i == $limit) {
                            break;
                        }
                    } elseif ($page != 1 && $i >= ((($page - 1) * $limit) + 1)) {
                        $ratingResponseAll[] = $value;
                        if ($i == (($page - 1) * $limit) + $limit) {
                            break;
                        }
                    }
                }

                if ($page + 1 <= ceil(count($posCount) / $limit)) {
                    $posCountNextPage = $page + 1;
                } else {
                    $posCountNextPage = "";
                }
                if ($page + 1 <= ceil(count($negCount) / $limit)) {
                    $negCountNextPage = $page + 1;
                } else {
                    $negCountNextPage = "";
                }
                if ($page + 1 <= ceil(count($ratingResponse) / $limit)) {
                    $allCountNextPage = $page + 1;
                } else {
                    $allCountNextPage = "";
                }
                if ($filterBy === 'all') {
                    if ($page == 1) {
                        $reviewResponse = array('totalReviews' => count($ratingResponse), 'reviewsCollection' => $ratingResponseAll, 'star_wise_review' => $reviewStar, 'current_page' => $page, 'next_page' => $allCountNextPage);
                    } else {
                        $reviewResponse = array('totalReviews' => count($ratingResponse), 'reviewsCollection' => $ratingResponseAll, 'current_page' => $page, 'next_page' => $allCountNextPage);
                    }
                }
                if ($filterBy === 'pos') {
                    $reviewResponse = array('totalReviews' => count($posCount), 'reviewsCollection' => $posetiveResponse, 'current_page' => $page, 'next_page' => $posCountNextPage);
                }
                if ($filterBy === 'neg') {
                    $reviewResponse = array('totalReviews' => count($negCount), 'reviewsCollection' => $negativeResponse, 'current_page' => $page, 'next_page' => $negCountNextPage);
                }
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
