<?php
namespace Biztech\Productdesigner\Block\Adminhtml\Config\Form\Renderer;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Website extends Field
{
    protected $_scopeConfig;
    protected $helper;
    protected $_encryptor;  
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Biztech\Productdesigner\Helper\Data $helper,
        array $data = []
    ){
        $this->_encryptor = $encryptor;
        $this->helper = $helper;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->storeManager = $context->getStoreManager();
        parent::__construct($context,$data);
    }
    protected function _getElementHtml(AbstractElement $element)
    {
        $html      = '';
        $data      = $this->_scopeConfig->getValue('productdesigner/activation/data');
        
        $ele_value = explode(',', str_replace($data, '', $this->_encryptor->decrypt($element->getValue())));
        
        $ele_name  = $element->getName();
        $ele_id    = $element->getId();
        $element->setName($ele_name . '[]');
        $data_info1 = $this->helper->getDataInfo();
        $data_info = (array)$data_info1;
        if(isset($data_info['dom']) &&  intval($data_info['c']) > 0  &&  intval($data_info['suc']) == 1){
            foreach ($this->storeManager->getWebsites() as $website) {
                $url =  $this->_scopeConfig->getValue('web/unsecure/base_url');
                $url = $this->helper->getFormatUrl(trim(preg_replace('/^.*?\/\/(.*)?\//', '$1', $url)));
                foreach($data_info['dom'] as $web){
                    if($web->dom == $url && $web->suc == 1)    {
                        $element->setChecked(false);
                        $id = $website->getId();                        
                        $name = $website->getName();
                        $element->setId($ele_id.'_'.$id);
                        $element->setValue($id);
                        if(in_array($id, $ele_value) !== false){                            
                            $element->setChecked(true);
                        }
                        if ($id!=0) {
                            $html .= '<div><label>'.$element->getElementHtml().' '.$name.' </label></div>';
                        }
                    }
                }
            }
        }else{
            $html = sprintf('<strong class="required" style="color:red;">%s</strong>', __('Please enter a valid key'));

        }
        return $html;
    }
}

