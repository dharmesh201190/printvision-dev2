<?php

namespace Biztech\NameNumber\Observer;

use Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Serialize\Serializer\Serialize;

class catalogProductLoadAfter implements ObserverInterface {

    // variables
    protected $designFactory;
    protected $_request;
    protected $dataInterface;
    protected $_serialize;
   
    public function __construct(
        \Magento\Framework\App\Request\Http $request, \Biztech\Productdesigner\Model\DesignsFactory $designFactory, \Magento\Framework\App\ProductMetadataInterface $dataInterface, Serialize $serialize
    ) {
        $this->_request = $request;
        $this->designFactory = $designFactory;
        $this->dataInterface = $dataInterface;
        $this->_serialize = $serialize;
    }

    
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $data = json_decode(file_get_contents('php://input'), TRUE);
        if (isset($data)) {
            $post = $this->_request->getPostValue();
            $designId = $post['designId'][0];
            $designObj = $this->designFactory->create()->load($designId);
            $nameNumberData = json_decode($designObj->getNameNumberDetails(), true);
            if ($nameNumberData && count($nameNumberData) > 0) {
                $item = $observer->getQuoteItem();
                $namesAndnumbersString = array();
                $namesAndnumbers = '';
                $nameNumberArr = array();
                foreach ($nameNumberData as $nameNumberObj) {
                    $nameNumberArr = array();
                    foreach ($nameNumberObj as $key => $value) {
                        if ($key != 'id') {
                            if (in_array(ucwords($key), $namesAndnumbersString, true) == false) {
                                if (count($namesAndnumbersString) != 3)
                                    array_push($namesAndnumbersString, ucwords($key));
                            }
                            array_push($nameNumberArr, $value);
                        }
                    }
                    $namesAndnumbers .= implode(" / ", $nameNumberArr);
                    $namesAndnumbers .= "<br>";
                }
                $namesAndnumbersString = implode(" / ", $namesAndnumbersString);
                if ($additionalOption = $item->getOptionByCode('additional_options')) {
                    $additionalOptions = json_decode($additionalOption->getValue(), TRUE);
                }
                $additionalOptions[] = array(
                    'product_id' => $data['productId'],
                    'code' => 'name_numbers',
                    'label' => $namesAndnumbersString,
                    'design_id' => $designId,
                    'value' => $namesAndnumbers,
                    'custom_view' => false
                );

                $optionData = array(
                    'product_id' => $data['productId'],
                    'code' => 'additional_options',
                    'label' => 'Product Design',
                    'value' => $this->serializeData($additionalOptions)
                );

                $item->addOption($optionData);
            }
        }
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

}
