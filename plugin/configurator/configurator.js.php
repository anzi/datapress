<?php
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

header("Content-type: text/javascript");

if (!$guessurl = site_url())
	$guessurl = wp_guess_url();
$baseuri = $guessurl;
$exhibituri = $baseuri . '/wp-content/plugins/datapress';

print <<<EOF

function ex_add_head_link(uri, kind, remove_id) {
	var link = "";
	if (kind == "google-spreadsheet") {
		var link = SimileAjax.jQuery('<link id = "' + remove_id + '" rel="exhibit/data" type="application/jsonp" href="' + uri + '" ex:converter="googleSpreadsheets" />');
	}
	else if (kind == "application/json") {
		var link = SimileAjax.jQuery('<link id = "' + remove_id + '" rel="exhibit/data" type="application/json" href="$exhibituri/proxy/parrot.php?url=' + uri + '" />');
	}
	SimileAjax.jQuery('head').append(link);
}

function addExhibitElementLink(listId, caption, prefix, fields, field_display) {
	var next_id = SimileAjax.jQuery('#' + listId + ' > li').size();
	var liid = listId + "_" + next_id;
	var liid_remove = liid + "_remove";
	var opStr = "";
	opStr = opStr + "<li id='" + liid + "'>" + caption + " ";
	SimileAjax.jQuery.each(fields, function(key, value) {
	    field_name = prefix + "_" + next_id + "_" + key;
	    if (field_display && (key in field_display)) {
	        opStr = opStr + field_display[key](key, value, field_name);
	    } else {
    		var field = "<input type='hidden' name='" + field_name + "' value='" + value + "' />";
	    	opStr = opStr + field;
	    }
	});
	opStr = opStr + "[ <a href='#' onclick='removeExhibitElementLink(\"" + liid + "\",\"" + liid_remove + "\"); return false;'>remove</a> ]";
	opStr = opStr + "</li>";
	SimileAjax.jQuery('#' + listId).append(opStr);
	return liid_remove;
}

function popup(url) {
	window.open(url);
}

function removeExhibitElementLink(liid, liid_remove) {
    SimileAjax.jQuery("#" + liid).remove();
    SimileAjax.jQuery("#" + liid_remove).remove();
    ex_load_links();
}

function appendToPost(myValue) {
	window.tinyMCE.execInstanceCommand("content", "mceInsertContent",true,myValue);
}

function appendToLens(myValue) {
	// var win = window.dialogArguments || opener || parent || top;
	jQuery('#lens-text').append('.' + myValue);
	// win.tinyMCE.execInstanceCommand("lens-text", "mceInsertContent",true,myValue);
}

function ex_data_types_changed(e, arr) {
	// Get all types
	var types = db._types;
	var props = db._properties;
	var type_choice = "<option selected value=''> - </option>";
	var prop_choice = "<option selected value=''> - </option>";

	for (var key in types) {
		if (key != "Item") {
			var id = types[key].getID();
			var label = types[key].getLabel();		
			type_choice = type_choice + "<option value='" + id + "'>" + label + "</option>";			
		}
	}	
	for (var key in props) {
		prop_choice = prop_choice + "<option value='" + key + "'>" + key + "</option>";
	}	

	SimileAjax.jQuery('.alltypebox').html(type_choice);		
	SimileAjax.jQuery('.allpropbox').html(prop_choice);
}

function ex_load_links() {
    db = Exhibit.Database.create();
	db.loadDataLinks(ex_data_types_changed);		
}
EOF
?>