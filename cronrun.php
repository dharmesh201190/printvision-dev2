<?php

/**
 * This is wrapper class in which all the functions are called from angular.
 * We have implemented caching over here so it will speed up tool user experience.
 *
 *  PHP version 5
 *
 * LICENSE: This source file is subject to version 1.0 with our BYI tool implementation
 *
 * @ByiDesigner   Main class
 * @$_baseUrl    we will need to change base URL when we are installing extension on any client website.
 * @author     Original Author <admin@biztechcs.com>
 * @author     Another Author <admin@biztechcs.com>
 * @version    1.0
 * @Date : 01/06/2017
 *
 */
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

/**
 * @ByiDesigner : Build class for Mage
 * Date : 12/07/2017
 * By : U V
 *
 */
use Magento\Framework\App\Bootstrap;

/**
 * If your external file is in root folder
 */
require __DIR__ . '/app/bootstrap.php';

ini_set('display_errors', 1);

$params = $_SERVER;
$bootstrap = Bootstrap::create(BP, $params);
$objectManager = $bootstrap->getObjectManager();
$state = $objectManager->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');
$objectManager->create('Biztech\Productdesigner\Cron\GenerateImages')->execute();
/*$objectManager->create('Biztech\Productdesigner\Cron\GenerateProductTemplateImages')->execute();
$objectManager->create('Biztech\Productdesigner\Cron\GeneratePhotoBookPdf')->execute();
 */
// $objectManager->create('Biztech\Marketplace\Cron\ImportProduct')->execute();
// $objectManager->create('Biztech\Marketplace\Cron\Assignproduct')->execute();
