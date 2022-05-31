<?php

namespace Biztech\BackgroundPatterns\Observer;

class ClipartCategoriesFetchedAfter implements \Magento\Framework\Event\ObserverInterface {

    protected $_eventManager;
    protected $_scopeConfig;
    protected $_clipartModel;
    protected $_clipartFactory;
    protected $_finalArr;

    public function __construct(
        \Magento\Framework\Event\Manager $manager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Biztech\Productdesigner\Model\Clipart $clipartModel,
        \Biztech\Productdesigner\Model\Mysql4\Clipart\CollectionFactory $clipartFactory
    ) {
        $this->_eventManager = $manager;
        $this->_scopeConfig = $scopeConfig;
        $this->_clipartModel = $clipartModel;
        $this->_clipartFactory = $clipartFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        // init data
        $type = $observer->getData('type');
        $eventResponse = $observer->getData('clipartCollection');
        $clipartCategories = $eventResponse->getData();
        $checkForPattern = (isset($type) && $type == 'pattern') ? 1 : 0;

        // set array as empty
        $this->_finalArr = array();

        // 
        foreach ($clipartCategories as $key => $clipartItem) {

            // 
            foreach ($clipartItem->getData() as $key => $value) {
                $clipart = $this->_clipartModel->load($value['clipart_id']);
                if($clipart->getIsPattern() == $checkForPattern) {
                    $value['isPattern'] = $checkForPattern;
                    array_push($this->_finalArr, array('id' => $value['clipart_id'], 'name' => $value['clipart_title'], 'level' => $value['level'], 'isPattern' => $value['isPattern'], 'parent' => $value['parent_categories']));
                    if($checkForPattern == 1) {
                        $this->pushAllChild($value, false, true);
                    } else {                        
                        $this->pushAllChild($value, $checkForPattern);
                    }
                }
                else if($checkForPattern == 1) {
                    $this->pushAllChild($value, $checkForPattern);
                }
            }
        }
        $eventResponse->setClipartCategories($this->_finalArr);
    }

    public function pushAllChild($clipart, $checkForPattern = false, $skipRecheck = false) {
        $child = $this->_clipartFactory->create()->addFieldToFilter('parent_categories', array('eq' => $clipart['clipart_id']));
        foreach ($child as $key => $value) {
            if($checkForPattern === false) {

                $tmp = array('id' => $value['clipart_id'], 'name' => $value['clipart_title'], 'level' => $value['level'], 'isPattern' => $value['is_pattern'], 'parent' => $value['parent_categories']);            
                array_push($this->_finalArr, $tmp);
                if($value['level'] == '1') {
                    $this->pushAllChild($value);
                }
            } else if($checkForPattern !== false && $value['is_pattern'] == $checkForPattern) {

                $tmp = array('id' => $value['clipart_id'], 'name' => $value['clipart_title'], 'level' => $value['level'], 'isPattern' => $value['is_pattern'], 'parent' => $value['parent_categories']);
                array_push($this->_finalArr, $tmp);
                if($value['level'] == '1' && $checkForPattern == 1) {
                    $this->pushAllChild($value, false, true);
                } else {
                    $this->pushAllChild($value, $checkForPattern);    
                }
            } else {
                if($checkForPattern == 1)
                    $this->pushAllChild($value, $checkForPattern);
            }
        }
    }

    public function checkForChild($clipart, $checkForPattern) {

    }
}