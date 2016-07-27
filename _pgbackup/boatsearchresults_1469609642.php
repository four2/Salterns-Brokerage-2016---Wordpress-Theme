<?php
/* Template Name: Boat Search Results Template */
//We're using session variables to store the search parameters so we need to call session_start().
//This line needs to appear on every page in the website and must appear before anything else is sent out to the browser.
//If your website uses a content management system you may need to put this line in the header file of the CMS.
//If you don't want to use session variables, you could store the search parameters in a cookie or pass them around using querystrings instead.
session_start();
error_reporting(0);


$search_url = site_url('boat-search-template');
$search_result = site_url('boat-search-result');
$search_detail = site_url('boat-detail');
$search_video_url = site_url('boat-video');

//Uncomment this line to hide warnings about session variables if they appear
//ini_set('session.bug_compat_warn', 0);

//Include TheYachtMarket LiveFeedback functions and variables.
require_once("includes/TheYachtMarket-LiveFeedback.php");


//Get search criteria from querystring or session variables.
//If there's a value in the querystring, this will override what's in the session variable.
//The function calls below will also save the values back to the session variables.
$saleCharter = getFromSessionOrQueryStringAndUpdateSession("saleCharter");
$newUsed = getFromSessionOrQueryStringAndUpdateSession("newUsed");
$sailPower = getFromSessionOrQueryStringAndUpdateSession("sailPower");
$type = getFromSessionOrQueryStringAndUpdateSession("type");
$makeModel = getFromSessionOrQueryStringAndUpdateSession("makeModel");
$country = getFromSessionOrQueryStringAndUpdateSession("country");
$priceFrom = getFromSessionOrQueryStringAndUpdateSession("priceFrom");
$priceTo = getFromSessionOrQueryStringAndUpdateSession("priceTo");
$currency = getFromSessionOrQueryStringAndUpdateSession("currency");
$charterPeriod = getFromSessionOrQueryStringAndUpdateSession("charterPeriod");
$lengthFrom = getFromSessionOrQueryStringAndUpdateSession("lengthFrom");
$lengthTo = getFromSessionOrQueryStringAndUpdateSession("lengthTo");
$lengthUnit = getFromSessionOrQueryStringAndUpdateSession("lengthUnit");
$yearFrom = getFromSessionOrQueryStringAndUpdateSession("yearFrom");
$yearTo = getFromSessionOrQueryStringAndUpdateSession("yearTo");
$orderBy = getFromSessionOrQueryStringAndUpdateSession("orderBy");

//Set page number to 1 if not numeric or blank
$page = (int)$_REQUEST["pg"];
if($page < 1){$page = 1;}

//Store page number in session so we can easily create a link back to the last viewed search results page.
$_SESSION["page"] = $page;

//Get search results from TheYachtMarket LiveFeedback.
//Create SOAP client.
$client = new nusoap_client($liveFeedbackApiUrl, "wsdl");

//Set encoding to UTF-8 (may have to add or remove these lines depending on the server (Windows or Linux) to display UTF-8 encoded characters such as pound or euro symbols).
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
	//Generate the search string to pass to TheYachtMarket LiveFeedback (list of key=value params separated by &).
	if($saleCharter != ""){$searchParams[] = "saleorcharter=" . urlencode($saleCharter);}
	if($newUsed != ""){$searchParams[] = "neworused=" . urlencode($newUsed);}
	if($sailPower != ""){$searchParams[] = "sailorpower=" . urlencode($sailPower);}
	if($type != ""){$searchParams[] = "type=" . urlencode($type);}
	if($makeModel != ""){$searchParams[] = "manufacturer=" . urlencode($makeModel);}
	if($country != ""){$searchParams[] = "country=" . urlencode($country);}
	if(is_numeric($priceFrom)){$searchParams[] = "pricefrom=" . urlencode($priceFrom);}
	if(is_numeric($priceTo)){$searchParams[] = "priceto=" . urlencode($priceTo);}
	if($currency != ""){$searchParams[] = "currency=" . urlencode($currency);}
	if($charterPeriod != ""){$searchParams[] = "charterperiod=" . urlencode($charterPeriod);}
	if(is_numeric($lengthFrom)){$searchParams[] = "lengthfrom=" . urlencode($lengthFrom);}
	if(is_numeric($lengthTo)){$searchParams[] = "lengthto=" . urlencode($lengthTo);}
	if($lengthUnit != ""){$searchParams[] = "lengthunit=" . urlencode($lengthUnit);}
	if(is_numeric($yearFrom)){$searchParams[] = "yearfrom=" . urlencode($yearFrom);}
	if(is_numeric($yearTo)){$searchParams[] = "yearto=" . urlencode($yearTo);}

	//Order by.
	//$searchParams[] = "sortby=price&sortorder=desc";
	switch($orderBy){
		case "datedesc":
		$searchParams[] = "sortby=date&sortorder=desc";
		break;
		case "dateasc":
		$searchParams[] = "sortby=date&sortorder=asc";
		break;
		case "priceasc":
		$searchParams[] = "sortby=price&sortorder=asc";
		break;
		case "pricedesc":
		$searchParams[] = "sortby=price&sortorder=desc";
		break;
		case "lengthasc":
		$searchParams[] = "sortby=length&sortorder=asc";
		break;
		case "lengthdesc":
		$searchParams[] = "sortby=length&sortorder=desc";
		break;
		case "yeardesc":
		$searchParams[] = "sortby=year&sortorder=desc";
		break;
		case "yearasc":
		$searchParams[] = "sortby=year&sortorder=asc";
		break;
	}

	//Join parameters into a string.
	if(isset($searchParams)){
		$liveFeedbackSearchString = join($searchParams, "&");
	}else{
		$liveFeedbackSearchString = "";
	}

	//echo($liveFeedbackSearchString);

	//Get the search results from TheYachtMarket LiveFeedback.
	//apiKey: Your LiveFeedback API key
	//language: Two letter language code for the language you would like the boat's description returned in.
	//          This is only availble for languages where you have entered the boat details on TheYachtMarket.com in that language.
	//descriptionLength: Number of characters of the boat's description to return in the search results. Keep this
	//			as low as possible to prevent the page loading slowly.
	//resultsPerPage: How many search results to return on each page.
	//pageNumber: The page number of the search results that you want the data for.
	//searchString: Search parameters for filtering the search. Leave blank to return all boats.
	//onlyFeaturedBoats: If true, this will only return boats that you have marked as "featured" in your account on TheYachtMarket. If false, all boats matching the search will be returned.
	//onlyBoatsMarkedForExport: If true, this will only return boats that have been marked for inclusion in this LiveFeedback implementation. If false, all boats matching the search will be returned.
	$param = array("apiKey" => $liveFeedbackApiKey, "language" => "en", "descriptionLength" => $liveFeedbackSearchResultsDescriptionLength, "resultsPerPage" => $liveFeedbackSearchResultsPerPage, "pageNumber" => $page, "searchString" => $liveFeedbackSearchString, "onlyFeaturedBoats" => false, "onlyBoatsMarkedForExport" => $liveFeedbackOnlyBoatsMarkedForExport);
	$result = $client->call("GetSearchResults", $param);

	if ($client->fault) {
		//Something went wrong - uncomment code below to debug.
		//echo "<h2>Error at $client->fault</h2><pre>";
		//print_r($result);
		//echo "</pre>";

		//Show friendly message to user.
		$errorMessage = $liveFeedbackFetchError;

	} else {
		//Fetch succeeded.

		//Get the paging info.
		$totalResults = $result["GetSearchResultsResult"]["Paging"]["TotalResults"];
		$numPages = $result["GetSearchResultsResult"]["Paging"]["NumPages"];
		$resultsThisPage = $result["GetSearchResultsResult"]["Paging"]["ResultsThisPage"];

		if ($totalResults == 0){
			//No results.
			$errorMessage = "No boats were found matching your search. Please try widening your search criteria.";
		}else{
			//Boats found - get the details into an array.

			if($resultsThisPage == 1){
				$boats = $result["GetSearchResultsResult"]["Boats"];
			}else{
				//If there's more than one result, the array will be nested one level deeper
				$boats = $result["GetSearchResultsResult"]["Boats"]["SearchResultsBoat"];
			}

			//Uncomment the line below for debugging.
			//showWsdl($boats);
		}
	}
}


//Returns an unordered list of the non-empty items.
function formatFeatures($length, $year, $saleStatus, $country, $countrySubDivision, $area){
	$location = array();
	if($area != ""){array_push($location, $area);}
	if($countrySubDivision != ""){array_push($location, $countrySubDivision);}
	if($country != ""){array_push($location, $country);}
	$locationString = join(", ", $location);

	$return = "<ul>\n";
	if($length != ""){$return .= "<li>" . number_format($length, 2) . "m (" . metresToFeetAndInches($length) . ")" . "</li>";}
	if($year != ""){$return .= "<li>" . $year . "</li>";}
	if($locationString != ""){$return .= "<li>" . ucwords($locationString) . "</li>";}
	if($saleStatus != ""){$return .= "<li>" . formatSalesStatus($saleStatus) . "</li>";}
	if($priceComment != ""){$return .= "<li>" . $priceComment . "</li>";}
	$return .= "</ul>\n";
	return $return;
}


//Show sale price / charter price.
function formatPrice($currency, $currencySymbol, $salePrice, $charter, $charterPrice, $charterPricePeriod){
	if($charter == "true"){
		//Boat is for charter
		if($charterPrice != ""){
			return $currencySymbol . number_format($charterPrice, 0) . " " . $currency . " per " . $charterPricePeriod;
		}
	}else{
		//Boat is for sale
		if($salePrice != ""){
			return $currencySymbol . number_format($salePrice, 0) . " " . $currency;
		}
	}

	//Price not specified
	return "Contact us for price";
}


//Show image or placeholder.
function getImageUrl($imageUrl){
	if($imageUrl == ""){
		//No image, return placeholder image.
		return "/images/no-boat-image.gif";
	}else{
		//Image present, return the url.
		return $imageUrl;
	}
}
?>
<?php get_header(); ?>
<div class="container site-inner-width-1100">
    <div id="main-content" class="main-content">
        <div id="primary" class="content-area">
            <div id="content" class="site-content" role="main">
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <?php
				the_title( '<header class="entry-header"><h1 class="entry-title">', '</h1></header><!-- .entry-header -->' );
				?>
                    <div class="entry-content">
                            <div>
                            <p>Page <?php echo($page)?></p>
                            <a href="<?= $search_url; ?>">&lt; New search</a>
                            <?php
						if($errorMessage){
							?>
                                <div>
                                    <?php echo($errorMessage);?>
                                </div>
                                <?php
						}else{
            //Iterate through this page of boats.
							foreach($boats as $boat){
								?>
                                    <!--OLD style-->
                                    <div class="boatResultsPanel">
                                        <div class="imgContainer">
                                            <a href="../boat-details/?boatid=<?php echo($boat["BoatId"]);?>">
                                                <img src="<?php echo(getImageUrl($boat["ImageUrlThumb"]));?>" alt="<?php echo(htmlspecialchars(trim($boat["Manufacturer"] . " " . $boat["Model"])));?>" />
                                            </a>                                             
                                        </div>
                                        <div class="copyContainer">
                                            <h2><?php echo(formatPrice($boat["Currency"], $boat["CurrencySymbol"], $boat["SalePrice"], $boat["Charter"], $boat["CharterPrice"], $boat["CharterPricePeriod"]));?></h2>
                                            <h3><?php echo(trim($boat["Manufacturer"] . " " . $boat["Model"]));?> | <?php echo($boat["LyingCountry"]);?></h3>
                                            <p><?php echo($boat["Description"]);?><?php if($boat["Description"] != ""){echo("...");}?>.</p>
                                            <?php
						echo(formatFeatures($boat["NewOrUsed"], $boat["SailOrPower"], $boat["Year"], $boat["LengthOverallMetres"], $boat["Fuel"], $boat["SaleStatus"]));
						?>
                                            <div class="clearBoth"></div>
                                            <p class="readMore"><a href="../boat-details/?boatid=<?php echo($boat["BoatId"]);?>">View Boat &#8250;</a></p>
                                        </div>
                                        <div class="clearBoth"></div>
                                    </div>
                                    <!--OLD style END-->
                                    <div>
                                        <a name="boat<?php echo($boat["BoatId"]);?>"></a>
                                        <h3><?php echo(trim($boat["Manufacturer"] . " " . $boat["Model"]));?></h3>
                                        <a href="<?= $search_detail; ?>?boatid=<?php echo($boat["BoatId"]);?>">
                                            <img src="<?php echo(getImageUrl($boat["ImageUrlThumb"]));?>" alt="<?php echo(htmlspecialchars(trim($boat["Manufacturer"] . " " . $boat["Model"])));?>" />
                                        </a>
                                        <p><?php echo(formatPrice($boat["Currency"], $boat["CurrencySymbol"], $boat["SalePrice"], $boat["Charter"], $boat["CharterPrice"], $boat["CharterPricePeriod"]));?></p>
                                        <p><?php echo($boat["PriceComment"]);?></p>
                                        <?php
									echo(formatFeatures($boat["LengthOverallMetres"], $boat["Year"], $boat["SaleStatus"], $boat["LyingCountry"], $boat["LyingCountrySubDivision"], $boat["LyingArea"]));
									?>
                                        <a href="<?= $search_detail; ?>?boatid=<?php echo($boat["BoatId"]);?>">View details</a>
                                        <?php
									if($boat["HasVideo"] == "true"){
										?>
                                            <a href="<?= $search_video_url; ?>?boatid=<?php echo($boat["BoatId"])?>" target="_blank">View video</a>
                                        <?php
									}
									?>
                                        <br />
                                        <strong>All available fields. You can choose which you include.</strong>
                                        <br />BoatId on TheYachtMarket:
                                        <?php echo($boat["BoatId"])?>
                                        <br />Broker/dealer's own reference:
                                        <?php echo($boat["SellerReference"])?>
                                        <br />Manufacturer:
                                        <?php echo($boat["Manufacturer"])?>
                                        <br />Model:
                                        <?php echo($boat["Model"])?>
                                        <br />Boat name:
                                        <?php echo($boat["BoatName"])?>
                                        <br />Snippet of description:
                                        <?php echo($boat["Description"])?>
                                        <br />Keel type:
                                        <?php echo($boat["KeelType"])?>
                                        <br />Sale price:
                                        <?php echo($boat["SalePrice"])?>
                                        <br />Currency of price:
                                        <?php echo($boat["Currency"])?>
                                        <br />Currency symbol:
                                        <?php echo($boat["CurrencySymbol"])?>
                                        <br />For charter? (true = boat is for charter; false = boat is for sale):
                                        <?php echo($boat["Charter"])?>
                                        <br />Charter price:
                                        <?php echo($boat["CharterPrice"])?>
                                        <br />Period the charter price represents (i.e. per hour/day/week/month):
                                        <?php echo($boat["CharterPricePeriod"])?>
                                        <br />Price comment (e.g. "or nearest offer", "reduced" etc.):
                                        <?php echo($boat["Price comment"])?>
                                        <br />Tax paid/included:
                                        <?php echo($boat["TaxIncluded"])?>
                                        <br />Length overall (in metres):
                                        <?php echo($boat["LengthOverallMetres"])?>
                                        <br />Length overall (in feet and inches:
                                        <?php echo(metresToFeetAndInches($boat["LengthOverallMetres"]))?>
                                        <br />Year:
                                        <?php echo($boat["Year"])?>
                                        <br />Country where the boat is located:
                                        <?php echo($boat["LyingCountry"])?>
                                        <br />Country subdivision (e.g. name of a state, province etc.) where the boat is located:
                                        <?php echo($boat["LyingCountrySubDivision"])?>
                                        <br />Area (region, city, village etc.) where the boat is located:
                                        <?php echo($boat["LyingArea"])?>
                                        <br />New or used boat:
                                        <?php echo($boat["NewOrUsed"])?>
                                        <br />Sail or power:
                                        <?php echo($boat["SailOrPower"])?>
                                        <br />Fuel:
                                        <?php echo($boat["Fuel"])?>
                                        <br />Sale status (i.e. available, underoffer, sold):
                                        <?php echo($boat["SaleStatus"])?>
                                        <br />Date listed:
                                        <?php echo($boat["DateListed"])?>
                                        <br />Video available?:
                                        <?php echo($boat["HasVideo"])?>
                                        <br />
                                        <strong>Primary image at various sizes (use whichever is closest to your design's requirements):</strong>
                                        <ul>
                                            <li>
                                                <?php echo($boat["ImageUrlFeatured"])?>
                                            </li>
                                            <li>
                                                <?php echo($boat["ImageUrlThumb"])?>
                                            </li>
                                            <li>
                                                <?php echo($boat["ImageUrlTinySquare"])?>
                                            </li>
                                            <li>
                                                <?php echo($boat["ImageUrlFourByThree"])?>
                                            </li>
                                            <li>
                                                <?php echo($boat["ImageUrlPdfThumb"])?>
                                            </li>
                                        </ul>
                                    </div>
                                    <?php
							}


            //Generate the page links.
							if($totalResults > $liveFeedbackSearchResultsPerPage && $liveFeedbackSearchResultsPerPage != 0){
								?>
                                    <div>
                                        <ul>
                                            <?php
										for($i = 1; $i <= ceil($totalResults / $liveFeedbackSearchResultsPerPage); $i++){
											?>
                                                <li<?php if($i == $page){echo(" class=\"selected\"");}?>>
                                                    <a href="<?= $search_result; ?><?php if($i > 1){echo(" pg=" . $i);}?>">Page <?php echo($i);?></a>
                                                </li>
                                            <?php
										}
										?>
                                        </ul>
                                    </div>
                                <?php
							}
						}
						?>
                        </div>
                        <div>
                            <a href="http://www.theyachtmarket.com/" target="_blank">Boat listings powered by TheYachtMarket</a>
                        </div>
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
</div>
<!-- #main-content -->
<?php
get_sidebar();
get_footer();
