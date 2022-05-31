<?php

namespace SamSolutions\Artwork\Controller\Index;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Sales\Model\OrderRepository;
use Magento\Framework\Controller\ResultFactory;

class Add extends \Magento\Framework\App\Action\Action
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

    public function __construct(
        Context $context,
        UploaderFactory $uploaderFactory,
        AdapterFactory $adapterFactory,
        Filesystem $filesystem,
        OrderRepository $orderRepository,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        CheckoutSession $checkoutSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->uploaderFactory = $uploaderFactory;
        $this->adapterFactory = $adapterFactory;
        $this->filesystem = $filesystem;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
        $this->orderRepository = $orderRepository;
    }

    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        $post = $this->getRequest()->getParams();
        $orderId = $post['order_id'];
        $itemId = $post['item_id'];
        $order = $this->orderRepository->get($orderId);
        $item = $order->getItemById($itemId);
        if (isset($_FILES['artworkdesign_image']['name']) && $_FILES['artworkdesign_image']['name'] != '') {
            try {
                $uploaderFactory = $this->uploaderFactory->create(['fileId' => 'artworkdesign_image']);
                $uploaderFactory->setAllowedExtensions(['indt', 'ai', 'pdf']);
                $imageAdapter = $this->adapterFactory->create();
                $uploaderFactory->addValidateCallback('custom_image_upload', $uploaderFactory, 'validateUploadFile');
                $uploaderFactory->setAllowRenameFiles(true);
                $uploaderFactory->setFilesDispersion(true);
                $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
                $destinationPath = $mediaDirectory->getAbsolutePath('lr/artworkdesign');
                $result = $uploaderFactory->save($destinationPath);
                if (!$result) {
                    throw new LocalizedException(
                        __('File cannot be saved to path: $1', $destinationPath)
                    );
                }

                $imagePath = 'lr/artworkdesign' . $result['file'];
                $fullPath = $destinationPath . '/' . $result['file'];
                $imageName = $result['file'];
                $post['artworkdesign_image'] = $imagePath;
                $post['full_path'] = $fullPath;
                $post['image_name'] = $_FILES['artworkdesign_image']['name'];
                $productOptions = $item->getProductOptions();
                $productOptions ['additional_options'][] =
                    [
                        'label'       => 'Artwork',
                        'type'        => 'file',
                        'custom_view' => true,
                        'value'       => "<a href='/pub/media/" . $imagePath . "' download='" . $result['file'] . "'>" . $post['image_name'] . "</a>",
                    ];
                foreach ($productOptions ['additional_options'] as $key => $option) {
                    if ($option['label'] == 'Artwork After') {
                        unset($productOptions['additional_options'][$key]);
                    }
                }
                $item->setProductOptions($productOptions)->save();
                $this->orderRepository->save($order);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__("We can\'t process your request right now. Sorry, that\'s all we know." . $e->getMessage()));

                return $resultRedirect;
            }

            return $resultRedirect;
        }
    }
}
