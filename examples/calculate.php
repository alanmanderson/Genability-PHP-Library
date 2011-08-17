<?
// set the default timezone for php date methods <http://www.php.net/manual/en/function.date-default-timezone-set.php>
date_default_timezone_set('America/Los_Angeles');

// default fromDateTime and toDateTime set to the first of this month and next month
$fromDateTime = '2011-'.date('m').'-01T00:00:00.0-0700';
$toDateTime = '2011-'.date('m', mktime(0, 0, 0, date("m")+1, date("d"), date("Y"))).'-01T00:00:00.0-0700';

// set the price paramters to whatever is sent via POST
// othwerwise, set params to default
if ($_POST['tariff']) {
	$TARIFF_ID = $_POST['tariff'];
} else {
	$TARIFF_ID = '512';
}

if ($_POST['fromDateTime']) {
	$FROM_DATE_TIME = $_POST['fromDateTime'];
} else {
	$FROM_DATE_TIME = $fromDateTime;
}

if ($_POST['toDateTime']) {
	$TO_DATE_TIME = $_POST['toDateTime'];
} else {
	$TO_DATE_TIME = $toDateTime;
}


/** include the Genability PHP Library */
require_once('../genability.php');

// set your app id and app key
$gen = new genability(array(
  'app_id'  => 'your-app-id-here',    // Your Unique Genability Application ID <https://developer.genability.com/admin/applications>
  'app_key' => 'your-app-key-here',   // Your Unique Genability Application Key <https://developer.genability.com/admin/applications>
  //'debug'   => true,                // Debug mode echos API Url & POST data if set to true (Optional)
));

// if tariffInputs are sent through POST, make the calculate call
// otherwise, get the metadata parameters
if ($_POST['tariffInputs']) {
	$output = $gen->getCalculation(array(
	  'masterTariffId'=> $TARIFF_ID,               // Unique Genability ID (primary key) for this tariff
	  'fromDateTime'  => $FROM_DATE_TIME,          // Starting date and time for this Calculate request. (Required)
	  'toDateTime'    => $TO_DATE_TIME,            // End date and time for this Calculate request. (Required)
	  'territoryId'   => $_POST['territoryId'],    // The territory ID of where the usage consumption occurred. (Optional)
	  'tariffInputs'  => $_POST['tariffInputs'],   // The input values to use when running the calculation. (Array)
	  
	));
} elseif ($_POST['tariff']) {
	$output = $gen->getCalculateInputs(array(
	  'masterTariffId'=> $TARIFF_ID,              // Unique Genability ID (primary key) for this tariff
	  'fromDateTime'  => $FROM_DATE_TIME,         // Starting date and time for this Calculate request. (Required)
	  'toDateTime'    => $TO_DATE_TIME,           // End date and time for this Calculate request. (Required)
	  'territoryId'   => $_POST['territoryId'],   // The territory ID of where the usage consumption occurred. (Optional)
	));
}

$c = json_decode($output, true);

// helper method to display text easier (lowercase and without _s)
function formatText($input) {
	return strtolower(str_replace("_", " ", $input));
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Genability API PHP Library :: Examples :: calculate.php</title>
	<link rel="stylesheet" href="../static/genability_php_library.css">
	<link rel="stylesheet" href="../static/cupertino/jquery-ui-1.8.15.custom.css">
	<script src="../static/jquery-1.6.2.min.js" type="text/javascript"></script>
	<script src="../static/jquery-ui-1.8.15.custom.min.js" type="text/javascript"></script>
	<script src="../static/genability_php_library.js" type="text/javascript"></script>
</head>
<body>
<div id="genabilityExample">
	<div id="powered_by_genability">
		<h1 id="genability"><a href="http://genability.com/" target="_blank">Powered by Genability</a></h1>
		<p><a href="https://developer.genability.com/documentation/api-reference/pricing/calculate" target="_blank">Calculate Documentation</a></p>
	</div>
	<h3 class="nav">Genability API PHP Library :: Examples :: <a href="tariff.php">Tariff</a> | <a href="price.php">Price</a> | <a href="calculate.php">Calculate</a></h3>
	<h2>Calculate Example</h2>
	<form id="tariffInputs" action="<?=$_SERVER['PHP_SELF']?>" method="POST">
		<div class="inputBlock">
			<label for="tariff">Master Tariff Id</label>
			<input type="text" name="tariff" value="<?=$TARIFF_ID?>"/>
		</div>
		<div class="inputBlock">
			<label for="quantity_key">Consumption or Demand</label>
			<select class="quantity_key">
				<option value="consumption">consumption (kwh)</option>
				<option value="demand">demand (kw)</option>
			</select>
		</div>
		<div class="inputBlock">
			<label for="fromDateTime">From Date</label>
			<input type="text" name="fromDateTime" value="<?=$fromDateTime?>"/>
		</div>
		<div class="inputBlock">
			<label for="toDateTime">To Date</label>
			<input type="text" name="toDateTime" value="<?=$toDateTime?>"/>
		</div>
		<div class="inputBlock">
			<label for="territoryId">Territory Id</label>
			<input type="text" name="territoryId" value="<?=$_POST['territoryId']?>"/>
		</div>

<div id="showInputs">
	<label>Show Inputs</label>
	<input type="button" id="metadata" value="Get Metadata"/>
	<input type="button" id="one" value="One Input"/>
	<input type="button" id="months" value="Months"/>
	<input type="button" id="days" value="Days"/>
	<input type="button" id="hours" value="Hours"/>
</div>

<div id="easyInput"<?if ($c["status"] == "success" && $c["type"] == "TariffInput") { ?>style="display: block;"<? } ?>>
	<label for="fillTheRest">Easy Input</label>
	<input type="text" name="fillTheRest"/> <a href="#fillAll">fill all values</a>
</div>

<div id="easyHourInputs">
	<div class="hourCol">
	<label>Easy Hour Inputs</label><a href="#fillHours">fill hours</a>
	<div class="inputBlock">
	<label>00:00</label><input type="text" name="hour[0]"/>
	</div>
	<div class="inputBlock">
	<label>01:00</label><input type="text" name="hour[1]"/>
	</div>
	<div class="inputBlock">
	<label>02:00</label><input type="text" name="hour[2]"/>
	</div>
	<div class="inputBlock">
	<label>03:00</label><input type="text" name="hour[3]"/>
	</div>
	<div class="inputBlock">
	<label>04:00</label><input type="text" name="hour[4]"/>
	</div>
	<div class="inputBlock">
	<label>05:00</label><input type="text" name="hour[5]"/>
	</div>
	<div class="inputBlock">
	<label>06:00</label><input type="text" name="hour[6]"/>
	</div>
	<div class="inputBlock">
	<label>07:00</label><input type="text" name="hour[7]"/>
	</div>
	<div class="inputBlock">
	<label>08:00</label><input type="text" name="hour[8]"/>
	</div>
	<div class="inputBlock">
	<label>09:00</label><input type="text" name="hour[9]"/>
	</div>
	<div class="inputBlock">
	<label>10:00</label><input type="text" name="hour[10]"/>
	</div>
	<div class="inputBlock">
	<label>11:00</label><input type="text" name="hour[11]"/>
	</div>
	</div>
	<div class="hourCol right">
	<div class="inputBlock">
	<label>12:00</label><input type="text" name="hour[12]"/>
	</div>
	<div class="inputBlock">
	<label>13:00</label><input type="text" name="hour[13]"/>
	</div>
	<div class="inputBlock">
	<label>14:00</label><input type="text" name="hour[14]"/>
	</div>
	<div class="inputBlock">
	<label>15:00</label><input type="text" name="hour[15]"/>
	</div>
	<div class="inputBlock">
	<label>16:00</label><input type="text" name="hour[16]"/>
	</div>
	<div class="inputBlock">
	<label>17:00</label><input type="text" name="hour[17]"/>
	</div>
	<div class="inputBlock">
	<label>18:00</label><input type="text" name="hour[18]"/>
	</div>
	<div class="inputBlock">
	<label>19:00</label><input type="text" name="hour[19]"/>
	</div>
	<div class="inputBlock">
	<label>20:00</label><input type="text" name="hour[20]"/>
	</div>
	<div class="inputBlock">
	<label>21:00</label><input type="text" name="hour[21]"/>
	</div>
	<div class="inputBlock">
	<label>22:00</label><input type="text" name="hour[22]"/>
	</div>
	<div class="inputBlock">
	<label>23:00</label><input type="text" name="hour[23]"/>
	</div>
	</div>
</div>

<label for="hiddenInputBecauseImLazy">&nbsp;</label>
<input type="submit" value="Calculate!" class="letsCalculate"/>

<hr/>

<div id="generatedInputs"></div>

<?if ($c["status"] == "success" && $c["type"] == "TariffInput") { ?>
<table id="metadataInputs" class="pretty_blue_table">
	<tr>
		<th>key</th>
		<th>fromDateTime</th>
		<th>toDateTime</th>
		<th>value</th>
		<th>unit</th>
		<th>accuracy</th>
	</tr>
<?
	//foreach ($c["results"] as $r) {
	for ($i = 0; $i < sizeof($c["results"]) ; $i++) { ?>
	
	<tr>
		<td><?=$c["results"][$i]["key"]?><input type="hidden" name="tariffInputs[<?=$i?>][key]" value="<?=$c[results][$i][key]?>"/></td>
		<td><?=date("n/j/y g:i a", strtotime($c["results"][$i]["fromDateTime"]))?><input type="hidden" name="tariffInputs[<?=$i?>][fromDateTime]" value="<?=$c[results][$i][fromDateTime]?>"/></td>
		<td><?=date("n/j/y g:i a", strtotime($c["results"][$i]["toDateTime"]))?><input type="hidden" name="tariffInputs[<?=$i?>][toDateTime]" value="<?=$c[results][$i][toDateTime]?>"/></td>
		<td><input type="text" name="tariffInputs[<?=$i?>][value]" class="tariffValue"/></td>
		<td><?=$c["results"][$i]["unit"]?><input type="hidden" name="tariffInputs[<?=$i?>][unit]" value="<?=$c[results][$i][unit]?>"/></td>
		<td><?=$c["results"][$i]["accuracy"]?></td>
	</tr>
<?	}
?>
</table>
<? } ?>
</form>

<a id="toggleResponse" href="#">view/hide response</a><div id="json_resp"><?=var_dump(json_decode($output, true));?></div>

<? if ($c["status"] == "success" && $c["type"] == "CalculatedCost") {
	for ($i=0; $i<sizeof($c["results"]); $i++) { ?>
	<div class="tariff_info">
		<p><label>Master Tariff Id</label><?=$c["results"][$i]["masterTariffId"]?></p>
		<p><label>Tariff Name</label><?=$c["results"][$i]["tariffName"]?></p>
		<p><label>Timespan</label>From <?=date("D M j, Y g:i a", strtotime($c["results"][$i]["fromDateTime"]))?> to <?=date("D M j, Y g:i a", strtotime($c["results"][$i]["toDateTime"]))?></p>
		<p><label>Total Cost</label><strong><?=$c["results"][$i]["totalCost"]?></strong></p>
	</ul>
	<table class="cost_breakdown pretty_blue_table">
		<thead>
			<tr>
				<th>Rate Id</th>
				<th>Rate Band Id</th>
				<th>Rate Group Name</th>
				<th>Rate Name</th>
				<th>From Date</th>
				<th>To Date</th>
				<th>Rate Type</th>
				<th>Quantity Key</th>
				<th>Quantity</th>
				<th>Count</th>
				<th>Cost</th>
			</t>
		</thead>
		<tbody><?foreach ($c["results"][$i]["items"] as $ci) {?>
			<tr>
				<td><?=$ci["tariffRateId"]?></td>
				<td><?=$ci["tariffRateBandId"]?></td>
				<td><?=$ci["rateGroupName"]?></td>
				<td><?=$ci["rateName"]?></td>
				<td><?=date("n/j/y g:i a", strtotime($ci["fromDateTime"]))?></td>
				<td><?=date("n/j/y g:i a", strtotime($ci["toDateTime"]))?></td>
				<td><?=formatText($ci["rateType"])?></td>
				<td><?=$ci["quantityKey"]?></td>
				<td><?=$ci["itemQuantity"]?></td>
				<td><?=$ci["itemCount"]?></td>
				<td><?=$ci["cost"]?></td>
			</tr>
		<?}?></tbody>
	</table>
	<? }
} else if ($c["type"] == "Error" && $c["results"][0]["objectName"] == "request") { ?>
	<p class="error">Please enter a valid app id and app key, you can grab a pair here: <a href="https://developer.genability.com/admin/applications">https://developer.genability.com/admin/applications</a>
<? } else if ($c["type"] == "Error") {
	echo '<p class="error">Error occured in calculate() method :<br/><br/>';
	foreach ($c["results"] as $errors) {
		echo $errors['objectName'] . ' ' . $errors['code'] . '  ' . $errors['message'] . '<br/>';
	}
	echo '</p>';
} ?>
</div>
</body>
</html>