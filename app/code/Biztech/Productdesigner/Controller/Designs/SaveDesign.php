<?php
namespace Biztech\Productdesigner\Controller\Designs;

class SaveDesign extends \Biztech\Productdesigner\Controller\Designs {

    protected $productId;
    public function execute() {
        try {
            $data = json_decode(file_get_contents('php://input'), TRUE);
            if (isset($data['title']) && !empty($data['title'])) {
                $designId = $this->isTitleExists($data['title']);
                if ($designId != null) {
                    $response = array(
                        'status' => 'success',
                        'message' => 'success',
                        'titleExists' => true,
                        'design_id' => $designId
                    );
                    $this->getResponse()->setBody(json_encode($response));
                } else {
                    $this->saveDesign();
                }
            } else {
                $this->saveDesign();
            }
        } catch (\Exception $e) {
            $response = $this->_infoHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }
    }

    public function saveDesign() {
        // Fetch Params
        $data = json_decode(file_get_contents('php://input'), TRUE);
        $allCartData = isset($data['cartData']) ? $data['cartData'] : '';
        $this->productId = ($data['product_id']) ?: '';

        // Cart popup may have multiple combination so that we need to update canvas json and data url and all the rows need to be save as individual
        if ($allCartData) {
            foreach ($allCartData as $cartData) {
                // This function will update the data url and canvas to json
                list($associatedMediaImage, $newDataURL, $newCanvasJSON, $updatedImageDesignIds) = $this->processCartData($cartData['productId'], $data);
                $response[] = $this->saveProductDesign($data, $cartData['productId'], $associatedMediaImage, $newDataURL, $newCanvasJSON, $updatedImageDesignIds, $cartData);
            }
        } else {
            $associated_product_id = isset($data['associated_product_id']) ? $data['associated_product_id'] : '';

            $response[] = $this->saveProductDesign($data, $associated_product_id, null, null, null, null, null);
        }
        $response['status'] = 'success';
        $this->getResponse()->setBody(json_encode($response));
    }

    private function saveProductDesign($data, $associated_product_id, $associatedMediaImage = null, $newDataURL = null, $newCanvasJSON = null, $updatedImageDesignIds = null, $cartData = null) {
        $dataUrls = $newDataURL ? $newDataURL : $data['dataUrl'];
        $canvasJson = $newCanvasJSON ? $newCanvasJSON : $data['canvasJson'];
        $product_image_data = $associatedMediaImage ? $associatedMediaImage : $data['product_image_data'];
        $sidesAndParentImageIDs = isset($data['sidesAndParentImageIDs']) ? $data['sidesAndParentImageIDs'] : '';
        $title = isset($data['title']) ? $data['title'] : '';
        $customerId = isset($data['customer_id']) ? $data['customer_id'] : '';
        $prices = '';
        $cartQty = (isset($data['qty'])) ? $data['qty'] : 1;
        if (isset($data['prices'])) {
            $qty = ($cartData && isset($cartData['qty'])) ? $cartData['qty'] : $cartQty;
            $prices = array('objPrices' => $data['prices'], 'qty' => $qty);
            if (isset($data['customOptionsPrice'])) {
                $prices['customOptionsPrice'] = $data['customOptionsPrice'];
            }
            if (isset($data['additional_price'])) {
                $prices['additional_price'] = $data['additional_price'];
            }
            $prices = base64_encode(json_encode($prices));
        }
        $scaleFactor = ($data['scaleFactor']) ?: '';
        $canvas_dataurl_file = isset($data['canvas_dataurl_file']) ? $data['canvas_dataurl_file'] : '';
        $customer_comments = isset($data['customer_comments']) ? $data['customer_comments'] : '';
        if ($this->productId) {
            /*
             * Convert relative path to absolute path
             */
            $product_image_path = $this->_infoHelper->convertRelToAbsPath($product_image_data['url']);
            $product_image_data['path'] = $product_image_path;
            /**
             * Save canvas objects data into design table
             */

            $params = array(
                'title' => $title,
                'product_id' => $this->productId,
                'associated_product_id' => $associated_product_id,
                'customer_id' => $customerId,
                'prices' => $prices,
                'json_objects' => base64_decode($canvasJson),
                'scale_factor' => $scaleFactor,
                'canvas_dataurl_file' => $canvas_dataurl_file,
                'customer_comments' => $customer_comments,
                'relative_image_id' => json_encode($updatedImageDesignIds),
                'parent_image_id' => json_encode($sidesAndParentImageIDs[1]),
                'is_display' => !empty($title) ? 1 : 0
            );
            $designId = $this->_infoHelper->saveDesign($params);
            if (isset($cartData['additional_options']) || (isset($data['additionalOptions']) && isset($data['additionalOptions']['nameNumber']))) {
                $additionalOptions = (isset($cartData['additional_options'])) ? $cartData['additional_options'] : $data['additionalOptions'];
                $this->_eventManager->dispatch(
                    'design_save_after', ['design_id' => $designId, 'additional_options' => $additionalOptions]
                );
            }
            /**
             * Generate image in designs folder
             */
            $type = array('path' => 'designs',
                'id' => $designId);
            $response['images'] = $this->_infoHelper->generateImage($dataUrls, $product_image_data, $type);
            $response['designId'] = $designId;
            $response['title'] = $title;
            $response['image_id'] = $product_image_data['image_id'];
            /**
             * Save generated image paths in table
             */
            $this->_infoHelper->saveGenerateImages($response);
        }
        return $response;
    }
    
    private function processCartData($productId, $data) {

        $dataUrls = isset($data['dataUrl']) ? $data['dataUrl']: array();
        $canvasJson = ($data['canvasJson']) ?: '';
        $canvasToJson = json_decode(base64_decode($canvasJson), true);
        $sidesAndParentImageIDs = isset($data['sidesAndParentImageIDs']) ? $data['sidesAndParentImageIDs'] : '';
        /**
         * Load product and get Media gallery images of Product
         */
        $response = $this->_infoHelper->getProductTypeAndMediaImages($productId);
//        $productImages = $response['media_image'];
        $productImages = $this->_infoHelper->getSimpleProductDimensionsData($response['product'], $this->productId);
        /**
         * Loop for data urls of all dimensions for updating design area if multiple product are in a queue
         */
        $index = 0;
        $associatedMediaImage = array();
        $newDataURL = array();

        try {
            foreach ($dataUrls as $imageIdWithDesignArea => $dataUrl) {
            /**
             * Fetch image id from current canvas to data url
             */
            $imageIdWithDesignAreasArray = explode("&", $imageIdWithDesignArea);
            $imageIds = $imageIdWithDesignAreasArray[0];
            $imageIdArray = explode('@', $imageIds);
            $imageId = $imageIdArray[1];
            $designAreaId = $imageIdWithDesignAreasArray[1];
            /**
             * Fetch associated product image from image side
             */
            $param['imageId'] = $imageId;
            $param['sidesAndParentImageIDs'] = $sidesAndParentImageIDs;
            $param['productImages'] = $productImages;
            $param['productId'] = $productId;
            $mediaImage = $this->findImageFromProduct($param);
            $associatedMediaImage = $mediaImage;
            $dimensionIndex = 0;
            foreach ($mediaImage['dim'] as $key => $value) {
                if ($dimensionIndex == $index) {
                    $newDataURL['@' . $value['image_id'] . '&' . $value['designareaId']] = $dataUrl;
                }
                $dimensionIndex++;
            }
            $index++;
        }
    } catch(\Exception $e) {
        $response = array('status' => 'failure', 'log' => "Seems like designed product and chosen product does not match!", 'message' => "Seems like designed product and chosen product does not match!");
        $this->getResponse()->setBody(json_encode($response));
    }

        /**
         * Loop for data canvasJson of all dimensions for updating design area if multiple product are in a queue
         */
        $updatedImageDesignIds = array();
        $newCanvasJSON = array();
        $canvasJsonKeys = array();
        foreach (array_keys($canvasToJson) as $key) {
            $imageIdWithDesignAreasArray = explode("&", $key);
            $imageIds = $imageIdWithDesignAreasArray[0];
            $imageIdArray = explode('@', $imageIds);
            $imageId = $imageIdArray[1];
            $designAreaId = $imageIdWithDesignAreasArray[1];
            if (!isset($canvasJsonKeys[$imageId])) {
                $canvasJsonKeys[$imageId] = [];
            }
            $canvasJsonKeys[$imageId][] = $designAreaId;
        }
        foreach ($canvasJsonKeys as $imageId => $designAreas) {
            $param = array();
            $param['imageId'] = $imageId;
            $param['sidesAndParentImageIDs'] = $sidesAndParentImageIDs;
            $param['productImages'] = $productImages;
            $param['productId'] = $productId;
            $canvasJSONAssociatedImage = $this->findImageFromProduct($param);
            if(isset($canvasJSONAssociatedImage['dim'])){
                foreach ($canvasJSONAssociatedImage['dim'] as $index => $dim) {
                    if(!isset($designAreas[$index])){
                        continue;
                    }
                    $designareaId = $dim['designareaId'];
                    $json = $canvasToJson['@' . $imageId . '&' . $designAreas[$index]];
                    $newCanvasJSON['@' . $canvasJSONAssociatedImage['image_id'] . '&' . $designareaId] = $json;
                    $updatedImageDesignIds['@' . $imageId . '&' . $designAreas[$index]] = '@' . $canvasJSONAssociatedImage['image_id'] . '&' . $designareaId;
                }
            }
        }
        return array($associatedMediaImage, $newDataURL, base64_encode(json_encode($newCanvasJSON)), $updatedImageDesignIds);
    }

    private function findImageFromProduct($param) {
        $imageId = $param['imageId'];
        $sides = $param['sidesAndParentImageIDs'][0];
        $productImages = $param['productImages'];
        $imageSide = $sides[$imageId];
        $associatedMediaImage = array();
        foreach ($productImages as $productImage) {
            if ($productImage['image_side'] == $imageSide) {
                $associatedMediaImage = $productImage;
                break;
            }
        }
        return $associatedMediaImage;
    }

    public function isTitleExists($designTitle) {
        $designId = null;
        if ($this->session->isLoggedIn()) {
            $customerid = $this->session->getCustomer()->getId();
            $designCollection = $this->designCollectionFactory->create()
            ->addFieldToFilter('customer_id', $customerid)
            ->addFieldToFilter('title', $designTitle);
            $totalRecords = count($designCollection->getData());
            if ($totalRecords > 0) {
                $designId = $designCollection->getData()[0]['design_id'];
            }
        }

        return $designId;
    }

}
