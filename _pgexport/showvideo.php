<?php
/* Template Name: Show Boat Video Template */
//We're using session variables to store the search parameters so we need to call session_start().
//This line needs to appear on every page in the website and must appear before anything else is sent out to the browser.
//If your website uses a content management system you may need to put this line in the header file of the CMS.
//If you don't want to use session variables, you could store the search parameters in a cookie or pass them around using querystrings instead.
session_start();

//Uncomment this line to hide warnings about session variables if they appear
//ini_set('session.bug_compat_warn', 0);

//Include TheYachtMarket LiveFeedback functions and variables.
require_once("includes/TheYachtMarket-LiveFeedback.php");

//Get boatId from querystring.
$boatId = 0;
$boatId = (int) $_GET["boatid"];


if ($boatId == 0){
	//BoatId not present in querystring or not an integer.
	$errorMessage = "No boat was specified.";
	
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
		$param = array("apiKey" => $liveFeedbackApiKey, "boatId" => $boatId);
		$result = $client->call("GetVideoEmbedCode", $param);
		
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
			
			//Get video embed code into variable
			$videoEmbedCode = $result["GetVideoEmbedCodeResult"];
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
						<?php
						if ($errorMessage){
            //Show error message.
							echo("<div id=\"errorMessage\">");
							echo($errorMessage);
							echo("</div>");
						}else{
							?>
							<h1>Video</h1>
							<?php
							echo($videoEmbedCode);
						}
						?>
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
