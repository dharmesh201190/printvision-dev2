<?php


namespace Biztech\Productdesigner\Plugin;

use Magento\Framework\Registry;

class Reorder {

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    private $cart;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    private $resultRedirectFactory;

    /**
     * @var \Magento\Sales\Controller\AbstractController\OrderLoaderInterface
     */
    private $orderLoader;

    /**
     * @var Registry
     */
    private $registry;

    protected $_storeManager;
    protected $productRepository;
    protected $dataInterface;
    protected $_serialize;
    protected $_objectManager;

    protected $session;

    public function __construct(
      \Magento\Sales\Controller\AbstractController\OrderLoaderInterface  $orderLoader,
      \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
      \Magento\Checkout\Model\Cart $cart, 
      \Magento\Framework\Message\ManagerInterface $messageManager,  
       Registry $registry,
      \Magento\Store\Model\StoreManagerInterface $storeManager,
      \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
      \Magento\Framework\App\ProductMetadataInterface $dataInterface,
      \Magento\Framework\Serialize\Serializer\Serialize $serialize,
      \Magento\Framework\ObjectManagerInterface $objectManager,
      \Magento\Framework\Session\SessionManagerInterface $session
    ) {

        $this->registry = $registry;
        $this->orderLoader = $orderLoader;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->cart = $cart;
        $this->messageManager = $messageManager;
        $this->_storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->dataInterface = $dataInterface;
        $this->_serialize = $serialize;
        $this->_objectManager = $objectManager;
        $this->session = $session;
    }
    public function aroundExecute(
     \Magento\Sales\Controller\AbstractController\Reorder $subject 
    ){
      $result = $this->orderLoader->load($subject->getRequest());
        if ($result instanceof \Magento\Framework\Controller\ResultInterface) {
            return $result;
        }
        $order = $this->registry->registry('current_order');
        $resultRedirect = $this->resultRedirectFactory->create();
        $cart = $this->cart;
        $items = $order->getItemsCollection();
        $this->session->start();
        foreach ($items as $item) {
            try{
                $additionaloptions = $item->getProductOptionByCode('additional_options');
                if($additionaloptions){
                  foreach ($additionaloptions as $itemkey => $itemvalue) {
                      if($itemkey == 0){                        
                          //$_SESSION['curren_design_session'] = $itemvalue['design_id'];
                        $this->session->setItemDesignId($itemvalue['design_id']);
                      }
                  }                  
                }
                $cart->addOrderItem($item);

            }catch (\Magento\Framework\Exception\LocalizedException $e) {
                if ($this->cart->getUseNotice(true)) {
                    $this->messageManager->addNoticeMessage($e->getMessage());
                } else {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
                return $resultRedirect->setPath('*/*/history');
            } catch (\Exception $e) {
                /*$this->messageManager->addExceptionMessage(
                    $e,
                    __('We can\'t add this item to your shopping cart right now.')
                );*/
                 $this->messageManager->addExceptionMessage(
                    $e,
                    __($e->getMessage())
                );
                return $resultRedirect->setPath('checkout/cart');
            }
        }

        $cart->save();
        return $resultRedirect->setPath('checkout/cart');
    }


    protected function serializeData($value) {
        $string = '';
        if (version_compare($this->dataInterface->getVersion(), '2.2.0', '>=')) {
            $string = json_encode($value);
        } else {
            // $string = serialize($value);
            $string = $this->_serialize->serialize($value);
        }
        return $string;
    }

    public function unserializeData($value) {
        $string = '';
        if (version_compare($this->dataInterface->getVersion(), '2.2.0', '>=')) {
            $string = json_decode($value, true);
        } else {
            $string = (isset($value) && $value) ? $this->_serialize->unserialize($value) : '';
            // $string = unserialize($value);
        }
        return $string;
    }

}