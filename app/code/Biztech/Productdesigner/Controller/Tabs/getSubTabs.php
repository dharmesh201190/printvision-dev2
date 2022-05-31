<?php

namespace Biztech\Productdesigner\Controller\Tabs;

class getSubTabs extends \Biztech\Productdesigner\Controller\Tabs {

    const Identifier = 'getSubTabs';

    public function execute() {
        try {
            $data = json_decode(file_get_contents('php://input'), TRUE);
            $productId = isset($data['product_id']) ? $data['product_id'] : 0;
            $isAdmin = isset($data['isAdmin']) ? $data['isAdmin'] : 0;
            $cacheKey = self::Identifier . $productId . '-' . $data['data']; /* Create key for unique cache identifier */
            $response = $this->_infoHelper->loadCache($cacheKey);
            if (!$response) {
                $subTabsData = $this->getSubTabsData($data['data'],$isAdmin);
                $response['subTabsData'] = $subTabsData;
                $this->_infoHelper->setCache($response, $cacheKey);
            }
            $this->getResponse()->setBody(json_encode($response));
        } catch (\Exception $e) {
            $response = $this->_infoHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }
    }

    public function getSubTabsData($maintabId,$isAdmin = false){
        $subTabModel = $this->_subtabsdata->create()->addFieldToFilter('maintab', $maintabId)->getFirstItem()->getData();

        $subTabarray = array();
        if (sizeof($subTabModel) > 0) {
            $subTabarray = explode(',', $subTabModel['subtabs']);

            // Remove Sub Tabs For Admin where no need of that
            if($isAdmin){
                foreach ($subTabarray as $key => $value) {
                    $checkIsAdmin = $this->_maintabsdata->create()->addFieldToFilter('id',$value)->getFirstItem()->getData();
                    if($checkIsAdmin['is_admin'] == 0){
                        unset($subTabarray[$key]);
                    }
                }
            }
            
        }
        $subTabsData = array();
        array_push($subTabsData, $maintabId);
        $subTabsData = array_merge($subTabsData,$subTabarray); 
        return $subTabsData;
    }

}
