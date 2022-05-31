<?php
namespace Biztech\Productdesigner\Model\Adminhtml\Config\Source;
class Enabledisable implements \Magento\Framework\Option\ArrayInterface{
    protected $_helper;
    
    public function __construct( 
         \Magento\Framework\ObjectManagerInterface $interface,
         \Biztech\Productdesigner\Helper\Info $helperdata
    ){
        $this->objectManager = $interface;
        $this->_helper= $helperdata;
    }
    public function toOptionArray(){
        $options = array(            
            array('value' => 0, 'label'=>__('No')),    
        );        
        $websites = $this->_helper->getAllWebsites();
        if(!empty($websites)){           
           $options[] = array('value' => 1, 'label'=>__('Yes'));
        }
        return $options;
    }
}