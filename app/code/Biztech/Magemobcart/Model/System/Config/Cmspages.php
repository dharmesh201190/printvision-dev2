<?php

namespace Biztech\Magemobcart\Model\System\Config;

class Cmspages
{
    protected $cmsPagesModel;

    public function __construct(
        \Magento\Cms\Model\Page $cmsPagesModel
    ) {
        $this->_cmsPagesModel = $cmsPagesModel;
    }
    public function toOptionArray()
    {
        $optionArray = array();
        $cmsPages =  $this->_cmsPagesModel->getCollection();
        foreach ($cmsPages->getData() as $_page) {
            $optionArray[] = array('label' => $_page['title'], 'value' => $_page['identifier']);
        }
        return $optionArray;
    }
}
