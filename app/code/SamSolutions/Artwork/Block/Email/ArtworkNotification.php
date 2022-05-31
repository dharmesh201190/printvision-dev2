<?php

namespace SamSolutions\Artwork\Block\Email;

use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\ProductAlert\Block\Email\AbstractEmail;
use Magento\Catalog\Model\Product;
use Magento\ProductAlert\Block\Product\ImageProvider;

class ArtworkNotification extends AbstractEmail

{
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    private $productRepository;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Filter\Input\MaliciousCode $maliciousCode,
        PriceCurrencyInterface $priceCurrency,
        ProductRepository $productRepository,
        \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder,
        array $data = [],
        ImageProvider $imageProvider = null
    ) {
        parent::__construct($context, $maliciousCode, $priceCurrency, $imageBuilder, $data, $imageProvider);
        $this->productRepository = $productRepository;
    }

    protected $_template = 'SamSolutions_Artwork::email/artwork_notification.phtml';

    private   $orderId   = null;

    /**
     * Product collection array
     *
     * @var \Magento\Sales\Api\Data\OrderItemInterface[]
     */
    private $_items = [];

    public function getOrderId()
    {
        return $this->orderId;
    }

    public function setOrderId($id)
    {
        $this->orderId = $id;
    }

    /**
     * Add item to collection
     *
     * @param \Magento\Sales\Api\Data\OrderItemInterface $item
     *
     * @return void
     */
    public function addItem(\Magento\Sales\Api\Data\OrderItemInterface $item)
    {
        $this->_items[$item->getId()] = $item;
    }

    /**
     * Retrieve product collection array
     *
     * @return \Magento\Sales\Api\Data\OrderItemInterface[]
     */
    public function getItems()
    {
        return $this->_items;
    }

    public function getProduct($id)
    {
        return $this->productRepository->getById($id);
    }

    /**
     * Retrieve unsubscribe url for product
     *
     * @param int $productId
     *
     * @return string
     */
    public function getOrderLinkUrl()
    {
        $params = $this->_getUrlParams();
        $params['order_id'] = $this->getOrderId();

        return $this->getUrl('sales/order/view', $params);
    }

}
