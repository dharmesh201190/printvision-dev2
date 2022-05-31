<?php

namespace Biztech\Productdesigner\Block\Adminhtml\Fonts\Grid;

class Image extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {

    protected $_helper;
    protected $_storeManager;
    protected $fontsFactory;
    protected $_fileSystem;
    public function __construct(
        \Magento\Catalog\Helper\Image $helper, \Magento\Framework\Image\Factory $imageFactory,\Magento\Store\Model\StoreManagerInterface $storeManager, \Biztech\Productdesigner\Model\FontsFactory $fontsFactory, \Magento\Framework\Filesystem $filesystem
        ) {
        $this->_helper = $helper;
        $this->_storeManager = $storeManager;
        $this->fontsFactory = $fontsFactory;
        $this->_fileSystem = $filesystem;
        
    }
    public function render(\Magento\Framework\DataObject $row) {

        $fontId = $row->getFontsId();
          $fontData = $this->fontsFactory->create()->load($fontId)->getData();
            if($fontData['font_image']){
                $path = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $fontData['font_image'];
                $reader = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                $dirImg = $reader->getAbsolutePath() . $fontData['font_image'];

            if(file_exists($dirImg) && getimagesize($dirImg))
            {
                $html = '<img height="auto" width="150" ';
                $html .= 'src="' . $path . '" />';
            }else{
                $html = '';
            }
        }else{
            $html = '';
        }
        return $html;
    }

}
