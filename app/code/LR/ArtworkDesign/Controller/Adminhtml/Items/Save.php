<?php
namespace LR\ArtworkDesign\Controller\Adminhtml\Items;

class Save extends \LR\ArtworkDesign\Controller\Adminhtml\Items
{
    const XML_PATH_EMAIL_RECIPIENT = 'artwork_design/email/send_email_from';
    const XML_PATH_EMAIL_TEMPLATE = 'artwork_design/email/artworkadmin_email_template';

    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            try {
                $model = $this->_objectManager->create('LR\ArtworkDesign\Model\ArtworkDesign');
                $data = $this->getRequest()->getPostValue();
                $fullPath = '';
                $imageName = '';
                $fileContent = '';
                if(isset($_FILES['admin_artwork_image']['name']) && $_FILES['admin_artwork_image']['name'] != '') {
                    try{
                        $uploaderFactory = $this->uploaderFactory->create(['fileId' => 'admin_artwork_image']);
                        $uploaderFactory->setAllowedExtensions(['svg', 'jpg', 'jpeg', 'png', 'docx', 'doc', 'pdf']);
                        $imageAdapter = $this->adapterFactory->create();
                        $uploaderFactory->addValidateCallback('custom_image_upload',$uploaderFactory,'validateUploadFile');
                        $uploaderFactory->setAllowRenameFiles(true);
                        $uploaderFactory->setFilesDispersion(true);
                        $mediaDirectory = $this->filesystem->getDirectoryRead($this->directoryList::MEDIA);
                        $destinationPath = $mediaDirectory->getAbsolutePath('lr/artworkdesign');
                        $result = $uploaderFactory->save($destinationPath);
                        if (!$result) {
                            throw new LocalizedException(
                                __('File cannot be saved to path: $1', $destinationPath)
                            );
                        }
                        
                        $imagePath = 'lr/artworkdesign'.$result['file'];
                        $fullPath = $destinationPath.'/'.$result['file'];
                        $imageName = $_FILES['admin_artwork_image']['name'];
                        $data['admin_artwork_image'] = $imagePath;
                        $fileContent = file_get_contents($fullPath);
                    } catch (\Exception $e) {
                        $this->messageManager->addError($e->getMessage());
                        $id = (int)$this->getRequest()->getParam('artworkdesign_id');
                        if (!empty($id)) {
                            $this->_redirect('lr_artworkdesign/*/edit', ['id' => $id]);
                        } else {
                            $this->_redirect('lr_artworkdesign/*/new');
                        }
                        return;
                    }
                }
                if(isset($data['admin_artwork_image']['delete']) && $data['admin_artwork_image']['delete'] == 1) {
                    $mediaDirectory = $this->filesystem->getDirectoryRead($this->directoryList::MEDIA)->getAbsolutePath();
                    $file = $data['admin_artwork_image']['value'];
                    $imgPath = $mediaDirectory.$file;
                    if ($this->_file->isExists($imgPath))  {
                        $this->_file->deleteFile($imgPath);
                    }
                    $data['admin_artwork_image'] = NULL;
                }
                if (isset($data['admin_artwork_image']['value'])){
                    $data['admin_artwork_image'] = $data['admin_artwork_image']['value'];
                }
                $inputFilter = new \Zend_Filter_Input(
                    [],
                    [],
                    $data
                );
                $data = $inputFilter->getUnescaped();
                $id = $this->getRequest()->getParam('artworkdesign_id');
                if ($id) {
                    $model->load($id);
                    $model->getArtworkdesignComment();
                    $serializer = $this->_objectManager->create(\Magento\Framework\Serialize\SerializerInterface::class);

                    $comments = $serializer->unserialize($model->getArtworkdesignComment());
                    if(isset($data['admin_artwork_image']))
                    {
                       $comments[] = array('is_customer'=>0,'comment'=>$data['admin_artwork_comment'],'image'=>$data['admin_artwork_image']); 
                    } else {
                        $comments[] = array('is_customer'=>0,'comment'=>$data['admin_artwork_comment']);
                    }
                    //$comments[]['comment'] = $post['artworkdesign_comment'];
                    $data['artworkdesign_comment'] = $serializer->serialize($comments);

                    if ($id != $model->getId()) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('The wrong item is specified.'));
                    }
                }
                $model->setData($data);
                $session = $this->_objectManager->get('Magento\Backend\Model\Session');
                $session->setPageData($model->getData());
                $model->save();

                $this->inlineTranslation->suspend();

                $post = $this->getRequest()->getPostValue();
                $postObject = new \Magento\Framework\DataObject();
                $postObject->setData($post);
                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                 
                $error = false;
                 
                $sender = [
                    'name' => 'Admin',
                    'email' => $this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $storeScope),
                ];                 
                
                $email = $post['artworkdesign_email'];                

                $transport = $this->_transportBuilder
                    ->setTemplateIdentifier($this->scopeConfig->getValue(self::XML_PATH_EMAIL_TEMPLATE, $storeScope)) // this code we have mentioned in the email_templates.xml
                    ->setTemplateOptions([
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                        ])
                    ->setTemplateVars(['data' => $postObject])
                    ->setFrom($sender)
                    //->addTo($this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $storeScope))
                    ->addTo($email, $storeScope);
                if($fileContent != '')
                {
                    $transport->addAttachment($fileContent, $imageName,'image');
                }
                $transport->getTransport()->sendMessage();
                $this->inlineTranslation->resume();

                $this->messageManager->addSuccess(__('You saved the item.'));
                $session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('lr_artworkdesign/*/edit', ['id' => $model->getArtworkdesignId()]);
                    return;
                }
                $this->_redirect('lr_artworkdesign/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $id = (int)$this->getRequest()->getParam('artworkdesign_id');
                if (!empty($id)) {
                    $this->_redirect('lr_artworkdesign/*/edit', ['id' => $id]);
                } else {
                    $this->_redirect('lr_artworkdesign/*/new');
                }
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('Something went wrong while saving the item data. Please review the error log.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data);
                $this->_redirect('lr_artworkdesign/*/edit', ['id' => $this->getRequest()->getParam('artworkdesign_id')]);
                return;
            }
        }
        $this->_redirect('lr_artworkdesign/*/');
    }
}
