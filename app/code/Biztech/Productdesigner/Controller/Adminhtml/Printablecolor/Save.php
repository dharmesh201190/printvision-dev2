<?php
namespace Biztech\Productdesigner\Controller\Adminhtml\Printablecolor;
class Save extends \Biztech\Productdesigner\Controller\Adminhtml\Printablecolor
{
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            try {
                $model = $this->printableColorCollection->create();

                $data = $this->getRequest()->getPostValue();
                $id = $this->getRequest()->getParam('id');
                $pcolor=$this->printableColor->create();
                if(!$id){
                    forEach($pcolor as $color){
                    if($color->getColorCode()==$data['color_code'])
                    {
                        $this->messageManager->addError(__('This item aldready exists.'));
                        $this->_redirect('productdesigner/printablecolor');
                        return;
                    }
                }
            }

                $inputFilter = new \Zend_Filter_Input(
                    [],
                    [],
                    $data
                );
                $data = $inputFilter->getUnescaped();
               
                if(isset($data['stores'])) {
                    if(in_array('0',$data['stores'])){
                       $data['store_id'] = '0';
                    }
                    else{
                       $data['store_id'] = implode(",", $data['stores']);
                    }
                    unset($data['stores']);
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
                $this->messageManager->addSuccess(__('Printable Color was successfully saved.'));
                $this->session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('productdesigner/printablecolor/edit', ['id' => $model->getId()]);
                    return;
                }
                $this->_redirect('productdesigner/printablecolor/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $id = (int)$this->getRequest()->getParam('id');
                if (!empty($id)) {
                    $this->_redirect('productdesigner/printablecolor/edit', ['id' => $id]);
                } else {
                    $this->_redirect('productdesigner/printablecolor/new');
                }
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->logger->critical($e);
                $this->session->setPageData($data);
                $this->_redirect('productdesigner/printablecolor/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->_redirect('productdesigner/printablecolor/');
    }
}
