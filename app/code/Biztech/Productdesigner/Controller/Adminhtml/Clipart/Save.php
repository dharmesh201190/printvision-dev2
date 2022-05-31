<?php


namespace Biztech\Productdesigner\Controller\Adminhtml\Clipart;

class Save extends \Biztech\Productdesigner\Controller\Adminhtml\Clipart {

    public function execute() {
        if ($this->getRequest()->getPostValue()) {
            try {
                $clipartModel = $this->clipartFactory->create();

                // Changes by dipak
                $data = $this->prepareData();

                $id = $this->getRequest()->getParam('id');
                if ($id) {
                    $clipartModel->load($id);
                    if ($id != $clipartModel->getId()) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('The wrong item is specified.'));
                    }
                }
                $clipartCatCollection = $clipartModel->getCollection();
                foreach ($clipartCatCollection as $clipartCat) {
                    if ($clipartCat->getId() != $id && $clipartCat->getClipartTitle() == trim($data['clipart_title'])) {
                        $this->messageManager->addError(__('This title already exists.'));
                        $this->_redirect('productdesigner/clipart');
                        return;
                    }
                }

                $clipartModel->setData($data)->setId($id);
                $session = $this->_session;
                $session->setPageData($clipartModel->getData());
                $clipartModel->save();
                // store cliaprt media images

                if (isset($data['clipart'])) {
                    $clipart_gallery = $data['clipart'];
                }
                if (!$id) {
                    $id = $clipartModel->getId();
                }

                if ($id) {
                    $i = 0;
                    if (isset($clipart_gallery)) {
                        // Changes by dipak
                        $this->saveClipartMediaGallery($clipart_gallery, $session, $id);
                    }
                }
                // store cliaprt media images
                $this->messageManager->addSuccess(__('Clipart was successfully saved.'));
                $session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('productdesigner/clipart/edit', ['id' => $clipartModel->getId()]);
                    return;
                }
                $this->_redirect('productdesigner/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                // Changes by dipak
                $this->localizedException($e);
                return;
            } catch (\Exception $e) {
                // Changes by dipak
                $this->otherException($e);
                return;
            }
        }
        $this->_redirect('productdesigner/clipart/');
    }

    protected function prepareData($value = '') {
        $data = $this->getRequest()->getPostValue();
        $inputFilter = new \Zend_Filter_Input(
                [], [], $data
        );
        $data = $inputFilter->getUnescaped();
        if (isset($data['clipart_title'])) {
            $data['clipart_title'] = trim($data['clipart_title']);
        }
        if (isset($data['parent_categories'])) {
            $data['is_root_category'] = 0;
        }
        if(isset($data['is_pattern'])){
            $data['is_pattern'] = $data['is_pattern'];
        }else{
            $data['is_pattern'] = 0;
        }
        // echo "<pre>";
        // print_r($data);
        // exit();
        if ($data['is_root_category'] == 1) {
            $data['parent_categories'] = '';
        }
        // if(isset($data['is_pattern'])){
        //     $data['is_pattern'] = $data['is_pattern'];
        // }else{
        //     $data['is_pattern'] = 0;
        // }
        if (isset($data['is_root_category']) && $data['is_root_category'] != 0) {
            $data['level'] = 0;
        } else {
            $data['level'] = 1;
            if ($data['parent_categories']) {
                $parentCat = $this->clipartFactory->create()->load($data['parent_categories'])->getData();
                $data['level'] = $parentCat['level'] + 1;
                // if(isset($parentCat['is_pattern'])){
                //     $data['is_pattern'] = $parentCat['is_pattern'];
                // }else{
                //     $data['is_pattern'] = 0;
                // }
            }
        }
        if (isset($data['stores'])) {
            if (in_array('0', $data['stores'])) {
                $data['store_id'] = '0';
            } else {
                $data['store_id'] = implode(",", $data['stores']);
            }
            unset($data['stores']);
        }

        return $data;
    }

    protected function saveClipartMediaGallery($clipart_gallery, $session, $id) {
        foreach ($clipart_gallery as $c_gallery) {
            $media_model = $this->clipartMediaFactory->create();
            $inputFilter = new \Zend_Filter_Input(
                    [], [], $c_gallery
            );
            $data = $inputFilter->getUnescaped();
            $image_id = $data['image_id'];
            $image_path = $data['file'];
            if ($image_id) {
                $media_model->load($image_id);
            }
            $exclude = (isset($data['exclude'])) ? 1 : 0;
            $remove = isset($data['remove']) ? 1 : 0;
            $dataArray = array(
                'clipart_id' => $id,
                'image_path' => $data['file'],
                'label' => $data['label'],
                'tags' => $data['tags'],
                'position' => $data['sort'],
                'price' => $data['price'],
                'disabled' => $exclude,
                'remove' => $remove,
            );

            try {
                if ($dataArray['remove'] == 1) {
                    $media_model->load($image_id);
                    $media_model->delete();
                    $reader = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                    $dirImg = $reader->getAbsolutePath();
                    $clipartPath = $dirImg . 'productdesigner' . DIRECTORY_SEPARATOR . 'clipart';
                    $baseImg = $clipartPath . $image_path;
                    $resizedImg = $clipartPath . DIRECTORY_SEPARATOR . 'resized' . $image_path;
                    $mediumImg = $clipartPath . DIRECTORY_SEPARATOR . 'medium' . $image_path;
                    if (file_exists($baseImg)) {
                        unlink($baseImg);
                    }
                    if (file_exists($resizedImg)) {
                        unlink($resizedImg);
                    }
                    if (file_exists($mediumImg)) {
                        unlink($mediumImg);
                    }
                    $this->checkForDirectory($baseImg);
                    $this->checkForDirectory($resizedImg);
                    $this->checkForDirectory($mediumImg);
                } else {
                    if ($image_id) {
                        $media_model->setData($dataArray)->setId($image_id);
                        $media_model->save();
                    } else {
                        $media_model->setData($dataArray);
                        $media_model->save();
                    }
                }
            } catch (\Exception $e) {
                
            }
            $this->_session->setPageData($media_model->getData());
        }
    }

    protected function localizedException($e) {
        $this->messageManager->addError($e->getMessage());
        $id = (int) $this->getRequest()->getParam('id');
        if (!empty($id)) {
            $this->_redirect('productdesigner/clipart/edit', ['id' => $id]);
        } else {
            $this->_redirect('productdesigner/clipart/new');
        }
    }

    protected function otherException($e) {
        $this->messageManager->addError(
                __('Something went wrong while saving the item data. Please review the error log.' . $e->getMessage())
        );
        $this->_redirect('productdesigner/clipart/edit', ['id' => $this->getRequest()->getParam('id')]);
    }

    protected function checkForDirectory($directoryPath, $baseDirectoryName = 'clipart') {

        // flag value that will be changed once operation is done
        $flag = false;

        // Process begins
        do {

            // Store last position of directory separator
            $pos = strrpos($directoryPath, '/');

            // fetch full path till directory of image
            $directoryPath = substr($directoryPath, 0, $pos);

            // find last occurence of directory separator
            $pos = strrpos($directoryPath, '/') + 1;

            // find directory name
            $pathName = substr($directoryPath, $pos);

            // Check if directory is 
            if (is_dir($directoryPath) && ($pathName != $baseDirectoryName && $pathName != 'resized' && $pathName != 'medium')) {

                // check for available files(excluding system hidden folders)
                $files = array_diff(scandir($directoryPath), array('..', '.'));

                // if file count is 0, that indicates folder is empty, will remove the same
                if (count($files) == 0) {
                    rmdir($directoryPath);
                }

                // check if path has reached to base image path which indicates that our operation is complete
            } else if (($pathName == $baseDirectoryName || $pathName == 'resized') && $pathName != 'medium') {

                // this will indicate that our opearation is complete and traversal can be stopped now
                $flag = true;
            } else if ($pathName == $baseDirectoryName || $pathName == 'medium') {

                // this will indicate that our opearation is complete and traversal can be stopped now
                $flag = true;
            }
        } while ($flag == false);
    }

}
