<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// This script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

$a_style = $prefs['site_style'];
if (isset($_REQUEST["looksetup"])) {
	ask_ticket('admin-inc-look');
	if (isset($_REQUEST['style'])) {
		check_ticket('admin-inc-general');

		if (!isset($_REQUEST['style_option']) || $_REQUEST['style_option'] == tra('None')) { // style has no options
			$_REQUEST['style_option'] = '';
		}
		check_ticket('admin-inc-general');
	}
} else { // just changed theme menu, so refill options
	if (isset($_REQUEST['style']) && $_REQUEST['style'] != '') {
		$a_style = $_REQUEST['style'];
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
	$none = json_encode( tr('None') );
	$headerlib->add_js(<<<JS
$js

\$(document).ready( function() {
	var optionDropDown = \$('select[name=style_option]');
	var styleDropDown = \$('select[name=style]');
	// pick up theme drop-down change
	styleDropDown.change( function() {
		var ops = style_options[styleDropDown.val()];
		var none = true;
		var current = optionDropDown.val();
		optionDropDown.empty().attr('disabled',false)
			.append(\$('<option/>').attr('value',$none).text($none));
		\$.each(ops[1], function(i, val) {
			optionDropDown.append(\$('<option/>').attr('value',i).text(i));
			none = false;
		});
		optionDropDown.val(current);
		if (none) {
			optionDropDown.attr('disabled',true);
		}
		
		optionDropDown.change();
	}).change();
	optionDropDown.change( function() {
		var t = styleDropDown.val();
		var o = optionDropDown.val();
		var f = style_options[t][1][o];

		if( ! f ) {
			f = style_options[t][0];
		}

		if (f) {
			\$('#style_thumb').fadeOut('fast').attr('src', f).fadeIn('fast').animate({'opacity': 1}, 'fast');
		} else {
			\$('#style_thumb').animate({'opacity': 0.3}, 'fast');
		}
	});	
});
JS
	);
}

/* Theme generator for Tiki 7+ */

if ($prefs['feature_themegenerator'] === 'y') {
	include_once 'lib/themegenlib.php';
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$reload = true;
		if (!empty($_REQUEST['tg_new_theme']) && !empty($_REQUEST['tg_edit_theme_name'])) {
			$tg_edit_theme_name = $_REQUEST['tg_edit_theme_name'];
			$themegenlib->saveNewTheme($tg_edit_theme_name);
		} else if (!empty($_REQUEST['tg_delete_theme'])) {
			$themegenlib->deleteCurrentTheme();
		} else if (!empty($_REQUEST['tg_swaps']) && !empty($_REQUEST['tg_preview'])) {
			$themegenlib->previewCurrentTheme($_REQUEST['tg_css_file'], $_REQUEST['tg_swaps']);
		} else if (!empty($_REQUEST['tg_swaps']) && !empty($_REQUEST['tg_change_file'])) {
			//$themegenlib->previewCurrentTheme($_REQUEST['tg_css_file'], $_REQUEST['tg_swaps']);
			$reload = false;
		} else if (!empty($_REQUEST['tg_swaps']) && !empty($_REQUEST['tg_css_file'])) {
			$themegenlib->updateCurrentTheme($_REQUEST['tg_css_file'], $_REQUEST['tg_swaps']);
		} else {
			$reload = false;
		}
	}
	
	$themegenlib->setupEditor();
	//$auto_query_args[] = 'tg_css_file';
	
	
}

if (isset($_REQUEST["looksetup"])) {
	for ($i = 0, $count_feedback = count($tikifeedback); $i < $count_feedback; $i++) {
		if (substr($tikifeedback[$i]['name'], 0, 5) == 'style') { // if style or style_option
			// If the theme has changed, reload the page to use the new theme
			$reload = true;
		}
	}
}

if ($reload) {
		$location = 'location: tiki-admin.php?page=look';
		if ($prefs['feature_tabs'] === 'y' && isset($_COOKIE['tab']) && $_COOKIE['tab'] > 1) {
			$location.= "&cookietab=" . $_COOKIE['tab'];
		}
//		if ($prefs['feature_themegenerator'] === 'y') {
//			if (!empty($_REQUEST['tg_css_file'])) {
//				$location.= "&tg_css_file=" . $_REQUEST['tg_css_file'];
//			}
//			if (!empty($_REQUEST['tg_preview'])) {
//				$location.= "&tg_preview=true";
//			}
//		}
		header($location);
		exit;
}