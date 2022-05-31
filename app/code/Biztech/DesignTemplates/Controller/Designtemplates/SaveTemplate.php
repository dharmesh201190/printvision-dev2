<?php

/**
 * Copyright © 2017-2018 AppJetty. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Biztech\DesignTemplates\Controller\Designtemplates;

class SaveTemplate extends \Biztech\DesignTemplates\Controller\Designtemplates {

    public function execute() {
        try {
            /*
             * Fetch Params
             */
            $data = json_decode(file_get_contents('php://input'), TRUE);
            $response['status'] = 'success';
            $response = $this->templateHelper->saveProductTemplate($data);
            $this->getResponse()->setBody(json_encode($response));
        } catch (\Exception $e) {
            $response = $this->templateHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }
    }

}
