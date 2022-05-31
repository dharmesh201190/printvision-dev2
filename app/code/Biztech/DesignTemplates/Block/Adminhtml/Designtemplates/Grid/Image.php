<?php

namespace Biztech\DesignTemplates\Block\Adminhtml\Designtemplates\Grid;

class Image extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {

    protected $_storeManager;
    protected $_designtemplates;
    protected $_directorylist;
    protected $_filesystem;

    public function __construct(
        \Magento\Framework\Image\Factory $imageFactory, \Magento\Store\Model\StoreManagerInterface $storeManager, \Biztech\DesignTemplates\Model\Mysql4\Designtemplates\CollectionFactory $designtemplates, \Magento\Framework\Filesystem\DirectoryList $directorylist, \Magento\Framework\Filesystem $filesystem
    ) {
        $this->_storeManager = $storeManager;
        $this->_designtemplates = $designtemplates;
        $this->_directorylist = $directorylist;
        $this->_filesystem = $filesystem;
    }

    public function render(\Magento\Framework\DataObject $row) {
        $designtemplate_id = $row->getDesigntemplatesId();
        $designImages = $this->_designtemplates->create()->addFieldToFilter('designtemplates_id', Array('eq' => $designtemplate_id))->getFirstItem()->getData();
        if (isset($designImages['image'])) {
            $path = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'productdesigner/templates/'. $designtemplate_id . '/base/' . $designImages['image'];
            $reader = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath();
            $dirImg = $reader.'/productdesigner/templates/'. $designtemplate_id . '/base/' . $designImages['image'];
            if (isset($designImages['image']) && file_exists($dirImg)) {
                $html = '<img ';
                $html .= 'src="' . $path . '" height="135" width="135">';
            } else {
                $html = '';
            }
        } else {
            $html = '';
        }
        return $html;
    }

}
