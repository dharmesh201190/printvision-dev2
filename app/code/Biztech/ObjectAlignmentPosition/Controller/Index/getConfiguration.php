<?php
namespace Biztech\ObjectAlignmentPosition\Controller\Index;

class getConfiguration extends \Magento\Framework\App\Action\Action {

    const Identifier = 'ObjectAlignmentPosition';
    protected $_storeManager;
    protected $_data;
    protected $_infoHelper;

    public function __construct(
    \Magento\Framework\App\Action\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, \Biztech\Productdesigner\Helper\Data $data, \Biztech\Productdesigner\Helper\Info $infoHelper) {
        $this->_storeManager = $storeManager;
        $this->_data = $data;
        $this->_infoHelper = $infoHelper;
        parent::__construct($context);
    }

    public function execute() {
        try {
            $cacheKey = self::Identifier;
            $response = $this->_infoHelper->loadCache($cacheKey);
            if (!$response) {
                $response = array();
                $storeid = $this->_storeManager->getStore()->getId();
                $response['enable'] = htmlentities($this->_data->getConfig('productdesigner/object_alignment_position/enable_alignment_position', $storeid));
                $this->_infoHelper->setCache($response, $cacheKey);
            }
            $this->getResponse()->setBody(json_encode($response));
        }catch (\Exception $e) {
            $response = $this->_infoHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }
    }

}
