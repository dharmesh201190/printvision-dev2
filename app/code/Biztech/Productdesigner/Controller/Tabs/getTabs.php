<?php

/**
 * Copyright © 2017-2018 AppJetty. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Biztech\Productdesigner\Controller\Tabs;


class getTabs extends \Biztech\Productdesigner\Controller\Tabs {

    /**
     *
     * @var type 
     */
    const Identifier = 'getMainTabs';

    protected $storeid;

    public function execute() {
        try {
            $data = json_decode(file_get_contents('php://input'), TRUE);
            $isPro = (isset($data['isPro'])) ? $data['isPro'] : 1;
            $productId = $data['product_id'];
            $isAdmin = isset($data['isAdmin']) ? $data['isAdmin'] : 0;
            $cacheKey = self::Identifier . $productId; /* Create key for unique cache identifier */
            $response = $this->_infoHelper->loadCache($cacheKey);
            $this->storeid = $this->_storeManager->getStore()->getId();
            // if (!$response) {
            $getTabsData = $this->getTabsData($productId);
            $getTabsData['product_id'] = $productId;

            if($isPro != 0) {

                $tabsObject = $this->_objectFactory->create();
                $tabsObject->setTabsData($getTabsData);
                $this->_eventManager->dispatch('tabsData', ['tabsObject' => $tabsObject]);
                $getTabsData = $tabsObject->getTabsData();
            }

            $response['allTabsData'] = $getTabsData['allTabsData'];
            $response['tabsData'] = $getTabsData['tempArray'];
            $finalTabsFlow = array();
                /* if($getTabsData['productLevelTabs'] ==  true){
                  foreach ($getTabsData['tempArray'] as $key => $value) {
                  $finalTabsFlow[$value] = array($value);
                  }
              }else{ */
                foreach ($getTabsData['tempArray'] as $key => $value) {
                    $finalTabsFlow[$value] = $this->getSubTabsData($value, $isAdmin);
                }
                /* } */
                $subTabsData = array();
                $subTabsData['subtabs'] = $finalTabsFlow;
                $subTabsData['product_id'] = $productId;
                $subTabsData['allTabsData'] = $getTabsData['allTabsData'];

                if($isPro != 0) {
                    $subTabsObject = $this->_objectFactory->create();
                    $subTabsObject->setSubTabsObjectData($subTabsData);
                    $this->_eventManager->dispatch('subTabsData', ['subTabsObject' => $subTabsObject]);
                    if ($subTabsObject->getActualSubTabsObjectData()) {
                        $finalTabsFlow = $subTabsObject->getActualSubTabsObjectData();
                    }
                }
                $response['subTabsData'] = $finalTabsFlow;
                $isTooltipEnable = $this->_pdHelper->getConfig('productdesigner/general/enable_tooltip', $this->storeid);
                $response['productTooltipData'] = "";
                $response['tooltipStatus'] = $isTooltipEnable;
                if ($isTooltipEnable == 1) {
                    $all_product = $this->_pdHelper->getConfig('productdesigner/enable_allproducts/enable', $this->storeid);
                    if ($all_product == 1) {
                        $first_product_tooltip = $this->_pdHelper->getConfig('productdesigner/enable_allproducts/product_tooltip_second', $this->storeid);
                        $second_product_tooltip = $this->_pdHelper->getConfig('productdesigner/enable_allproducts/product_tooltip_first', $this->storeid);

                        $response['productTooltipData'] = array($first_product_tooltip, $second_product_tooltip, $all_product);
                    }
                }

                $this->_infoHelper->setCache($response, $cacheKey);
            // }
                $this->getResponse()->setBody(json_encode($response));
            } catch (\Exception $e) {
                $response = $this->_infoHelper->throwException($e, self::class);
                $this->getResponse()->setBody(json_encode($response));
            }
        }

        public function getTabsData($productId = "") {
            $model = $this->_maintabsdata->create();
            $tabs_collection = $model->getData();
            $payload = json_decode(file_get_contents('php://input'), TRUE);
            $isPro = (isset($payload['isPro'])) ? $payload['isPro'] : 1;
            $allTabsData = array();
            $this->storeid = $this->_storeManager->getStore()->getId();
            $tabsarray = array();
            /* $productLevelTabs = false; */
            if (!empty($productId)) {
                $product = $this->_productLoader->create()->load($productId);
                $tabsId = $product->getMainTabs();
                if ($tabsId) {
                    $tabsarray = explode(',', $tabsId);
                    /* $productLevelTabs = true; */
                }
            }
            if (sizeof($tabsarray) <= 0) {
                $configTabs = $this->_pdHelper->getConfig('productdesigner/layout_general/main_tab', $this->storeid);
                $tabsarray = explode(',', $configTabs);
            }
            foreach ($tabs_collection as $value) {
                $allTabsData[$value['id']] = $value;
            }

            $tempArray = array_flip($tabsarray);
            foreach($allTabsData as $key => $value) {
                if(isset($value['isPro'])) {
                    if($isPro == 0 && $value['isPro'] != 0) {
                        $index = array_search($value['id'], $tabsarray);
                        // unset($tabsarray[$index]);
                        if(gettype($index) == 'integer' && $index >= 0){
                            array_splice($tabsarray, $index, 1);
                        }
                        unset($allTabsData[$key]);
                    } else if($isPro == 1 && $value['isPro'] == 2) {
                        $index = array_search($value['id'], $tabsarray);
                        // unset($tabsarray[$index]);
                        if(gettype($index) == 'integer' && $index >= 0){
                            array_splice($tabsarray, $index, 1);
                        }
                        unset($allTabsData[$key]);
                    }
                }
            }
            return array('allTabsData' => $allTabsData, 'tempArray' => $tabsarray);
        }

        public function getSubTabsData($maintabId, $isAdmin = false) {
            $subTabModel = $this->_subtabsdata->create()->addFieldToFilter('maintab', $maintabId)->getFirstItem()->getData();
            $payload = json_decode(file_get_contents('php://input'), TRUE);
            $isPro = (isset($payload['isPro'])) ? $payload['isPro'] : 1;

            $subTabarray = array();
            if (sizeof($subTabModel) > 0) {
                $subTabarray = explode(',', $subTabModel['subtabs']);

            // Remove Sub Tabs For Admin where no need of that
                if ($isAdmin) {
                    foreach ($subTabarray as $key => $value) {
                        $checkIsAdmin = $this->_maintabsdata->create()->addFieldToFilter('id', $value)->getFirstItem()->getData();
                        if ($checkIsAdmin['is_admin'] == 0) {
                            unset($subTabarray[$key]);
                        }
                        if(isset($value['isPro'])) {
                            if($isPro == 0 && $value['isPro'] != 0) {
                                unset($subTabarray[$key]);
                            } else if($isPro == 1 && $value['isPro'] == 2) {
                                unset($subTabarray[$key]);
                            }
                        }
                    }
                }else{
                   foreach ($subTabarray as $key => $value) {
                     $value = $this->_maintabsdata->create()->addFieldToFilter('id', $value)->getFirstItem()->getData();
                     if(isset($value['isPro'])) {
                        if($isPro == 0 && $value['isPro'] != 0) {
                            unset($subTabarray[$key]);
                        } else if($isPro == 1 && $value['isPro'] == 2) {
                            unset($subTabarray[$key]);
                        }
                    }
                }
            }
        }
        $subTabsData = array();
        array_push($subTabsData, $maintabId);
        $subTabsData = array_merge($subTabsData, $subTabarray);
        return $subTabsData;
    }
}
