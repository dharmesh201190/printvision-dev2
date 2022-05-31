<?php
/**
 * Copyright © 2017-2018 AppJetty. All rights reserved.
 */

namespace Biztech\DesignTemplates\Model\Mysql4\Designtemplatecategory;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init('Biztech\DesignTemplates\Model\Designtemplatecategory', 'Biztech\DesignTemplates\Model\Mysql4\Designtemplatecategory');
        $this->_map['fields']['id'] = 'main_table.id';
        $this->_map['fields']['store'] = 'main_table.store_id';
    }

    public function setFirstStoreFlag($flag = false)
    {
        $this->_previewFlag = $flag;
        return $this;
    }

    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            if ($store instanceof Store) {
                $store = [$store->getId()];
            }
            if (!is_array($store)) {
                $store = [$store];
            }

            if ($withAdmin) {
                $store[] = Store::DEFAULT_STORE_ID;
            }

            $set = array();
            if (is_array($store)) {
                foreach ($store as $key => $value) {
                    $set[] = array(
                        'finset' => $value
                    );
                }
            }
            $this->addFieldToFilter('store_id', array($set));
        }
        return $this;
    }

    protected function _afterLoad()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $items = $this->getData();
        if (count($items)) {
            foreach ($this as $item) {
                if ($item->getData('store_id') == 0) {
                    $storeId = $item->getData('store_id');
                    $storeCode = $storeManager->getStore($storeId)->getCode();
                    $item->setStoreId(array(0 => '0'));
                } else {
                    $storeId = explode(',', $item->getData('store_id'));
                    $storeCode = $storeManager->getStore($storeId[0])->getCode();
                    if (is_array($storeId)) {
                        $item->setStoreId($storeId);
                    } else {
                        $item->setStoreId($storeId[0]);
                    }
                }
                $item->setData('_first_store_id', $storeId);
                $item->setData('store_code', $storeCode);
            }
        }
        return parent::_afterLoad();
    }
}
