<?php
namespace Biztech\Productdesigner\Block\Adminhtml;

class Imageeffects extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected $imageEffectsFactory;

    public function __construct(
         \Biztech\Productdesigner\Model\Mysql4\Imageeffects\CollectionFactory $imageEffectsFactory
    ) {
        $this->imageEffectsFactory = $imageEffectsFactory;
    }
    protected function _construct()
    {
        $model = $this->imageEffectsFactory->create()->getData();

        $this->_controller = 'imageeffects';
        $this->_headerText = __('Effects Manager');
        $this->_addButtonLabel = __('Add  Image Effect');
        parent::_construct();
    }
}
