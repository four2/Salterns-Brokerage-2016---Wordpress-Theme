<?php
/* Template Name: Boat Details Template */
//We're using session variables to store the search parameters so we need to call session_start().
//This line needs to appear on every page in the website and must appear before anything else is sent out to the browser.
//If your website uses a content management system you may need to put this line in the header file of the CMS.
//If you don't want to use session variables, you could store the search parameters in a cookie or pass them around using querystrings instead.
session_start();

//Uncomment this line to hide warnings about session variables if they appear
//ini_set('session.bug_compat_warn', 0);

//Include TheYachtMarket LiveFeedback functions and variables.
require_once("includes/TheYachtMarket-LiveFeedback.php");


$search_video_url = site_url('boat-video');

//Get boatId from querystring.
$boatId = 0;
$boatId = (int) $_GET["boatid"];

//Initialise variable for checking if the requested boat exists. If the boat exists, this will be set to true later on.
$boatExists = false;

//Create arrays for building the meta title and description.
$metaKeywordsArray = array();
$metaDescriptionArray = array();

//Generate the URL for linking back to the listings
$backToSearchUrl = site_url('boat-search-result');
$page = (int)$_SESSION["page"];

if($page > 1){$backToSearchUrl .= "?pg=" . $page;}
if($boatId != 0){$backToSearchUrl .= "#boat" . $boatId;}


if ($boatId == 0){
	//BoatId not present in querystring or not an integer.
	$errorMessage = "No boat was specified in your search. Please go back to the search and select a boat to view.";
	
}else{
	//BoatId is present and is an integer.
	
	//Create SOAP client.
	$client = new nusoap_client($liveFeedbackApiUrl, "wsdl");
	
	//Set encoding to UTF-8 (may have to add or remove these lines depending on the server (Windows or Linux) to display some symbols such as pound or euro).
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
		
		//Get the boat details from LiveFeedback.
		$param = array("apiKey" => $liveFeedbackApiKey, "language" => "en", "boatId" => $boatId);
		$result = $client->call("GetBoat", $param);
		
		if ($client->fault) {
			//Something went wrong - uncomment code below to debug.
			//echo "<h2>Error at $client->fault</h2><pre>";
			//print_r($result);
			//echo "</pre>";
			
			//Show friendly message to user.
			$errorMessage = $liveFeedbackFetchError;
			
		} else {
			//Fetch of data from LiveFeedback succeeded.
			
			// Display the result for debugging.
			//showWsdl($result);
			
			
			//Get the data for this boat into an array.
			$boat = $result["GetBoatResult"];
			
			
			//Does the boat exist?
			if($boat["BoatExists"] == "true"){$boatExists = true;}
			
			
			if(!$boatExists){
				//Boat does not exist.
				$errorMessage = "Sorry, the boat you were looking for could not be found; it may have been sold already.";
				
			}else{
				//Boat exists.
				
				//Process boat details.
				
				//Make/model.
				$makeModel = trim($boat["Manufacturer"] . " " . $boat["Model"]);
				if ($makeModel == ""){
					$makeModel = "Unknown";
				}
				
				
				//Show sale price, charter price or POA.
				if($boat["Charter"] == "true"){
					//Boat is for charter
					$priceString = $boat["CurrencySymbol"] . number_format($boat["CharterPrice"], 0) . " ". $boat["Currency"] . " per " . $boat["CharterPricePeriod"];
				}else{
					//Boat is for sale
					if($boat["SalePrice"] == ""){
						//Price not specified
						$priceStrong = "POA";
					}else{
						$priceString = $boat["CurrencySymbol"] . number_format($boat["SalePrice"], 0) . " ". $boat["Currency"];
					}
				}
				
				
				//Description.
				$description = $boat["Description"];
				
				//Remove excess line breaks.
				$description = fixLineBreaks($description, true);
				
				//Add the description heading if description is not blank.
				if($description != ""){$description = "<h3>Description</h3>\n" . $description;}
				
				
				//Custom title/description 1.
				$custom1 = trim($boat["CustomTitle1"]);
				if($custom1 != ""){$custom1 = "<h3>" . $custom1 . "</h3>";}
				$custom1 .= fixLineBreaks($boat["CustomDescription1"], true);
				
				
				//Custom title/description 2.
				$custom2 = trim($boat["CustomTitle2"]);
				if($custom2 != ""){$custom2 = "<h3>" . $custom2 . "</h3>";}
				$custom2 .= fixLineBreaks($boat["CustomDescription2"], true);
				
				
				//Custom title/description 3.
				$custom3 = trim($boat["CustomTitle3"]);
				if($custom3 != ""){$custom3 = "<h3>" . $custom3 . "</h3>";}
				$custom3 .= fixLineBreaks($boat["CustomDescription3"], true);
				
				
				//VAT/tax.
				$taxString = $boat["TaxIncluded"];
				switch(strtolower($taxString)){
					case "true":
					$taxString = "Paid / included";
					break;
					case "false":
					$taxString = "Not paid / excluded";
					break;
					default:
					$taxString = "";
					break;
				}
				
				
				//Sail/power/commerical/other.
				$categoryString = $boat["SailOrPower"];
				switch(strtolower($categoryString)){
					case "":
					case "other":
					$categoryString = "";
					break;
					default:
					$categoryString = $categoryString;
					break;
				}
				
				
				//Fuel.
				$fuelString = $boat["Fuel"];
				if(strtolower($fuelString) == "gas/lpg"){
					$fuelString = "Gas/LPG";
				}else{
					$fuelString = ucfirst($fuelString);
				}
				
				
				//Location.
				$locationArea = trim($boat["LyingArea"]);
				$locationCountrySubDivision = trim($boat["LyingCountrySubDivision"]);
				$locationCountry = trim($boat["LyingCountry"]);
				if($locationArea != ""){$locationArray[] = $locationArea;}
				if($locationCountrySubDivision != ""){$locationArray[] = $locationCountrySubDivision;}
				if($locationCountry != ""){$locationArray[] = $locationCountry;}
				$locationString = implode(", ", $locationArray);
				
				
				//Disclaimer.
				$disclaimer = $boat["Disclaimer"];
				
				
				//General info table - add non blank items to an array.
				addKeyValuePair($generalInfoArray, "Manufacturer / model", $boat["Manufacturer"] . " " . $boat["Model"]);
				addKeyValuePair($generalInfoArray, "Name", $boat["BoatName"]);
				addKeyValuePair($generalInfoArray, "Designer", $boat["Designer"]);
				addKeyValuePair($generalInfoArray, "Year", $boat["Year"]);
				addKeyValuePair($generalInfoArray, "Category", ucfirst($categoryString));
				addKeyValuePair($generalInfoArray, "New or used", ucfirst($boat["NewOrUsed"]));
				addKeyValuePair($generalInfoArray, "Status", formatSalesStatus($boat["SaleStatus"]));
				addKeyValuePair($generalInfoArray, "Price", $priceString);
				addKeyValuePair($generalInfoArray, "Tax / VAT status", $taxString);
				addKeyValuePair($generalInfoArray, "Price comment", $boat["PriceComment"]);
				addKeyValuePair($generalInfoArray, "Lying", $locationString);
				addKeyValuePair($generalInfoArray, "Reference", $boat["SellerReference"]);
				
				//General info table - build the table.
				if(count($generalInfoArray) > 0){
					$generalInfoTable = "\n<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
					$generalInfoTable .= "\n<tr><th>General Information</th><th>&nbsp;</th>";
					foreach ($generalInfoArray as $key => $value){
						$generalInfoTable .= "\n<tr><td>" . $key . "</td><td>" . $value . "</td></tr>";
					}
					$generalInfoTable .= "\n</table>";
				}
				
				
				//Specifications table - add non blank items to an array.
				addKeyValuePair($specificationsArray, "Length overall", $boat["LengthOverallMetres"], " m", 2);
				addKeyValuePair($specificationsArray, "Length waterline", $boat["LengthWaterlineMetres"], " m", 2);
				addKeyValuePair($specificationsArray, "Length on deck", $boat["LengthOnDeckMetres"], " m", 2);
				addKeyValuePair($specificationsArray, "Beam", $boat["BeamMetres"], " m", 2);
				addKeyValuePair($specificationsArray, "Draft", $boat["DraftMetres"], " m", 2);
				addKeyValuePair($specificationsArray, "Displacement", $boat["DisplacementKg"], " kg", -1);
				addKeyValuePair($specificationsArray, "Hull", $boat["Hull"]);
				addKeyValuePair($specificationsArray, "Keel", ucfirst(strtolower($boat["KeelType"])));
				
				//Specifications table - build the table.
				if(count($specificationsArray) > 0){
					$specificationsTable = "\n<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
					$specificationsTable .= "\n<tr><th>Specifications</th><th>&nbsp;</th>";
					foreach ($specificationsArray as $key => $value){
						$specificationsTable .= "\n<tr><td>" . $key . "</td><td>" . $value . "</td></tr>";
					}
					$specificationsTable .= "\n</table>";
				}
				
				
				//Propulsion table - add non blank items to an array.
				addKeyValuePair($propulsionArray, "Engine", $boat["Engine"]);
				addKeyValuePair($propulsionArray, "Engine hours", $boat["EngineHours"], " hours", -1);
				addKeyValuePair($propulsionArray, "Fuel", $fuelString);
				addKeyValuePair($propulsionArray, "Fuel capacity", $boat["FuelCapacityLitres"], " litres", -1);
				addKeyValuePair($propulsionArray, "Maximum speed", $boat["MaxSpeedKph"], " kph", 2);
				addKeyValuePair($propulsionArray, "Cruising speed", $boat["CruisingSpeedKph"], " kph", 2);
				addKeyValuePair($propulsionArray, "Range", $boat["RangeKm"], " km", -1);
				
				//Propulsion table - build the table.
				if(count($propulsionArray) > 0){
					$propulsionTable = "\n<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
					$propulsionTable .= "\n<tr><th>Propulsion</th><th>&nbsp;</th>";
					foreach ($propulsionArray as $key => $value){
						$propulsionTable .= "\n<tr><td>" . $key . "</td><td>" . $value . "</td></tr>";
					}
					$propulsionTable .= "\n</table>";
				}
				
				
				//Accommodation table - add non blank items to an array.
				addKeyValuePair($accommodationArray, "Number of berths", $boat["Berths"]);
				addKeyValuePair($accommodationArray, "Number of cabins", $boat["Cabins"]);
				addKeyValuePair($accommodationArray, "Passenger capacity", $boat["Passengers"]);
				addKeyValuePair($accommodationArray, "Drinking water capacity", $boat["DrinkingWaterCapacityLitres"], " litres", -1);
				addKeyValuePair($accommodationArray, "Further information", nl2br($boat["Accommodation"]));
				
				//Accommodation table - build the table.
				if(count($accommodationArray) > 0){
					$accommodationTable = "\n<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"lastTable\">";
					$accommodationTable .= "\n<tr><th>Accommodation</th><th>&nbsp;</th>";
					foreach ($accommodationArray as $key => $value){
						$accommodationTable .= "\n<tr><td>" . $key . "</td><td>" . $value . "</td></tr>";
					}
					$accommodationTable .= "\n</table>";
				}
				
				
				//Get the photos for this boat into an array.
				//If mulitple photos, the values will be in ["Photos"]["Photo"][0],[1],[2] etc.
				//However, if only one photo, the values will be direclty in ["Photos"].
				if(is_array($boat["Photos"])){
					//One or more photos
					if(is_array($boat["Photos"]["Photo"][0])){
						//More than one photo
						$photos = $boat["Photos"]["Photo"];
					}else{
						//One photo
						$photos = $boat["Photos"];
					}
				}else{
					//No photos
					//Set placeholder image for no photo
					$photos[0]["Caption"] = "";
					$photos[0]["ImageUrlOriginal"] = "/images/shared/boat-placeholder-large.gif";
					$photos[0]["ImageUrlMain"] = "/images/shared/boat-placeholder-large.gif";
					$photos[0]["ImageUrlLarge"] = "/images/shared/boat-placeholder-large.gif";
					$photos[0]["ImageUrlFeatured"] = "/images/shared/boat-placeholder-large.gif";
					$photos[0]["ImageUrlThumb"] = "/images/shared/boat-placeholder-large.gif";
					$photos[0]["ImageUrlTinySquare"] = "/images/shared/boat-placeholder-large.gif";
					$photos[0]["ImageUrlFourByThree"] = "/images/shared/boat-placeholder-large.gif";
					$photos[0]["ImageUrlPdfThumb"] = "/images/shared/boat-placeholder.gif";
				}
				
				
				//Video.
				$videoEmbedCode = $boat["VideoEmbedCode"];
				
				
				//Generate meta keywords array.
				if($makeModel != "Unknown"){array_push($metaKeywordsArray, $makeModel);}
				if($categoryString != ""){array_push($metaKeywordsArray, $categoryString);}
				if($fuelString != ""){array_push($metaKeywordsArray, $fuelString);}
				if($locationString != ""){array_push($metaKeywordsArray, $locationString);}
				if($boat["Year"] != ""){array_push($metaKeywordsArray, $boat["Year"]);}
				
				//Create comma separated string from keywords array.
				$metaKeywords = implode(", ", $metaKeywordsArray);
				
				
				//Generate meta description array.
				if($boat["Year"] != ""){array_push($metaDescriptionArray, $boat["Year"]);}
				if($makeModel != "Unknown"){array_push($metaDescriptionArray, $makeModel);}
				if($priceString != "POA"){array_push($metaDescriptionArray, "for sale at " . $priceString);}
				if($locationString != ""){array_push($metaDescriptionArray, "lying in " . $locationString);}
				
				//Create string from description array.
				$metaDescription = implode(" ", $metaDescriptionArray);
				if($metaDescription != ""){$metaDescription = "A " . $metaDescription;}
				
				
				//End of processing boat details.
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

					<div>
						<div>
							<?php
							if ($errorMessage){
				//Show error message.
								echo("<div id=\"errorMessage\">");
								echo($errorMessage);
								echo("</div>");
							}else{
								?>
								<h1><?php echo($makeModel);?></h1>
								<p><a href="<?php echo($backToSearchUrl);?>">&#8249; Back to listings</a></p>
								<div>
									<div>
										<?php
										foreach ($photos as $value){
											$captionEncoded = htmlspecialchars($value["Caption"], ENT_COMPAT | ENT_XHTML);
											?>
											<a href="<?php echo($value["ImageUrlLarge"]);?>" target="_blank"><img alt="<?php echo($captionEncoded);?>" src="<?php echo($value["ImageUrlFeatured"]);?>" /></a>
											<?php
										}
										?>
									</div>
									<div>
										<?php
										if($videoEmbedCode != ""){
											?>
											<p><a href="<?= $search_video_url; ?>?boatid=<?php echo($boatId)?>" target="_blank">View video</a></p>
											<?php
										}
										?>
										<hr />
										<p>For more information please telephone: xxxx xxxx xxxx
    					<?php //Create a string for the email subject line.
    					$emailSubjectArray = array();
    					array_push($emailSubjectArray, "Enquiry about ");
    					if ($boat["BoatName"] != ""){
    						array_push($emailSubjectArray, "\"" . $boat["BoatName"] . "\"");
    					}
    					if ($makeModel != ""){
    						array_push($emailSubjectArray, $makeModel);
    					}
    					if ($boat["SellerReference"] != ""){
    						array_push($emailSubjectArray, "(" . $boat["SellerReference"] . ")");
    					}
    					$emailSubject = str_replace("+", "%20", urlencode(implode(" ", $emailSubjectArray)));
    					?>
    					or, <a href="mailto:email@domain.com?subject=<?php echo($emailSubject)?>">send an email</a> to enquire about this boat</p>
    				</div>
    			</div>
    			<?php
    		}
    		?>
    	</div>
    	<div>
    		<?php
    		if ($generalInfoTable != ""){
    			echo($generalInfoTable);
    		}
    		if ($specificationsArray != ""){
    			echo($specificationsTable);
    		}
    		if ($propulsionArray != ""){
    			echo($propulsionTable);
    		}
    		if ($accommodationArray != ""){
    			echo($accommodationTable);
    		}
    		if ($description != ""){
    			echo("<div>" . $description . "</div>");
    		}
    		if ($custom1 != ""){
    			echo("<div>" . $custom1 . "</div>");
    		}
    		if ($custom2 != ""){
    			echo("<div>" . $custom2 . "</div>");
    		}
    		if ($custom3 != ""){
    			echo("<div>" . $custom3 . "</div>");
    		}
    		if ($boat["DateListed"] != ""){
    			echo("<div>Date listed: " . $boat["DateListed"] . "</div>");
    		}
    		if ($boat["MoreInformationUrl"] != ""){
    			echo("<div><a href=\"" . $boat["MoreInformationUrl"] . "\" target=\"_blank\">More information</a></div>");
    		}
    		if ($boat["ViewOnTheYachtMarketUrl"] != ""){
    			echo("<div><a href=\"" . $boat["ViewOnTheYachtMarketUrl"] . "\" target=\"_blank\">View this boat on TheYachtMarket</a></div>");
    		}
    		if ($disclaimer != ""){
    			echo("<div>" . $disclaimer . "</div>");
    		}
    		?>
    		<p><strong>URLs to all size versions of the photos - use whichever are the closest to the size you need for your design:</strong></p>
    		<ul>
    			<?php
    			$photoCount = 0;
    			foreach ($photos as $value){
    				$photoCount ++;
    				?>
    				<li>Photo <?php echo($photoCount);?>
    					<ul>
    						<li><?php echo($value["ImageUrlFeatured"])?></li>
    						<li><?php echo($value["ImageUrlThumb"])?></li>
    						<li><?php echo($value["ImageUrlTinySquare"])?></li>
    						<li><?php echo($value["ImageUrlFourByThree"])?></li>
    						<li><?php echo($value["ImageUrlPdfThumb"])?></li>
    						<li><?php echo($value["ImageUrlMain"]);?></li>
    						<li><?php echo($value["ImageUrlLarge"]);?></li>
    						<li><?php echo($value["ImageUrlOriginal"]);?></li>
    					</ul>
    				</li>
    				<?php
    			}
    			?>
    		</ul>
    	</div>
    </div>
    <div><a href="http://www.theyachtmarket.com/" target="_blank">Boat listings powered by TheYachtMarket</a></div>

</div><!-- .entry-content -->
</article><!-- #post-## -->

</div><!-- #content -->
</div><!-- #primary -->
<?php get_sidebar( 'content' ); ?>
</div><!-- #main-content -->

<?php
get_sidebar();
get_footer();
