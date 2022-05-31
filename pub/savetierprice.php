<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('memory_limit', -1);
ini_set('max_execution_time', 20000);
/* ini_set('max_input_vars', '20000');
ini_set('set_time_limit', '0'); */
set_time_limit(0);
error_reporting(E_ALL);

use Magento\Framework\App\Bootstrap;
require '../app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);

$objectManager = $bootstrap->getObjectManager();

$state = $objectManager->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');
if(isset($_POST['tier_config_products']) && isset($_POST['tier_qty']) && isset($_POST['tier_price']))
{
	try{ ?>
		<div style="text-align: center;">
			<h2 style="color: green">Succefully Add/Updated Tier Price to Below Associated Products </br>of You Selected Configurable Products.</h2>
		<?php 
		$configProductIds = $_POST['tier_config_products'];

		$tierQty = explode(",",$_POST['tier_qty']);
		$tierPrice = explode(",",$_POST['tier_price']);

		$combimeQtyPrices = array_combine( $tierQty, $tierPrice );

		$tierPrices = array();
		foreach($combimeQtyPrices as $key => $value){
			$tierPrices[] = array(
			     'website_id'  => 0,
			     'cust_group'  => 0,
			     'percentage_value'  => $value,
			     'price_qty'   => $key
		    );
		}



		foreach($configProductIds as $configProductId)
		{
			$configProduct = $objectManager->create('Magento\Catalog\Model\Product')->load($configProductId);
			$children = $configProduct->getTypeInstance()->getUsedProducts($configProduct);
			foreach ($children as $child){
				$product = $objectManager->create('Magento\Catalog\Model\Product')->load($child->getId());

					$tierPrices = array();
					foreach($combimeQtyPrices as $key => $value){
						$tierPrices[] = array(
						     'website_id'  => 0,
						     'cust_group'  => 0,
						     'percentage_value'  => $value,
						     'price_qty'   => $key
					    );
					}

					foreach ($product->getTierPrice() as $tp) {
						if(!in_array($tp['price_qty'],$tierQty) && $tp['percentage_value'] != '')
						{
							$tierPrices[] = array(
							     'website_id'  => 0,
							     'cust_group'  => 0,
							     'percentage_value'  => $tp['percentage_value'],
							     'price_qty'   => $tp['price_qty']
						    );
						}
						if(!in_array($tp['price_qty'],$tierQty) && $tp['percentage_value'] == '')
						{
							$tierPrices[] = array(
							     'website_id'  => 0,
							     'cust_group'  => 0,
							     'price'  => $tp['price'],
							     'price_qty'   => $tp['price_qty']
						    );
						}
					}
			    $product->setTierPrice($tierPrices);

			    $product->save();
			}?>
			
				<?php echo "<b>Configurable Product ID : </b>".$configProduct->getId();?></br>
				<?php echo "<b>Configurable Product Name : </b>".$configProduct->getName();?></br>
				<?php echo "<b>Configurable Associated Products Count : </b>".count($children);?></br></br>
				
			<?php
			//echo "Product Id: ".$configProduct->getId() . " -- count: ".count($children)."<br/>";
			//die();
		}?>
			<a href="https://www.printvision.co.uk/tierprice.php">Go Back</a>
		</div> <?php
	}catch(\Exception $e){
	   echo "Message: ".$e->getMessage();exit;     
	}
} else {
	echo "The origin web server timed out responding to this request";
}

?>
