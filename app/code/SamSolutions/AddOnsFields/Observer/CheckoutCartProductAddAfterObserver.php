<?php

namespace SamSolutions\AddOnsFields\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class CheckoutCartProductAddAfterObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    protected $_request;

    /**
     * @var LoggerInterface
     */

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\View\LayoutInterface    $layout
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\App\RequestInterface $request,
        LoggerInterface $logger
    ) {
        $this->_layout = $layout;
        $this->_storeManager = $storeManager;
        $this->_request = $request;
        $this->logger = $logger;
    }

    /**
     * Add order information into GA block to render on checkout success pages
     *
     * @param EventObserver $observer
     *
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        /* @var \Magento\Quote\Model\Quote\Item $item */
        $item = $observer->getQuoteItem();
        $additionalOptions = [];
        if ($additionalOption = $item->getOptionByCode('additional_options')) {
            $additionalOptions = (array)json_decode($additionalOption->getValue());
        }
        $post = $this->_request->getParam('addons');
        if (is_array($post)) {
            foreach ($post as $key => $value) {
                if ($key == '' || $value == '' || $value == 0) {
                    continue;
                }
                $additionalOptions[] = [
                    'label' => 'Add-Ons ' . $key . 'mm',
                    'value' => $value . 'pcs',
                ];
            }

            if (count($additionalOptions) > 0) {
                $item->addOption([
                    'product_id' => $item->getProductId(),//Missing data
                    'code'       => 'additional_options',
                    'value'      => json_encode($additionalOptions),
                ]);
            }
        }
        /* To Do */
        // Edit Cart - May need to remove option and readd them
        // Pre-fill remarks on product edit pages
        // Check for comparability with custom option
    }
}
