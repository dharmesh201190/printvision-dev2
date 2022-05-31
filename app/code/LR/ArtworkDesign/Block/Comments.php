<?php
namespace LR\ArtworkDesign\Block;
use Magento\Framework\Serialize\SerializerInterface;
/**
 * Comments content block
 */
class Comments extends \Magento\Framework\View\Element\Template
{
    protected $_artworkdesign;
    /**
    * @var \Magento\Store\Model\StoreManagerInterface
    */
    protected $storeManager;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \LR\ArtworkDesign\Model\ArtworkDesignFactory $artworkdesign,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        SerializerInterface $serializer
    ) {
        $this->_artworkdesign = $artworkdesign;
        $this->_request = $request;
        $this->serializer = $serializer;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    public function getComments($id)
    {
        if($id)
        {
            $artWork = $this->_artworkdesign->create()->load($id);
            return $this->serializer->unserialize($artWork->getArtworkdesignComment());
        }
        return;
    }

    public function getCustomerComments($id)
    {
        $artWork = $this->_artworkdesign->create()->getCollection()->addFieldToFilter('customer_id', $id)->load();
        return $artWork;
    }

    public function getMediaUrl()
    {
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl;
    }
}
