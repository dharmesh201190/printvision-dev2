<?php
namespace Biztech\Productdesigner\Controller\Adminhtml\Imageeffects;

class Save extends \Biztech\Productdesigner\Controller\Adminhtml\Imageeffects {

    public function execute() {
        if ($this->getRequest()->getPostValue()) {
            try {
                $model = $this->imageEffectsFactory->create();

                $data = $this->getRequest()->getPostValue();
                
                $inputFilter = new \Zend_Filter_Input(
                        [], [], $data
                );
                $data = $inputFilter->getUnescaped();
                $id = $this->getRequest()->getParam('id');

                $fileData = $this->getRequest()->getFiles('effect_image');
                $effect_imageData = '';
                if (isset($fileData['name']) && $fileData['name'] != '') {
                    $uploader = $this->_fileUploaderFactory->create(['fileId' => 'effect_image']);
                    $effect_imageData = $uploader->validateFile();
                }
                if (isset($effect_imageData['name']) && $effect_imageData['name'] != '') {
                    $file_name = $effect_imageData['name'];
                    $file_size = $effect_imageData['size'];
                    $file_tmp = $effect_imageData['tmp_name'];
                    $file_type = $effect_imageData['type'];
                    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
                    $data['image_upload_new'] = [
                        'name' => $file_name,
                        'type' => $file_type,
                        'tmp_name' => $file_tmp,
                        'error' => 0,
                        'size' => $file_size
                    ];
                }
                if (isset($effect_imageData['name']) && $effect_imageData['name'] != '') {

                    $uploader = $this->_fileUploaderFactory->create(['fileId' => $data['image_upload_new']]);
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(false);
                    $reader = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                    $path = $reader->getAbsolutePath() . "productdesigner/effects/";

                    $result1 = $uploader->save($path);
                    $data['effect_image'] = "productdesigner/effects/" . $result1['file'];
                } else {
                    if (isset($data['effect_image']['delete'])) {
                        $data['effect_image'] = '';
                    } else {
                        $data['effect_image'] = null;
                    }
                }
                if ($id) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('The wrong item is specified.'));
                    }
                }
                $model->setData($data)->setId($id);
                $this->session->setPageData($model->getData());
                $model->save();

                $this->messageManager->addSuccess(__('Item was successfully saved.'));
                $this->session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('productdesigner/imageeffects/edit', ['id' => $model->getId()]);
                    return;
                }
                $this->_redirect('productdesigner/imageeffects/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $id = (int) $this->getRequest()->getParam('id');
                if (!empty($id)) {
                    $this->_redirect('productdesigner/imageeffects/edit', ['id' => $id]);
                } else {
                    $this->_redirect('productdesigner/imageeffects/new');
                }
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(
                        __('Something went wrong while saving the item data. Please review the error log.')
                );
                $this->logger->critical($e);
                $this->session->setPageData($data);
                $this->_redirect('productdesigner/imageeffects/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->_redirect('productdesigner/imageeffects/');
    }

}
