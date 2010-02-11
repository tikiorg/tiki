<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// This script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

$a_style = $prefs['site_style'];
if (isset($_REQUEST["looksetup"])) {
	ask_ticket('admin-inc-look');
	if (isset($_REQUEST["site_style"])) {
		check_ticket('admin-inc-general');
		simple_set_value('site_style', 'style');
		simple_set_value('site_style', 'site_style');

		if (!isset($_REQUEST["site_style_option"]) || $_REQUEST["site_style_option"] == tra('None')) { // style has no options
			$_REQUEST["site_style_option"] = '';
		}
		check_ticket('admin-inc-general');
		simple_set_value("site_style_option", "style_option");
		simple_set_value("site_style_option", "site_style_option");
	}
	foreach($pref_byref_values as $britem) {
		byref_set_value($britem);
	}
} else { // just changed theme menu, so refill options
	if (isset($_REQUEST["site_style"]) && $_REQUEST["site_style"] != '') {
		$a_style = $_REQUEST["site_style"];
	}
}
$styles = $tikilib->list_styles();
$smarty->assign_by_ref("styles", $styles);
$smarty->assign('a_style', $a_style);
$smarty->assign_by_ref("style_options", $tikilib->list_style_options($a_style));
/**
 * @param $stl - style file name (e.g. thenews.css)
 * @param $opt - optional option file name
 * @return string path to thumbnail file
 */
function get_thumbnail_file($stl, $opt = '') { // find thumbnail if there is one
	global $tikilib;
	if (!empty($opt) && $opt != tr('None')) {
		$filename = preg_replace('/\.css$/i', '.png', $opt); // change .css to .png
		
	} else {
		$filename = preg_replace('/\.css$/i', '.png', $stl); // change .css to .png
		$opt = '';
	}
	return $tikilib->get_style_path($stl, $opt, $filename);
}
// find thumbnail if there is one
$thumbfile = get_thumbnail_file($a_style, $prefs['site_style_option']);
if (!empty($thumbfile)) {
	$smarty->assign('thumbfile', $thumbfile);
}
if ($prefs['feature_jquery'] == 'y') {
	// hash of themes and their options and their thumbnail images
	$js = 'var style_options = {';
	foreach($styles as $s) {
		$js.= "\n'$s':['" . get_thumbnail_file($s, '') . '\',{';
		$options = $tikilib->list_style_options($s);
		if ($options) {
			foreach($options as $o) {
				$js.= "'$o':'" . get_thumbnail_file($s, $o) . '\',';
			}
			$js = substr($js, 0, strlen($js) - 1) . '}';
		} else {
			$js.= '}';
		}
		$js.= '],';
	}
	$js = substr($js, 0, strlen($js) - 1);
	$js.= '};';
	// JS to handle theme/option changes client-side
	// the var (style_options) has to be declared in the same block for AJAX call scope 
	$none = tr('None');
	$headerlib->add_js(<<<JS
$js

\$jq(document).ready( function() {
	// pick up theme drop-down change
	\$jq('select[name=site_style]').change( function() {
		var ops = style_options[\$jq('select[name=site_style]').val()];
		var none = true;
		\$jq('select[name=site_style_option]').empty().attr('disabled','').attr('selectedIndex', 0);
		\$jq.each(ops[1], function(i, val) {
			\$jq('select[name=site_style_option]').append(\$jq(document.createElement('option')).attr('value',i).text(i));
			none = false;
		});
		if (none) {
			\$jq('select[name=site_style_option]').empty().attr('disabled','disabled').
					append(\$jq(document.createElement('option')).attr('value',"$none").text("$none"));
		}
		
		var t = \$jq('select[name=site_style]').val();
		var f = style_options[t][0];
		if (f) {
			\$jq('#style_thumb').fadeOut('fast').attr('src', f).fadeIn('fast').animate({'opacity': 1}, 'fast');
		} else {
			\$jq('#style_thumb').animate({'opacity': 0.3}, 'fast');
		}
	});
	\$jq('select[name=site_style_option]').change( function() {
		var t = \$jq('select[name=site_style]').val();
		var o = \$jq('select[name=site_style_option]').val();
		var f = style_options[t][1][o];
		if (f) {
			\$jq('#style_thumb').fadeOut('fast').attr('src', f).fadeIn('fast').animate({'opacity': 1}, 'fast');
		} else {
			\$jq('#style_thumb').animate({'opacity': 0.3}, 'fast');
		}
	});	
});
JS
	);
}

if (isset($_REQUEST["looksetup"])) {
	for ($i = 0, $count_feedback = count($tikifeedback); $i < $count_feedback; $i++) {
		if (substr($tikifeedback[$i]['name'], 0, 10) == 'site_style') { // if site_style or site_style_option
			// If the theme has changed, reload the page to use the new theme
			$location = 'location: tiki-admin.php?page=look';
			if ($prefs['feature_tabs'] == 'y') {
				$location.= "&cookietab=" . $_COOKIE['tab'];
			}
			header($location);
			exit;
		}
	}
}
