<?php
namespace Biztech\Magemobcart\Block\Adminhtml;

class Bannerslider extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_bannerslider';
        $this->_blockGroup = 'Biztech_Magemobcart';
        $this->_headerText = __('Banner Slider');
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->cartHelper = $objectManager->get('Biztech\Magemobcart\Helper\Data');
        $this->messageManager = $objectManager->get('Magento\Framework\Message\ManagerInterface');
        if ($this->cartHelper->isEnable()) {
            $this->_addButtonLabel = __('Create Banner Slider');
            $this->buttonList->add(
                'export_csv',
                [
                'label' => __('Export CSV'),
                'style' => 'background-color: #eb5202; color: #ffffff; height:45px; width:130px; font-size:16px; padding:0.5rem 0rem 0.6rem 0rem;',
                'onclick' => "setLocation('{$this->getUrl('*/*/exportcsv')}')"
                ]
            );
            $this->buttonList->add(
                'export_xml',
                [
                'label' => __('Export XML'),
                'style' => 'background-color: #eb5202; color: #ffffff; height:45px; width:130px; font-size:16px; padding:0.5rem 0rem 0.6rem 0rem;',
                'onclick' => "setLocation('{$this->getUrl('*/*/exportxml')}')"
                ]
            );
            parent::_construct();
        } else {
            $this->messageManager->addError("Please activate the extension");
            return false;
        }
        
        parent::_construct();
    }
}
