<?php

namespace Biztech\Productdesigner\Controller\Designs;

class SaveSingleSideData extends \Biztech\Productdesigner\Controller\Designs {

    const canvasDataPath = '/productdesigner/canvasData/';

    public function execute() {
        try {
            /*
             * Fetch Params
             */
            $data = json_decode(file_get_contents('php://input'), TRUE);
            $canvasData = $data['canvasData'];
            $flag = $data['flag'];
            $timestamp = $data['timestamp'];
            $mediaPath = $this->dir->getPath('media');
            $dir = $mediaPath . self::canvasDataPath;
            $filename = $dir . $timestamp . '.txt';
            $generateSVG = false;
            if ($flag == 0) {
                if (file_exists($filename)) {
                    unlink($filename);
                }
                $storeid = $this->_storeManager->getStore()->getId();
                if ($this->pdHelper->getConfig('productdesigner/general/enabled_generateSVG', $storeid) == 0) {
                    $generateSVG = false;
                } else {
                    $generateSVG = true;
                }
            }
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
            $filesize = 0;
            if (file_exists($filename)) {
                $filesize = filesize($filename);
            }
            $storedCanvasData = '';
            if ($filesize > 0) {
                $readMyfile = fopen($filename, "r");
                $storedCanvasData = fread($readMyfile, filesize($filename));
                fclose($readMyfile);
            }
            if (!empty($storedCanvasData)) {
                $storedCanvasDataDecoded = json_decode(base64_decode($storedCanvasData));
                $canvasDataDecoded = json_decode(base64_decode($canvasData));
                $canvasDataMerged = (object) array_merge((array) $storedCanvasDataDecoded, (array) $canvasDataDecoded);
                $canvasDataEncoded = base64_encode(json_encode($canvasDataMerged));
                $finalCanvasData = $canvasDataEncoded;
            } else {
                $finalCanvasData = $canvasData;
            }
            $myfile = fopen($filename, "w");
            fwrite($myfile, $finalCanvasData);
            fclose($myfile);
            $response['status'] = 'success';
            $response['generateSVG'] = $generateSVG;
            $this->getResponse()->setBody(json_encode($response));
        } catch (\Exception $e) {
            $response = $this->_infoHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }
    }

}
