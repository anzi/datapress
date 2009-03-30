<?php
function show_datapress_html() { 
	if (!$guessurl = site_url())
		$guessurl = wp_guess_url();
	$baseuri = $guessurl;
	$exhibituri = $baseuri . '/wp-content/plugins/datapress';
	
	/* -------------------------------------------------
	 * Load up the exhibit if it exists
	 * ------------------------------------------------- */
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
	<form id="exhibit-config-form" action="javascript:return false;">
	<div id="exhibit-input">
		<div class="inside">
		  <div id="exhibit-input-container">

			<ul id="ex-tabs" class="outer-tabs">
				<li class="ui-tabs-selected"><a href="#exhibit-data">Add Data</a></li>
				<li class="spacer">&gt;</li>
				<li class="wp-no-js-hidden"><a href="#exhibit-views">Add Visualizations</a></li>
				<li class="spacer">&gt;</li>
				<li class="wp-no-js-hidden"><a href="#exhibit-facets" >Add Facets</a></li>
				<li class="spacer">&gt;</li>
	    		<li class="wp-no-js-hidden"><a href="#exhibit-display" >Configure Display</a></li>
				<li class="spacer">&gt;</li>
				<li class="wp-no-js-hidden" ><a href="#exhibit-lenses">Lenses (Advanced)</a></li>
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
			<input type="hidden" value="<?php echo $exhibitID ?>" name="exhibitid" />
			<input type="hidden" value="save_exhibit_configuration" name="action" />
			<input id="save_btn" type="button" class="button savebutton" name="save" value="<?php echo attribute_escape( __( 'Save' ) ); ?>" />
		  </p>
		</div>
	</div>
	</form>
	<script>
		$(document).ready(function(){		    
			function postExhibit(e) {
				var paste_exhibit = false;
				var paste_footnotes = false;
				
				if (e.target.name == "save_insert") {
					paste_exhibit = true;
				}
				if (e.target.name == "save_insert_footnotes") {
					paste_exhibit = true;
					paste_footnotes = true;
				}
				jQuery.post("<?php bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php",
				            jQuery("#exhibit-config-form").serialize(),
					        function(data) {
								var win = window.dialogArguments || opener || parent || top;
								win.set_post_exhibit(data);
								win.add_exhibit_token_and_exit();
							});
							
			}
			
			$('#save_btn').bind("click", postExhibit);
			$('#save_insert_btn').bind("click", postExhibit);
			$('#save_insert_footnotes_btn').bind("click", postExhibit);
			
			remove_callbacks = new Array();
			db = Exhibit.Database.create();
			var category_tabs = jQuery("#exhibit-input-container > ul").tabs();
			ex_load_links();
		});
	</script>
<?php 
}



/**
 * {@internal Missing Short Description}}
 *
 * Wrap iframe content (produced by $content_func) in a doctype, html head/body
 * etc any additional function args will be passed to content_func.
 *
 * @since unknown
 *
 * @param unknown_type $content_func
 */
function datapress_iframe($content_func /* ... */) {
	if (!$guessurl = site_url())
		$guessurl = wp_guess_url();
	$baseuri = $guessurl;
	$exhibituri = $baseuri . '/wp-content/plugins/datapress';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
<title><?php bloginfo('name') ?> &rsaquo; <?php _e('Uploads'); ?> &#8212; <?php _e('WordPress'); ?></title>
<?php
wp_enqueue_style( 'global' );
wp_enqueue_style( 'wp-admin' );
wp_enqueue_style( 'colors' );
if ( 0 === strpos( $content_func, 'media' ) )
	wp_enqueue_style( 'media' );

?>
<script type="text/javascript">
//<![CDATA[
function addLoadEvent(func) {if ( typeof wpOnload!='function'){wpOnload=func;}else{ var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}}
//]]>
</script>
<?php
do_action('admin_print_styles');
do_action('admin_print_scripts');
do_action('admin_head');
if ( is_string($content_func) )
	do_action( "admin_head_{$content_func}" );
?>
</head>
<body<?php if ( isset($GLOBALS['body_id']) ) echo ' id="' . $GLOBALS['body_id'] . '"'; ?>>
<?php
	$args = func_get_args();
	$args = array_slice($args, 1);
	call_user_func_array($content_func, $args);
?>
</body>
</html>
<?php
}

function show_datapress_configurator() {
	wp_enqueue_script('common');
	wp_enqueue_script('exhibit-api');
	wp_enqueue_script('dp-jquery');
	wp_enqueue_script('dp-jquery-ui');
	wp_enqueue_script('dp-jquery-tabs');
	wp_enqueue_script('dp-tinymce');
	wp_enqueue_script('dp-tinymce-langs');	
	wp_enqueue_script('configurator');	
	wp_enqueue_script('base64');
	wp_enqueue_style( 'global' );
	wp_enqueue_style( 'wp-admin' );
	wp_enqueue_style( 'colors' );
	wp_enqueue_style( 'media' );
	wp_enqueue_style('dp-configurator');
	echo datapress_iframe('show_datapress_html');
	die();
}
?>
