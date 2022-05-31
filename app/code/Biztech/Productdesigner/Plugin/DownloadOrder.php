<?php

namespace Biztech\Productdesigner\Plugin;

use Magento\Backend\Model\UrlInterface;

class DownloadOrder
{
    protected $_backendUrl;

    public function __construct(
        UrlInterface $backendUrl
    ) {
        $this->_backendUrl = $backendUrl;
    }

    public function beforePushButtons(
        \Magento\Backend\Block\Widget\Button\Toolbar\Interceptor $subject,
        \Magento\Framework\View\Element\AbstractBlock $context,
        \Magento\Backend\Block\Widget\Button\ButtonList $buttonList
    ) {
        $this->_request = $context->getRequest();
        $params = $this->_request->getParams();
       
        if(isset(($params['order_id']))){
           $order_id = $params['order_id'];
           $location = $this->_backendUrl->getUrl('productdesigner/Productdesigner/downloadOrder',['order_id' => $order_id]);
           if($this->_request->getFullActionName() == 'sales_order_view'){
              $buttonList->add(
                'mybutton',
                ['label' => __('Download Order'),
                'onclick' => "setLocation('" .$location. "')",
                'class' => 'reset'],
                -1
            );
          }
    }

    }
}