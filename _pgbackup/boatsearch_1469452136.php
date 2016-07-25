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
<?php get_header(); ?> 
<div id="main-content" class="main-content"> 
    <div id="primary" class="content-area"> 
        <div id="content" class="site-content" role="main"> 
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>> 
                <?php
				the_title( '<header class="entry-header"><h1 class="entry-title">', '</h1></header><!-- .entry-header -->' );
				?> 
                <div class="entry-content">
                    <hr />
                    <form class="genericForm" method="post" action="<?php echo($_SERVER["REQUEST_URI"]);?>">
                        <label for="saleCharter">For sale / charter:</label>
                        <select name="saleCharter" id="saleCharter">
                            <option value="">Any</option>
                            <option value="sale" ?php echo(isoptionselected("sale", $salecharter));?>For sale</option>
                            <option value="charter" ?php echo(isoptionselected("charter", $salecharter));?>For charter</option>
                        </select>
                        <br />
                        <label for="newUsed">New / used:</label>
                        <select name="newUsed" id="newUsed">
                            <option value="">Any</option>
                            <option value="used" ?php echo(isoptionselected("used", $newused));?>Used</option>
                            <option value="new" ?php echo(isoptionselected("new", $newused));?>New</option>
                        </select>
                        <br />
                        <label for="sailPower">Sail / power / commercial:</label>
                        <select name="sailPower" id="sailPower">
                            <option value="">Any</option>
                            <option value="sail" ?php echo(isoptionselected("sail", $sailpower));?>Sail</option>
                            <option value="power" ?php echo(isoptionselected("power", $sailpower));?>Power</option>
                            <option value="commercial" ?php echo(isoptionselected("commercial", $sailpower));?>Commercial</option>
                            <option value="other" ?php echo(isoptionselected("other", $sailpower));?>Other</option>
                        </select>
                        <br />
                        <label for="type">Boat type:</label>
                        <select name="type[]" id="type" multiple="multiple">
                            <option value="speed boat" ?php echo(isoptionselected("speed boat", $type));?>Speed boat</option>
                            <option value="sports boat" ?php echo(isoptionselected("sports boat", $type));?>Sports boat</option>
                            <option value="cruiser" ?php echo(isoptionselected("cruiser", $type));?>Cruiser</option>
                        </select>
                        <br />
                        <label for="makeModel">Make:</label>
                        <select name="makeModel" id="makeModel">
                            <option value="">Any</option>
                            <?php
							foreach ($manufacturers as $value){
								$manufacturerEncoded = htmlspecialchars($value, ENT_COMPAT | ENT_XHTML);
								echo("<option value=\"" . $manufacturerEncoded . "\"" . isOptionSelected($value, $makeModel) . ">" . $manufacturerEncoded . "</option>");
							}
							?>
                        </select>
                        <!--
        <br />
        <label for="makeModel">Make / model:</label>
        <input type="text" name="makeModel" id="makeModel" value="<?php //echo($makeModel);?>" />
        See notes below
    -->
                        <br />
                        <label for="country">Location:</label>
                        <select name="country" id="country">
                            <option value="">Any</option>
                            <?php
    	foreach ($countries as $value){
    		$countryEncoded = htmlspecialchars($value, ENT_COMPAT | ENT_XHTML);
    		echo("<option value=\"" . $countryEncoded . "\"" . isOptionSelected($value, $country) . ">" . $countryEncoded . "</option>");
    	}
    	?>
                        </select>
                        <br />
                        <label for="priceFrom">Price from:</label>
                        <input type="text" name="priceFrom" id="priceFrom" value="<?php echo($priceFrom);?>" />
                        <br />
                        <label for="priceTo">Price to:</label>
                        <input type="text" name="priceTo" id="priceTo" value="<?php echo($priceTo);?>" />
                        <br />
                        <label for="currency">Currency:</label>
                        <select name="currency" id="currency">
                            <option value="GBP" ?php echo(isoptionselected("gbp", $currency));?>GBP</option>
                            <option value="EUR" ?php echo(isoptionselected("eur", $currency));?>EUR</option>
                            <option value="USD" ?php echo(isoptionselected("usd", $currency));?>USD</option>
                        </select>
                        <br />
                        <label for="charterPeriod">Charter period:</label>
                        <select name="charterPeriod" id="charterPeriod">
                            <option value="hour" ?php echo(isoptionselected("hour", $charterperiod));?>per hour</option>
                            <option value="day" ?php echo(isoptionselected("day", $charterperiod));?>per day</option>
                            <option value="week" ?php echo(isoptionselected("week", $charterperiod));?>per week</option>
                            <option value="month" ?php echo(isoptionselected("month", $charterperiod));?>per month</option>
                        </select>
                        <br />
                        <label for="lengthFrom">Length from:</label>
                        <input type="text" name="lengthFrom" id="lengthFrom" value="<?php echo($lengthFrom);?>" />
                        <br />
                        <label for="lengthTo">Length to:</label>
                        <input type="text" name="lengthTo" id="lengthTo" value="<?php echo($lengthTo);?>" />
                        <br />
                        <label for="lengthUnit">Length unit:</label>
                        <select name="lengthUnit" id="lengthUnit">
                            <option value="metres" ?php echo(isoptionselected("metres", $lengthunit));?>Metres</option>
                            <option value="feet" ?php echo(isoptionselected("feet", $lengthunit));?>Feet</option>
                        </select>
                        <br />
                        <label for="yearFrom">Year from:</label>
                        <input type="text" name="yearFrom" id="yearFrom" value="<?php echo($yearFrom);?>" />
                        <br />
                        <label for="yearTo">Year to:</label>
                        <input type="text" name="yearTo" id="yearTo" value="<?php echo($yearTo);?>" />
                        <br />
                        <label for="orderBy">Order by:</label>
                        <select name="orderBy" id="orderBy">
                            <option value="datedesc" ?php echo(isoptionselected("datedesc", $orderby));?>Date listed - latest first</option>
                            <option value="dateasc" ?php echo(isoptionselected("dateasc", $orderby));?>Date listed - oldest first</option>
                            <option value="priceasc" ?php echo(isoptionselected("priceasc", $orderby));?>Price - low to high</option>
                            <option value="pricedesc" ?php echo(isoptionselected("pricedesc", $orderby));?>Price - high to low</option>
                            <option value="lengthasc" ?php echo(isoptionselected("lengthasc", $orderby));?>Length - low to high</option>
                            <option value="lengthdesc" ?php echo(isoptionselected("lengthdesc", $orderby));?>Length - high to low</option>
                            <option value="yeardesc" ?php echo(isoptionselected("yeardesc", $orderby));?>Year - newest first</option>
                            <option value="yearasc" ?php echo(isoptionselected("yearasc", $orderby));?>Year - oldest first</option>
                        </select>
                        <br />
                        <input type="submit" value="Search" />
                        <input type="hidden" name="postBack" value="true" />
                    </form>
                    <hr />
                </div>
                <!-- .entry-content -->                 
            </article>
            <!-- #post-## -->             
        </div>
        <!-- #content -->         
    </div>
    <!-- #primary -->     
    <?php get_sidebar( 'content' ); ?> 
</div>
<!-- #main-content --> 
<?php
get_sidebar();
get_footer();
