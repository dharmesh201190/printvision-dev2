<?php
namespace Biztech\Productdesigner\Model;
    class Imageeffects extends \Magento\Framework\Model\AbstractModel
    {
       
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Biztech\Productdesigner\Model\Mysql4\Imageeffects');
    }
    
    public function toOptionArray($id){
        $option_array = array();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();   
        
        $getEffetctList = $objectManager->create('Biztech\Productdesigner\Model\Mysql4\Imageeffectslist\Collection');
        $effects_collection = $getEffetctList->getData(); 
        $tempEffect = array();
        foreach ($effects_collection as $key => $value) {
            $tempEffect['value'] = $value['name'];
            $tempEffect['label'] = $value['name'];
            array_push($option_array, $tempEffect);
        }

        $effectsdata = $objectManager->create('Biztech\Productdesigner\Model\Mysql4\Imageeffects\Collection');
        $effectuse = [];                           
        foreach($effectsdata as $effect)
        {
            if($effect->getEffectId() != $id)
                $effectuse[] = $effect->getEffectName();            
        }           
        foreach ($option_array as $key => $value) 
        {                   
           if(in_array($value['value'],$effectuse))               
            unset($option_array[$key]);          
        }
        sort($option_array);                    
        return $option_array;
    } 
}
