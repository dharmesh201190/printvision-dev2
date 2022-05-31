<?php

namespace Biztech\Productdesigner\Controller\Adminhtml\Clipart;

class Delete extends \Biztech\Productdesigner\Controller\Adminhtml\Clipart {

    public function execute() {
        $id = $this->getRequest()->getParam('id');
        $reader = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $dirImg = $reader->getAbsolutePath();
        if ($id) {
            try {
                $clipartModel = $this->clipartFactory->create();
                $clipartModel->load($id);
                $this->deleteClipartMedia($id, $dirImg);
                $clipartModel->delete();
                $this->messageManager->addSuccess(__('You deleted the Clipart(s).'));
                $this->_redirect('productdesigner/clipart/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_logger->critical($e);
                $this->_redirect('productdesigner/clipart/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('We can\'t delete Clipart(s) right now. Please review the log and try again.')
                );
                $this->_logger->critical($e);
                $this->_redirect('productdesigner/clipart/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->messageManager->addError(__('We can\'t find a Clipart(s) to delete.'));
        $this->_redirect('productdesigner/clipart/');
    }

    protected function deleteClipartMedia($id, $dirImg) {
        $clipartMediaModel = $this->clipartMediaCollection->create()->getCollection()->addFieldToFilter('clipart_id', array('eq' => $id));
        foreach ($clipartMediaModel->getData() as $clipartMedia) {
            if ($clipartMedia['image_path']) {
                $clipartPath = $dirImg . 'productdesigner' . DIRECTORY_SEPARATOR . 'clipart';
                $baseImg = $clipartPath . $clipartMedia['image_path'];
                $resizedImg = $clipartPath . DIRECTORY_SEPARATOR . 'resized' . $clipartMedia['image_path'];
                $mediumImg = $clipartPath . DIRECTORY_SEPARATOR . 'medium' . $clipartMedia['image_path'];
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
            }
        }
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
