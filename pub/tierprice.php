<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('memory_limit', -1);
ini_set('max_execution_time', 0);
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

$ConfigProductCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection');
$ConfigProductCollection->addFieldToSelect('name');
$ConfigProductCollection->addAttributeToFilter('type_id', array('eq' => 'configurable'));
?>

<h2 style="text-align: center;">Tier Price Form</h2>
<form name="tierprice" class="tierprice-form" id="tierprice-form" method="post" action="#" style="text-align: center;">
	<label style="font-weight: 600; line-height: 30px; font-size: 17px;">Select Configurable Products</label></br>
	<select name="tier_config_products[]" id="multipleSelect" multiple style="padding: 8px; height: 150;">
		<?php foreach($ConfigProductCollection as $configProduct){ ?>
	  		<option value="<?php echo $configProduct->getId()?>"><?php echo $configProduct->getName()?></option>
	  	<?php }?>
	</select></br>

	<label style="font-weight: 600; line-height: 30px;">Qty</label></br>
	<input style="line-height: 20px; width: 355px;" id="tier_qty" type="text" name="tier_qty" value="" placeholder=" For ex. 2,5,10,15"></br>

 	<label style="font-weight: 600; line-height: 30px;">Discount %</label></br>
  	<input style="line-height: 20px; width: 355px;" id="tier_price" type="text" name="tier_price" value="" placeholder=" For ex. 4,8,12,16"></br>
  	</br>
	<input style="padding: 8px;" class="submit-tierprice" type="submit" value="Submit">
</form>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script type="text/javascript">
	jQuery(".submit-tierprice").click(function(e){

		if($('#multipleSelect').val() == ''){
			e.preventDefault();
			alert('Please select the Configurable Products');
		}
		
		if($('#tier_qty').val() == ''){
			e.preventDefault();
			alert('Please Enter the Qty');
		}
		
		if($('#tier_price').val() == ''){
			e.preventDefault();
			alert('Please Enter the Price');
		}

		if($('#multipleSelect').val() != '' && $('#tier_qty').val() != '' && $('#tier_price').val() != '')
		{
			$('#tierprice-form').attr('action','savetierprice.php');
		}
	});

</script>
