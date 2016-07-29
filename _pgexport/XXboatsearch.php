<?php
get_header(); ?>

            <section>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-9">
                            <div class="pg-empty-placeholder container-fluid"></div>
                            <div class="container-fluid">
                                <?php
the_title( '<header class="entry-header"><h1 class="entry-title">', '</h1></header><!-- .entry-header -->' );
?>
                                <div class="entry-content">
                                    <hr />
                                    <form class="genericForm" method="post" action="<?php echo($_SERVER["REQUEST_URI"]);?>">
                                        <div class="container-fluid">
                                            <div class="row">
                                                <div class="col-md-4 form-group-lg">
                                                    <label for="saleCharter">
                                                        <?php _e( 'For sale / charter:', 'sb2016' ); ?>
                                                    </label>
                                                    <select name="saleCharter" id="saleCharter" class="input-lg form-control">
                                                        <option value="">
                                                            <?php _e( 'Any', 'sb2016' ); ?>
                                                        </option>
                                                        <option value="sale" ?php echo(isoptionselected("sale", $salecharter));? for sale< option>

                                                        <option value="charter" ?php echo(isoptionselected("charter", $salecharter));? for charter< option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4 form-group-lg">
                                                    <label for="newUsed">
                                                        <?php _e( 'New / used:', 'sb2016' ); ?>
                                                    </label>
                                                    <select name="newUsed" id="newUsed" class="input-lg form-control">
                                                        <option value="">
                                                            <?php _e( 'Any', 'sb2016' ); ?>
                                                        </option>
                                                        <option value="used" ?php echo(isoptionselected("used", $newused));? used< option>

                                                        <option value="new" ?php echo(isoptionselected("new", $newused));? new< option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4 form-group-lg">
                                                    <label for="sailPower">
                                                        <?php _e( 'Sail / power / commercial:', 'sb2016' ); ?>
                                                    </label>
                                                    <select name="sailPower" id="sailPower" class="input-lg form-control">
                                                        <option value="">
                                                            <?php _e( 'Any', 'sb2016' ); ?>
                                                        </option>
                                                        <option value="sail" ?php echo(isoptionselected("sail", $sailpower));? sail< option>

                                                        <option value="power" ?php echo(isoptionselected("power", $sailpower));? power< option>

                                                        <option value="commercial" ?php echo(isoptionselected("commercial", $sailpower));? commercial< option>

                                                        <option value="other" ?php echo(isoptionselected("other", $sailpower));? other< option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 form-group-lg">
                                                    <label for="type">
                                                        <?php _e( 'Boat type:', 'sb2016' ); ?>
                                                    </label>
                                                    <select name="type[]" id="type" multiple="multiple" class="input-lg form-control">
                                                        <option value="speed boat" ?php echo(isoptionselected("speed boat", $type));? speed boat< option>

                                                        <option value="sports boat" ?php echo(isoptionselected("sports boat", $type));? sports boat< option>

                                                        <option value="cruiser" ?php echo(isoptionselected("cruiser", $type));? cruiser< option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4 form-group-lg">
                                                    <label for="makeModel">
                                                        <?php _e( 'Make:', 'sb2016' ); ?>
                                                    </label>
                                                    <select name="makeModel" id="makeModel" class="input-lg form-control">
                                                        <option value="">
                                                            <?php _e( 'Any', 'sb2016' ); ?>
                                                        </option>
                                                        <?php
			foreach ($manufacturers as $value){
				$manufacturerEncoded = htmlspecialchars($value, ENT_COMPAT | ENT_XHTML);
				echo("<option value=\"" . $manufacturerEncoded . "\"" . isOptionSelected($value, $makeModel) . ">" . $manufacturerEncoded . "</option>");
			}
			?>
                                                    </select>
                                                </div>
                                                <div class="col-md-4 form-group-lg">
                                                    <label for="country">
                                                        <?php _e( 'Location:', 'sb2016' ); ?>
                                                    </label>
                                                    <select name="country" id="country" class="input-lg form-control">
                                                        <option value="">
                                                            <?php _e( 'Any', 'sb2016' ); ?>
                                                        </option>
                                                        <?php
	foreach ($countries as $value){
		$countryEncoded = htmlspecialchars($value, ENT_COMPAT | ENT_XHTML);
		echo("<option value=\"" . $countryEncoded . "\"" . isOptionSelected($value, $country) . ">" . $countryEncoded . "</option>");
	}
	?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 form-group-lg">
                                                    <label for="currency">
                                                        <?php _e( 'Currency:', 'sb2016' ); ?>
                                                    </label>
                                                    <select name="currency" id="currency" class="input-lg form-control">
                                                        <option value="GBP" ?php echo(isoptionselected("gbp", $currency));? gbp< option>

                                                        <option value="EUR" ?php echo(isoptionselected("eur", $currency));? eur< option>

                                                        <option value="USD" ?php echo(isoptionselected("usd", $currency));? usd< option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4 form-group-lg">
                                                    <label for="priceFrom">
                                                        <?php _e( 'Price from:', 'sb2016' ); ?>
                                                    </label>
                                                    <input type="text" name="priceFrom" id="priceFrom" value="<?php echo($priceFrom);?>" class="input-lg form-control" />
                                                </div>
                                                <div class="col-md-4 form-group-lg">
                                                    <label for="priceTo">
                                                        <?php _e( 'Price to:', 'sb2016' ); ?>
                                                    </label>
                                                    <input type="text" name="priceTo" id="priceTo" value="<?php echo($priceTo);?>" class="input-lg form-control" />
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 form-group-lg">
                                                    <label for="lengthUnit">
                                                        <?php _e( 'Length unit:', 'sb2016' ); ?>
                                                    </label>
                                                    <select name="lengthUnit" id="lengthUnit" class="input-lg form-control">
                                                        <option value="metres" ?php echo(isoptionselected("metres", $lengthunit));? metres< option>

                                                        <option value="feet" ?php echo(isoptionselected("feet", $lengthunit));? feet< option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4 form-group-lg">
                                                    <label for="lengthFrom">
                                                        <?php _e( 'Length from:', 'sb2016' ); ?>
                                                    </label>
                                                    <input type="text" name="lengthFrom" id="lengthFrom" value="<?php echo($lengthFrom);?>" class="input-lg form-control" />
                                                </div>
                                                <div class="col-md-4 form-group-lg">
                                                    <label for="lengthTo">
                                                        <?php _e( 'Length to:', 'sb2016' ); ?>
                                                    </label>
                                                    <input type="text" name="lengthTo" id="lengthTo" value="<?php echo($lengthTo);?>" class="input-lg form-control" />
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 form-group-lg">
                                                    <label for="charterPeriod">
                                                        <?php _e( 'Charter period:', 'sb2016' ); ?>
                                                    </label>
                                                    <select name="charterPeriod" id="charterPeriod" class="input-lg form-control">
                                                        <option value="hour" ?php echo(isoptionselected("hour", $charterperiod));? per hour< option>

                                                        <option value="day" ?php echo(isoptionselected("day", $charterperiod));? per day< option>

                                                        <option value="week" ?php echo(isoptionselected("week", $charterperiod));? per week< option>

                                                        <option value="month" ?php echo(isoptionselected("month", $charterperiod));? per month< option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4 form-group-lg">
                                                    <label for="yearFrom">
                                                        <?php _e( 'Year from:', 'sb2016' ); ?>
                                                    </label>
                                                    <input type="text" name="yearFrom" id="yearFrom" value="<?php echo($yearFrom);?>" class="input-lg form-control" />
                                                </div>
                                                <div class="col-md-4 form-group-lg">
                                                    <label for="yearTo">
                                                        <?php _e( 'Year to:', 'sb2016' ); ?>
                                                    </label>
                                                    <input type="text" name="yearTo" id="yearTo" value="<?php echo($yearTo);?>" class="input-lg form-control" />
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 form-group-lg">
                                                    <label for="orderBy">
                                                        <?php _e( 'Order by:', 'sb2016' ); ?>
                                                    </label>
                                                    <select name="orderBy" id="orderBy" class="input-lg form-control">
                                                        <option value="datedesc" ?php echo(isoptionselected("datedesc", $orderby));? date listed - latest first< option>

                                                        <option value="dateasc" ?php echo(isoptionselected("dateasc", $orderby));? date listed - oldest first< option>

                                                        <option value="priceasc" ?php echo(isoptionselected("priceasc", $orderby));? price - low to high< option>

                                                        <option value="pricedesc" ?php echo(isoptionselected("pricedesc", $orderby));? price - high to low< option>

                                                        <option value="lengthasc" ?php echo(isoptionselected("lengthasc", $orderby));? length - low to high< option>

                                                        <option value="lengthdesc" ?php echo(isoptionselected("lengthdesc", $orderby));? length - high to low< option>

                                                        <option value="yeardesc" ?php echo(isoptionselected("yeardesc", $orderby));? year - newest first< option>

                                                        <option value="yearasc" ?php echo(isoptionselected("yearasc", $orderby));? year - oldest first< option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 form-group-lg">
                                                    <input type="submit" value="Search" class="input-lg form-control" />
                                                    <input type="hidden" name="postBack" value="true" />
                                                </div>
                                            </div>
                                        </div>
                                        <!--
    <br />
    <label for="makeModel">Make / model:</label>
    <input type="text" name="makeModel" id="makeModel" value="<?php //echo($makeModel);?>" />
    See notes below
-->
                                    </form>
                                    <hr />
                                </div>
                                <!-- .entry-content -->
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <?php if ( is_active_sidebar( 'right_sidebar' ) ) : ?>
                                <aside id="main_sidebar">
                                    <?php dynamic_sidebar( 'right_sidebar' ); ?>
                                </aside>
                            <?php endif; ?>
                        </div>
                    </div>
            </section>                

<?php get_footer(); ?>