<?php
namespace LR\ArtworkDesign\Block\Adminhtml\Items\Edit\Renderer;

class Download extends \Magento\Framework\Data\Form\Element\AbstractElement
{    
    /**
     * Renders grid column
     *
     * @param \Magento\Framework\DataObject $row
     * @return mixed
     */
    public function getAfterElementHtml()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface'); 
        $currentStore = $storeManager->getStore();

        $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        $html = '<a class="download-artwork" href="'.$mediaUrl.'/'.$this->getValue().'" download="">Download</a>';

        return $html;
    }
}
