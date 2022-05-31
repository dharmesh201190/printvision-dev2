<?php

namespace Biztech\TabsManagement\Observer;

use Magento\Framework\Event\ObserverInterface;

class getFinalTabs implements ObserverInterface {

    protected $_scopeConfig;
    protected $tabsFactory;

    public function __construct(
    \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Biztech\Productdesigner\Model\TabsDataFactory $tabsFactory
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->tabsFactory = $tabsFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $tabsOrder = $this->_scopeConfig->getValue('productdesigner/layout_general/main_tab', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $tabsarray = explode(',', $tabsOrder);
        $model = $this->tabsFactory->create();
        $data = $model->getCollection();
        $counter = 0;
        foreach ($data as $id) {
            $tabid = $id->getId();
            if (in_array($tabid, $tabsarray)) {
                $model->load($tabid);
                $sort = array_search($tabid, $tabsarray);
                $model->setSortOrder($sort);
                $model->save();
            } else {
                $model->load($tabid);
                $model->setSortOrder(count($tabsarray) + $counter++);
                $model->save();
            }
        }
    }

}
