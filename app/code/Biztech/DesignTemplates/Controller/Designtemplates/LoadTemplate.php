<?php

/**
 * Copyright © 2017-2018 AppJetty. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Biztech\DesignTemplates\Controller\Designtemplates;

class LoadTemplate extends \Biztech\DesignTemplates\Controller\Designtemplates {
    public function execute() {
        try {
            /*
             * Fetch Params
             */
            $data = json_decode(file_get_contents('php://input'), TRUE);
            $templateId = ($data['templateId']) ? base64_decode($data['templateId']) : '';
            $response = array();
            if ($templateId) {
                $response = $this->templateHelper->loadTemplate($templateId);
                $response['status'] = 'success';
            }
            $this->getResponse()->setBody(json_encode($response));
        } catch (\Exception $e) {
            $response = $this->templateHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }
    }

}
