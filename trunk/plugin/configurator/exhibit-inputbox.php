<?php
/* This is the contents of the Exhibit Creation Lightbox.
 *
 * Only one GET variable will ever be passed into this file, and this is the ID
 * of the Exhibit-in-progress to continue editing. If this variable is not present, 
 * then begin a new exhibit. If this variable is present, then load the existing exhibit.
 *
 * TODO:
 *  - Protect so that only admins can load this page
 *  - Load an existing exhibit if ID is present.
 *  - Create save button which posts exhibit, receives the exhibit id, and transmits 
 *      exhibit id back to root page.
 *  - Cancel button, which gets the hell out!
 */


/* -------------------------------------------------
 * Load up Wordpress
 * -------------------------------------------------
 */
	ob_start();
      $root = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
      if (file_exists($root.'/wp-load.php')) {
          // WP 2.6
          require_once($root.'/wp-load.php');
      } else {
          // Before 2.6
          require_once($root.'/wp-config.php');
      }
	ob_end_clean(); //Ensure we don't have output from other plugins.
	header('Content-Type: text/html; charset='.get_option('blog_charset'));
	$google_map_api_key = get_option( 'google_map_api_key' );

	if (!$guessurl = site_url())
		$guessurl = wp_guess_url();
	$baseuri = $guessurl;
	$exhibituri = $baseuri . '/wp-content/plugins/datapress';

/* -------------------------------------------------
 * Load up the exhibit if it exists
 * -------------------------------------------------
 */

$exhibitID = $_GET['exhibitid'];
$exhibitConfig = NULL;
if ($exhibitID != NULL) {
	// See if we know about any data sources associated with this item.
	$exhibitConfig = new WpPostExhibit();
	$ex_success = DbMethods::loadFromDatabase($exhibitConfig, $exhibitID);
	if (! $ex_success) {
		$exhibitConfig = NULL;
	}
}
?>
<html>
	<head>
	<style type="text/css" media="screen" src="exhibit.css"></style>
	
	<script type='text/javascript' src='<?php echo $baseuri ?>/wp-includes/js/jquery/jquery.js?ver=1.2.6'></script>
	<script type='text/javascript' src='<?php echo $baseuri ?>/wp-includes/js/jquery/ui.core.js?ver=1.5.2'></script>
	<script type='text/javascript' src='<?php echo $baseuri ?>/wp-includes/js/jquery/ui.tabs.js?ver=1.5.2'></script>
	<script type="text/javascript" src="<?php echo $baseuri ?>/wp-includes/js/tinymce/tiny_mce.js?ver=20081129"></script>
	<script type="text/javascript" src="<?php echo $baseuri ?>/wp-includes/js/tinymce/langs/wp-langs-en.js?ver=20081129"></script>
	<script type="text/javascript" src="http://static.simile.mit.edu/exhibit/api-2.0/exhibit-api.js"></script>
	<script type="text/javascript" src="http://static.simile.mit.edu/exhibit/extensions-2.0/chart/chart-extension.js"></script>
	<script type="text/javascript" src="http://static.simile.mit.edu/exhibit/extensions-2.0/time/time-extension.js"></script>
	
	<link rel='stylesheet' href='<?php echo $baseuri ?>/wp-admin/wp-admin.css?ver=20081210' type='text/css' media='all' />
	<link rel='stylesheet' href='<?php echo $baseuri ?>/wp-admin/css/colors-fresh.css?ver=20081210' type='text/css' media='all' />
	<link rel="stylesheet" type="text/css" href="<?php echo $exhibituri ?>/css/wpexhibit.css"></link>

	<script type="text/javascript" src="<?php echo $exhibituri ?>/configurator/configurator.js"></script>
	<script type="text/javascript">
		jQuery(document).ready(function(){
			var category_tabs = jQuery("#exhibit-input-container > ul").tabs();
			ex_load_links();
		});

		remove_callbacks = new Array();
		// This is the database for adding items
		db = Exhibit.Database.create();
	</script>

</head>
<body>

<div id="exhibit-input" class="postbox closed">
	<h3>DataPress</h3>
	<div class="inside">

	  <div id="exhibit-input-container">
		<p>Use the tabs below to configure your data exhibit.</p>
		<ul id="ex-tabs" class="outer-tabs">
			<li class="ui-tabs-selected"><a href="#exhibit-data">Data</a></li>
			<li class="wp-no-js-hidden"><a href="#exhibit-views">Views</a></li>
			<li class="wp-no-js-hidden"><a href="#exhibit-lenses">Lenses</a></li>
			<li class="wp-no-js-hidden"><a href="#exhibit-facets" >Facets</a></li>
    		<li class="wp-no-js-hidden"><a href="#exhibit-display" >Display</a></li>
		</ul>
		<div id="exhibit-data" class="outer-tabs-panel">
			<?php include("exhibit-inputbox-data.php") ?>
		</div>
		<div id="exhibit-views" class="outer-tabs-panel" style="display: none;">
			<?php include("exhibit-inputbox-views.php") ?>
		</div>
		<div id="exhibit-lenses" class="outer-tabs-panel" style="display: none;">
			<?php include("exhibit-inputbox-lenses.php") ?>
		</div>
		<div id="exhibit-facets" class="outer-tabs-panel" style="display: none;">
			<?php include("exhibit-inputbox-facets.php") ?>
		</div>
		<div id="exhibit-display" class="outer-tabs-panel" style="display: none;">
			<?php include("exhibit-inputbox-display.php") ?>
		</div>
	  </div>

	  <p align="right">
		<a href="#" class="addlink" onClick="appendToPost('{{Exhibit}}'); return false;">Insert Exhibit into Post</a>&nbsp;&nbsp;&nbsp;
		<a href="#" class="addlink"  onClick="appendToPost('{{Footnotes}}'); return false;">Insert Data Footnotes into Post</a>
	  </p>

	</div>
</div>

<script type="text/javascript">
	var saved_exhibit_id = "TEST ID";
</script>

</html>