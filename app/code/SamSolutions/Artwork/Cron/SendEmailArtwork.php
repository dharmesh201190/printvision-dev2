<?php

namespace SamSolutions\Artwork\Cron;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

class SendEmailArtwork
{

    protected $uploaderFactory;

    protected $adapterFactory;

    protected $filesystem;

    protected $jsonResultFactory;

    protected $_checkoutSession;

    protected $scopeConfig;

    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    private $orderRepository;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    private $orderFactory;

    /**
     * @var \SamSolutions\Artwork\Model\Email
     */
    private $email;

    public function __construct(
        CollectionFactory $orderFactory,
        \SamSolutions\Artwork\Model\Email $email
    ) {
        $this->orderFactory = $orderFactory;
        $this->email = $email;
    }

    public function execute()
    {
        $orders = $this->orderFactory->create()->addFieldToSelect('*')->addFieldToFilter('is_needed_artwork',
            ['eq' => 1]
        )->addFieldToFilter('status', array('in' => array('processing')));;
        foreach ($orders as $order) {
            $this->email->send($order);
        }

        return $this;
    }
}
