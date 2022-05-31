<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Adminhtml\Bannerslider;

use Magento\Backend\App\Action;

class Save extends \Magento\Backend\App\Action
{
    protected $uploaderFactory;
    protected $fileSystem;
    protected $bannerSliderModel;
    protected $storeManagerInterface;
    protected $backendSession;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem $fileSystem,
        \Biztech\Magemobcart\Model\Bannerslider $bannerSliderModel,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Backend\Model\Session $backendSession
    ) {
        $this->_uploaderFactory = $uploaderFactory;
        $this->_fileSystem = $fileSystem;
        $this->_bannerSliderModel = $bannerSliderModel;
        $this->_storeManagerInterface = $storeManagerInterface;
        $this->_backendSession = $backendSession;
        parent::__construct($context);
    }
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->_bannerSliderModel;

            $id = $this->getRequest()->getParam('id');
            if (isset($data['stores'])) {
                if (in_array('0', $data['stores'])) {
                    $data['store_id'] = '0';
                } else {
                    $data['store_id'] = implode(",", $data['stores']);
                }
                unset($data['stores']);
            }
            // if (isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
            try {
                $result = array();
                $uploader = $this->_objectManager->create(
                    'Magento\MediaStorage\Model\File\Uploader',
                    ['fileId' => 'filename']
                );
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'png']);
                $imageAdapterFactory = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')
                    ->create();
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(true);
                $uploader->setAllowCreateFolders(true);
                $mediaDirectory = $this->_fileSystem
                    ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);

                $result = $uploader->save(
                    $mediaDirectory
                        ->getAbsolutePath('Magemobcart/Banners')
                );
                $filepath = $this->_storeManagerInterface->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'Magemobcart/Banners'.$result['file'];
                $model->setProfile('Magemobcart/Banners'.$result['file']);
            } catch (\Exception $e) {
                if ($e->getCode() == 0) {
                    $this->messageManager->addError($e->getMessage());
                    return $resultRedirect->setPath('*/*/');
                }
            }
            if (array_key_exists('file', $result) && $filepath != "") {
                $data['filename'] = $result['file'];
                $data['filepath'] = $filepath;
            }
            
            // }
            if ($id) {
                $model->load($id);
            }

            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccess(__('The Banner slider has been saved.'));
                $this->_backendSession->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the entry.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
