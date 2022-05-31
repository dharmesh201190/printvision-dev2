<?php
namespace Biztech\Productdesigner\Block\Adminhtml;

class Subtabs extends \Magento\Backend\Block\Widget\Grid\Container {

    protected $subTabsCollection;
    protected $_store;

    public function __construct(
         \Biztech\Productdesigner\Model\Mysql4\Subtabs\CollectionFactory $subTabsCollection, \Magento\Framework\App\Config\ScopeConfigInterface $store
    ) {
        $this->subTabsCollection = $subTabsCollection;
        $this->_store = $store;
    }
    protected function _construct() {
        $this->_controller = 'subtabs';
        $this->_headerText = __('Subtabs Information');
        $main_tabs_exist = [];
        $subtabCollection = $this->subTabsCollection->create();
        foreach ($subtabCollection as $subtab) {
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
            $this->_addButtonLabel = __('Add  Sub Tab');
            parent::_construct();
        }
    }

}
