<?php

function makeWidget() {
	require_once("TheYachtMarket-LiveFeedback.php");

//Create SOAP client.
	$client = new nusoap_client($liveFeedbackApiUrl, "wsdl");
	$manufacturers = '';

//Set encoding to UTF-8 (may have to add or remove these lines depending on the server (Windows or Linux) to display pound or euro symbols).
	$client->soap_defencoding = "UTF-8";
	$client->decode_utf8 = false;

	$err = $client->getError();
	if ($err) {
	//Something went wrong - uncomment code below to debug.
	//echo "<h2>Error at $client->getError()</h2><pre>";
	//print_r($result);
	//echo "</pre>";

	//Show friendly message to user.
		$errorMessage = $liveFeedbackFetchError;
	}
	else {
		$param = array("apiKey" => $liveFeedbackApiKey, "onlyBoatsMarkedForExport" => $liveFeedbackOnlyBoatsMarkedForExport);
		$result = $client->call("GetLiveManufacturers", $param);

		if ($client->fault) {
		//Something went wrong - uncomment code below to debug.
		//echo "<h2>Error at $client->fault</h2><pre>";
		//print_r($result);
		//echo "</pre>";

		//Show friendly message to user.
			$errorMessage = $liveFeedbackFetchError;

		} else {
		//Uncomment the line below for debugging.
		//showWsdl($result);

		//Put list of manufacturers into array (how deep NuSoap nests the array depends on whether there are zero, one or mulitple records).
			if(!is_array($result["Manufacturers"])){
			//No records
			}else{
				if(is_array($result["Manufacturers"]["Manufacturer"])){
				//Mulitple records
					$manufacturers = $result["Manufacturers"]["Manufacturer"];
				}else{
				//Only one record
					$manufacturers = $result["Manufacturers"];
				}
			}
		}
	}


	if($manufacturers) : ?>
    <form method="post" action="<?= site_url('boat-search-template'); ?>">
        <select name="makeModel" id="makeModel" class="makeModel" onchange="this.form.submit()">
            <option value="">Find a boat</option>
            <?php
		foreach ($manufacturers as $value) {
			$manufacturerEncoded = htmlspecialchars($value, ENT_COMPAT | ENT_XHTML); ?>
                <option value="<?= $manufacturerEncoded; ?>">
                    <?= $manufacturerEncoded; ?>
                </option>
            <?php }
		?>
        </select>
        <input type="hidden" name="postBack" value="true">
    </form>
<?php else :
	echo 'No Manufacturers';
endif;

}
