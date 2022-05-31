<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('memory_limit', -1);
ini_set('max_execution_time', 20000);

set_time_limit(0);
error_reporting(E_ALL);

use Magento\Framework\App\Bootstrap;
require '../app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();
$state = $objectManager->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');

$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
$connection = $resource->getConnection();
$tableName = $resource->getTableName('catalog_product_entity_tier_price');

if(isset($_POST['tier_config_products']) && isset($_POST['tier_qty'])){
	$configProductIds = $_POST['tier_config_products'];
	$tierQty = $_POST['tier_qty'];
	foreach($configProductIds as $configProductId)
	{
		$configProduct = $objectManager->create('Magento\Catalog\Model\Product')->load($configProductId);
		$children = $configProduct->getTypeInstance()->getUsedProducts($configProduct);
		foreach ($children as $child){
			$childId = $child->getId(); 
			$sql = " DELETE FROM " . $tableName . " WHERE qty IN(".$tierQty.") AND entity_id =" .$childId;
			$connection->query($sql);
		}
	}
	echo "<b>Removed Tier Price Products Count : </b>".count($children);
}
?>
<a href="https://www.kingofflags.co.uk/deletetier.php">Go Back</a>
