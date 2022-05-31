<?php
namespace Biztech\Magemobcart\Controller\Adminhtml\Notification;

use Magento\Backend\App\Action;

class Save extends \Magento\Backend\App\Action
{
    protected $uploaderFactory;
    protected $fileSystem;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem\DirectoryList $fileSystem
    ) {
        $this->_uploaderFactory = $uploaderFactory;
        $this->_fileSystem = $fileSystem;
        parent::__construct($context);
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->_objectManager->create('Biztech\Magemobcart\Model\Notification');
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
                $uploader->setAllowedExtensions(['jpg', 'jpeg','png']);
                $imageAdapterFactory = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')
                    ->create();
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(true);
                $uploader->setAllowCreateFolders(true);
                $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                    ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);

                $result = $uploader->save(
                    $mediaDirectory
                        ->getAbsolutePath('Magemobcart/Notification')
                );
                $filepath = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'Magemobcart/Notification'.$result['file'];
                $model->setProfile('Magemobcart/Notification'.$result['file']);
            } catch (\Exception $e) {
                if ($e->getCode() == 0) {
                    $this->messageManager->addError($e->getMessage());
                    return $resultRedirect->setPath('*/*/');
                }
            }
            if (array_key_exists('file', $result) && $filepath != "") {
                $data['filename'] = $result['file'];
                $data['filepath'] = $filepath;
                $data['is_sent'] = 2;
            }
            // }
            if ($id) {
                $model->load($id);
            }
            $model->setData($data);

            try {
                $model->save();
                $notificaionId = $model->getId();
                $notificationData = $this->getRequest()->getParams();
                try {
                    if ($notificaionId != "") {
                        $deviceModel = $this->_objectManager->get('Biztech\Magemobcart\Model\Devicedata');
                        $collectionDevice = $deviceModel->getCollection();
                        if ($data['os'] == 'android') {
                            $collectionDevice = $collectionDevice->addFieldToFilter('device_type', 'android');
                        } elseif ($data['os'] == 'ios') {
                            $collectionDevice = $collectionDevice->addFieldToFilter('device_type', 'ios');
                        }
                        $existingNotificationIds = '';
                        foreach ($collectionDevice as $item) {
                            $existingNotificationIds = $item->getNotificationId();
                            $deviceId = $item->getId();
                            
                            $finalIdListArray = $this->getNotificationIds($existingNotificationIds, $notificaionId);

                            $finalIdLists = $finalIdListArray['final_ids'];
                            $isChange = $finalIdListArray['flag'];
                            if ($isChange) {
                                $deviceModel->load($deviceId)->setCronStatus('pending')
                                    ->setNotificationId($finalIdLists)
                                    ->setId($item->getId())
                                    ->save();
                            } else {
                                $deviceModel->load($deviceId)->setCronStatus('pending')
                                    ->save();
                            }
                        }
                        $model->setIsSent('2');
                        $model->save();
                        if (!array_key_exists('back', $notificationData)) {
                            if ($notificaionId) {
                                $this->sendNotification($notificaionId);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    $this->messageManager->addError('Something went wrong while saving the entry.'.$e->getMessage());
                }

                $this->messageManager->addSuccess(__('The Notification detail has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
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
    public function getNotificationIds($existingNotificationIds, $currentNotificationId)
    {
        if ($existingNotificationIds == "") {
            $finalIdList = $currentNotificationId;
            $flag = 1;
        } else {
            $existingExplode = explode(",", $existingNotificationIds);
            $existingExplode = array_filter($existingExplode);
            if (in_array($currentNotificationId, $existingExplode)) {
                $flag = 0;
            } else {
                array_push($existingExplode, $currentNotificationId);
                $flag = 1;
            }
            $finalIdList = implode(",", $existingExplode);
        }
        $finalDataArray['flag'] = $flag;
        $finalDataArray['final_ids'] = $finalIdList;
        return $finalDataArray;
    }
    public function sendNotification($id)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $model = $objectManager->get('Biztech\Magemobcart\Model\Notification');
        $notifications = $model->getCollection();
        $notifications->addFieldToFilter('notification_id', $id);
        foreach ($notifications as $item) {
            $model = $objectManager->get('Biztech\Magemobcart\Model\Notification')->load($item->getNotificationId());
            $data['url'] = $model->getUrl();
            $data['title'] = $model->getTitle();
            $data['category_id'] = $model->getCategoryId();
            $data['product_id'] = $model->getProductId();
            $data['message'] = $model->getMessage();
            $data['imagefilename'] = $model->getFilepath();
            $data['type'] = $item->getType();
            $data['notification_id'] = $model->getNotificationId();
            $data['os'] = $model->getOs();
            $data['website_id'] = $model->getWebsiteId();

            $model->setIsSent('2')->save();
            $objectManager->get('Biztech\Magemobcart\Helper\Notification')->sendNotification($data);
            $model->setIsSent('1')->save();
        }
        return true;
    }
}
