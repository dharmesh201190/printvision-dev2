<?php
namespace Biztech\Productdesigner\Controller\Designs;

class getDesigns extends \Magento\Framework\App\Action\Action {

    protected $_storeManager;
    protected $_helper;
    protected $_infoHelper;
    protected $session;

    public function __construct(
    \Magento\Framework\App\Action\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, \Biztech\Productdesigner\Helper\Data $helper, \Biztech\Productdesigner\Helper\Info $infoHelper, \Magento\Customer\Model\Session $session
    ) {
        $this->_storeManager = $storeManager;
        $this->_helper = $helper;
        $this->_infoHelper = $infoHelper;
        $this->session = $session;
        parent::__construct($context);
    }

    public function execute() {
        try {
            if ($this->session->isLoggedIn()) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $customer_id = $this->session->getCustomer()->getId();
                $customer['customer_login'] = 1;
                $customer['customer_id'] = $customer_id;
                $data = array(
                    'customer_id' => $customer_id,
                    'searchText' => $params['searchText'],
                    'page' => $params['page'],
                    'limit' => $params['limit']
                );
                $customer['searchText'] = $params['searchText'];
                $design = $this->_helper->getCustomerDesign($data);
                $customer['getMyDesigns'] = $design['designdata'];
                $customer['loadMoreFlag'] = $design['loadMoreFlag'];
                $customer['page'] = $design['page'];
                $customer['totalpages'] = $design['totalpages'];
            } else {
                $customer['getMyDesigns'] = array();
                $customer['loadMoreFlag'] = 0;
            }

            $this->getResponse()->setBody(json_encode($customer));
        } catch (\Exception $e) {
            $response = $this->_infoHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }
    }

}
