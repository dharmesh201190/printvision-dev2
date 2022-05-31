<?php

namespace Biztech\Productdesigner\Block\Cart\Item\Renderer\Actions;

class Edit extends \Magento\Checkout\Block\Cart\Item\Renderer\Actions\Edit {

    public function getConfigureUrl() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->create('\Magento\Store\Model\StoreManagerInterface');
        $params = $this->getItem()->getProduct()->getCustomOptions();
        foreach ($params as $key => $pram) {
            foreach ($params as $key => $pram) {
                $design_id = '';
                if ($key == 'additional_options') {
                    $designData = $pram->getData();
                    $itemId = base64_encode($pram->getItemId());
                    $designdata1 = $objectManager->create('Biztech\Productdesigner\Helper\Data')->unserializeData($designData['value']);
                    foreach ($designdata1 as $dData) {
                        if (isset($dData['code']))
                            if ($dData['code'] == 'product_design') {
                            $design_id = base64_encode($dData['design_id']);
                        }
                    }
                }
            }
        }
        if ($design_id != '') {
            return $storeManager->getStore()->getBaseUrl() . 'productdesigner/index/index/id/' . $this->getItem()->getProduct()->getId() . '/design/' . $design_id . '/item/' . $itemId;
        } else {
            return $this->getUrl(
                            'checkout/cart/configure', [
                        'id' => $this->getItem()->getId(),
                        'product_id' => $this->getItem()->getProduct()->getId()
                            ]
            );
        }
    }
}
