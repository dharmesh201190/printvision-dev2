<?php
namespace Biztech\Magemobcart\Cron;

class Test
{
    public function execute()
    {
        $id = 4;
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

            $model->setIsSent('3')->save();
            $objectManager->get('Biztech\Magemobcart\Helper\Data')->sendNotification($data);
            $model->setIsSent('1')->save();
        }
    }
}
