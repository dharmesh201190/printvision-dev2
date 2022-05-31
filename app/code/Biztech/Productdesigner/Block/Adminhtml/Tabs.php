<?php

namespace Biztech\Productdesigner\Block\Adminhtml;

class Tabs extends \Magento\Backend\Block\Widget\Grid\Container {

    protected $tabsCollection;
    protected $_store;

    public function __construct(
         \Biztech\Productdesigner\Model\Mysql4\TabsData\CollectionFactory $tabsCollection, \Magento\Framework\App\Config\ScopeConfigInterface $store
    ) {
        $this->tabsCollection = $tabsCollection;
        $this->_store = $store;
    }
    protected function _construct() {
        $this->_controller = 'tabs';
        $this->_headerText = __('Tabs Information');
        $main_tabs_exist = [];
        $tabsCollection = $this->tabsCollection->create();
        foreach ($tabsCollection as $subtab) {
            $main_tabs_exist[] = $subtab->getMaintab();
        }
        $selected_main_tabs = $this->_store->getValue('productdesigner/layout_general/main_tab');
        $remainingMainTabs = $selectedMainTabs = explode(',', $selected_main_tabs);
        foreach ($selectedMainTabs as $key => $value) {
            if (in_array($value, $main_tabs_exist)) {
                unset($remainingMainTabs[$key]);
            }
        }
        if (count($remainingMainTabs) > 0) {
            parent::_construct();
            $this->buttonList->remove('add');
        }
    }

}
