<?php

namespace Biztech\TabsManagement\Observer;

use Magento\Framework\Event\ObserverInterface;

class CheckMainTab implements ObserverInterface {

    protected $_subtabsCollection;
    protected $_urlInterface;
    protected $_messageManager;

    public function __construct(
        \Biztech\Productdesigner\Model\Mysql4\Subtabs\Collection $subtabsCollection,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->_subtabsCollection = $subtabsCollection;
        $this->_urlInterface = $urlInterface;
        $this->_messageManager = $messageManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $subtabs = [];
        $parentTab = [];
        $_product = $observer->getProduct();
        $maintabs = $_product->getMainTabs();

        if($maintabs && is_array($maintabs)){       
            foreach ($this->_subtabsCollection as $subtab) {
                $subtabs[] = $subtab->getData('subtabs');
                $parentTab[] = $subtab->getData('maintab');
            }
            $subtabsString = implode(",", $subtabs);
            $subtabsArray = explode(",", $subtabsString);
            foreach ($subtabsArray as $value) {
                if (in_array($value, $maintabs)) {
                    throw new \Exception("Some of the main tabs are already exist in sub tabs");
                }
            }
            $subtabsParent = implode(",", $parentTab);
            $subtabsParentArray = explode(",", $subtabsParent);
            foreach ($subtabsParentArray as $value) {
                if (in_array($value, $maintabs) == false) {
                    
                    $url = $this->_urlInterface->getUrl('productdesigner/subtabs/index', $paramsHere = array());
                    $msg = 'Removal of main tabs which contains subtabs will result in removal of those subtabs as well.' . "<a href = '".$url."'>Manage Subtabs</a>";
                    $this->_messageManager->addWarning( __($msg) );
                }            
            }
        }
    }

}
