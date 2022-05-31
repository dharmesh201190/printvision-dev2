<?php

namespace Biztech\Productdesigner\Model\Adminhtml\System\Config\Backend\Design;
use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized;

class Maintab extends \Magento\Framework\App\Config\Value {

    public function beforeSave() {
        $value1 = $this->getValue();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $subtabs_collection = $objectManager->create('Biztech\Productdesigner\Model\Mysql4\Subtabs\Collection');
        $subtabs = [];
        $parentTab = [];
        foreach ($subtabs_collection as $subtab) {
            $subtabs[] = $subtab->getData('subtabs');
            $parentTab[] = $subtab->getData('maintab');
        }
        $subtabsString = implode(",", $subtabs);
        $subtabsArray = explode(",", $subtabsString);
        $counter = false;
        foreach ($subtabsArray as $value) {
            if (in_array($value, $value1)) {
                throw new \Exception("Some of the main tabs are already exist in sub tabs");
            }            
        }
        $subtabsParent = implode(",", $parentTab);
        $subtabsParentArray = explode(",", $subtabsParent);
        foreach ($subtabsParentArray as $value) {
            if (in_array($value, $value1) == false) {
                $messageManager = $objectManager->create('Magento\Framework\Message\ManagerInterface');
                $urlBuilder = $objectManager->create('Magento\Framework\UrlInterface');
                $url = $urlBuilder->getUrl('productdesigner/subtabs/index', $paramsHere = array());
                $msg = 'Removal of main tabs which contains subtabs will result in removal of those subtabs as well.' . "<a href = '".$url."'>Manage Subtabs</a>";
                $messageManager->addWarning( __($msg) );
            }            
        }
        $this->setValue($value1);
        parent::beforeSave();
    }

}
