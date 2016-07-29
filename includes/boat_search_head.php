<?php
/* Template Name: Boat Search Template */
//We're using session variables to store the search parameters so we need to call session_start().
//This line needs to appear on every page in the website and must appear before anything else is sent out to the browser.
//If your website uses a content management system you may need to put this line in the header file of the CMS.
//If you don't want to use session variables, you could store the search parameters in a cookie or pass them around using querystrings instead.
session_start();
error_reporting(0);

$search_result_url = site_url('boat-search-result');

//Uncomment this line to hide warnings about session variables if they appear
//ini_set('session.bug_compat_warn', 0);

//Include TheYachtMarket LiveFeedback functions and variables.
require_once("includes/TheYachtMarket-LiveFeedback.php");

//Has the form been posted back?
if(isset($_POST['postBack']) && $_POST["postBack"] == "true"){
	//Form has been posted back.

	//Load search criteria from form post.
	$saleCharter = $_POST["saleCharter"];
	$newUsed = $_POST["newUsed"];
	$sailPower = $_POST["sailPower"];

	//Type allows multiple selection so check if it's an array
	if(is_array($_POST["type"])){
		//type is an array - convert to comma separated string
		$type = implode(",", $_POST["type"]);
	}else{
		//type is not an array - no selection so use zero length string
		$type = "";
	}

	$makeModel = $_POST["makeModel"];
	$country = $_POST["country"];
	$priceFrom = $_POST["priceFrom"];
	$priceTo = $_POST["priceTo"];
	$currency = $_POST["currency"];
	$charterPeriod = $_POST["charterPeriod"];
	$lengthFrom = $_POST["lengthFrom"];
	$lengthTo = $_POST["lengthTo"];
	$lengthUnit = $_POST["lengthUnit"];
	$yearFrom = $_POST["yearFrom"];
	$yearTo = $_POST["yearTo"];
	$orderBy = $_POST["orderBy"];

	//Validate numeric fields.
	if($priceFrom != "" and !is_numeric($priceFrom)){
		$validationErrors[] = "Price from must be a number";
	}
	if($priceTo != "" and !is_numeric($priceTo)){
		$validationErrors[] = "Price to must be a number";
	}
	if($lengthFrom != "" and !is_numeric($lengthFrom)){
		$validationErrors[] = "Length from must be a number";
	}
	if($lengthTo != "" and !is_numeric($lengthTo)){
		$validationErrors[] = "Length to must be a number";
	}
	if($yearFrom != "" and !is_numeric($yearFrom)){
		$validationErrors[] = "Year from must be a number";
	}
	if($yearTo != "" and !is_numeric($yearTo)){
		$validationErrors[] = "Year to must be a number";
	}


	//Were there any validation errors?
	if($validationErrors){
		//Form validation failed, show error messages.
		$errorMessage = "Please correct the errors listed below\n";
		$errorMessage .= "<ul>\n";
		foreach($validationErrors as $value){
			$errorMessage .= "<li>" . $value . "</li>\n";
		}
		$errorMessage .= "</ul>\n";

	}else{
		//Form validation passed.

		//Clear all search session variables as this is a new search (the search results page will re-populate the session vars from the querystring values).
		$_SESSION["saleCharter"] = "";
		$_SESSION["newUsed"] = "";
		$_SESSION["sailPower"] = "";
		$_SESSION["type"] = "";
		$_SESSION["makeModel"] = "";
		$_SESSION["country"] = "";
		$_SESSION["priceFrom"] = "";
		$_SESSION["priceTo"] = "";
		$_SESSION["currency"] = "";
		$_SESSION["charterPeriod"] = "";
		$_SESSION["lengthFrom"] = "";
		$_SESSION["lengthTo"] = "";
		$_SESSION["lengthUnit"] = "";
		$_SESSION["yearFrom"] = "";
		$_SESSION["yearTo"] = "";
		$_SESSION["orderBy"] = "";

		//Redirect to search results page.
		header("Location: $search_result_url". createSearchPageQueryString(1, $saleCharter, $newUsed, $sailPower, $type, $makeModel, $country
			, $priceFrom, $priceTo, $currency, $charterPeriod, $lengthFrom, $lengthTo, $lengthUnit, $yearFrom, $yearTo, $orderBy));
		exit;
	}
}else{
	//Form has not been posted back yet.


	//Load search criteria from session variables.
	$saleCharter = $_SESSION["saleCharter"];
	$newUsed = $_SESSION["newUsed"];
	$sailPower = $_SESSION["sailPower"];
	$type = $_SESSION["type"];
	$makeModel = $_SESSION["makeModel"];
	$country = $_SESSION["country"];
	$priceFrom = $_SESSION["priceFrom"];
	$priceTo = $_SESSION["priceTo"];
	$currency = $_SESSION["currency"];
	$charterPeriod = $_SESSION["charterPeriod"];
	$lengthFrom = $_SESSION["lengthFrom"];
	$lengthTo = $_SESSION["lengthTo"];
	$lengthUnit = $_SESSION["lengthUnit"];
	$yearFrom = $_SESSION["yearFrom"];
	$yearTo = $_SESSION["yearTo"];
	$orderBy = $_SESSION["orderBy"];
}


//Create SOAP client.
$client = new nusoap_client($liveFeedbackApiUrl, "wsdl");

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
}else{
	//Get the countries for this API key from TheYachtMarket LiveFeedback.
	//This section of code should be removed to improve the load speed of this page if you do not wish to show the country drop-down list.
	//apiKey: Your LiveFeedback API key
	//onlyBoatsMarkedForExport: If true, this will only return data for boats that have been marked for inclusion in this LiveFeedback implementation. If false, all boats matching the search will be returned.
	$param = array("apiKey" => $liveFeedbackApiKey, "onlyBoatsMarkedForExport" => $liveFeedbackOnlyBoatsMarkedForExport);
	$result = $client->call("GetLiveCountries", $param);

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

		//Put list of countries into array (how deep NuSoap nests the array depends on whether there are zero, one or mulitple countries).
		if(!is_array($result["Countries"])){
			//No countries
		}else{
			if(is_array($result["Countries"]["Country"])){
				//Mulitple countries
				$countries = $result["Countries"]["Country"];
			}else{
				//Only one country
				$countries = $result["Countries"];
			}
		}
	}

	//Get the manufacturers for this API key from TheYachtMarket LiveFeedback.
	//This section of code should be removed to improve the load speed of this page if you do not wish to show the country drop-down list.
	//apiKey: Your LiveFeedback API key
	//onlyBoatsMarkedForExport: If true, this will only return data for boats that have been marked for inclusion in this LiveFeedback implementation. If false, all boats matching the search will be returned.
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
?>
