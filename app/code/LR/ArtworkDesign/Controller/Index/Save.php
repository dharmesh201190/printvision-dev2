<?php

namespace LR\ArtworkDesign\Controller\Index;

use Magento\Framework\App\Action\Context;
use LR\ArtworkDesign\Model\ArtworkDesignFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Filesystem;
use Magento\Checkout\Model\Session as CheckoutSession;

class Save extends \Magento\Framework\App\Action\Action
{
    const XML_PATH_EMAIL_SUCCESS = 'artwork_design/email/success_msg2';

    protected $_artworkdesign;
    protected $uploaderFactory;
    protected $adapterFactory;
    protected $filesystem;
    protected $jsonResultFactory;
    protected $_checkoutSession;
    protected $scopeConfig;

    public function __construct(
        Context $context,
        ArtworkDesignFactory $artworkdesign,
        UploaderFactory $uploaderFactory,
        AdapterFactory $adapterFactory,
        Filesystem $filesystem,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        CheckoutSession $checkoutSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_artworkdesign = $artworkdesign;
        $this->uploaderFactory = $uploaderFactory;
        $this->adapterFactory = $adapterFactory;
        $this->filesystem = $filesystem;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }
    public function execute()
    {
        $post = $this->getRequest()->getParams();
        $customerName = $post['artworkdesign_name'];
        $customerEmail = $post['artworkdesign_email'];
        $customerPhone = $post['artworkdesign_phone'];
        $customerComment = $post['artworkdesign_comment'];

        if(isset($_FILES['artworkdesign_image']['name']) && $_FILES['artworkdesign_image']['name'] != '') {
            try{
                $uploaderFactory = $this->uploaderFactory->create(['fileId' => 'artworkdesign_image']);
                $uploaderFactory->setAllowedExtensions(['svg', 'jpg', 'jpeg', 'png', 'docx', 'doc', 'pdf']);
                $imageAdapter = $this->adapterFactory->create();
                $uploaderFactory->addValidateCallback('custom_image_upload',$uploaderFactory,'validateUploadFile');
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
                
                $imagePath = 'lr/artworkdesign'.$result['file'];
                $fullPath = $destinationPath.'/'.$result['file'];
                $imageName = $result['file'];
                $post['artworkdesign_image'] = $imagePath;
                $post['full_path'] = $fullPath;
                $post['image_name'] = $_FILES['artworkdesign_image']['name'];
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__("We can\'t process your request right now. Sorry, that\'s all we know.".$e->getMessage()));
                $response = array('success'=>0);
                return $this->jsonResultFactory->create()->setData($response);
            }
        }

        try {
            $this->_checkoutSession->setEmailParams($post);
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $successMsg = $this->scopeConfig->getValue(self::XML_PATH_EMAIL_SUCCESS, $storeScope);
            $this->messageManager->addSuccessMessage(__($successMsg));
            $response = array('success'=>1);
            return $this->jsonResultFactory->create()->setData($response);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__("We can\'t process your request right now. Sorry, that\'s all we know.".$e->getMessage()));
            $response = array('success'=>0);
            return $this->jsonResultFactory->create()->setData($response);
        }
    }
}
