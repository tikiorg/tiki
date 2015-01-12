<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// This script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}
global $prefs;
$themelib = TikiLib::lib('theme');

//handle case when changing the themes in the Look and Feel settings panel
$a_theme = $prefs['theme'];
if (isset($_REQUEST['looksetup'])) {
	ask_ticket('admin-inc-look');
	if (isset($_REQUEST['theme'])) {
		check_ticket('admin-inc-general');

		if (!isset($_REQUEST['theme_option']) || $_REQUEST['theme_option'] = '') {
			// theme has no options
			$_REQUEST['theme_option'] = '';
		}
		check_ticket('admin-inc-general');
	}
} else {
	// just changed theme menu, so refill options
	if (isset($_REQUEST['theme']) && $_REQUEST['theme'] != '') {
		$a_theme = $_REQUEST['theme'];
	}
}

$themes = $themelib->list_themes();	
$smarty->assign_by_ref('themes', $themes);
$theme_options = $themelib->list_theme_options($a_theme);
$smarty->assign('theme_options', $theme_options);

// get thumbnail if there is one
$thumbfile = $themelib->get_thumbnail_file($prefs['site_theme'], $prefs['site_theme_option']);
if (empty($thumbfile)) {
	$thumbfile = $themelib->get_thumbnail_file($prefs['site_theme']);
}
if (empty($thumbfile)) {
	$thumbfile = 'img/trans.png';
}
$smarty->assign('thumbfile', $thumbfile);

// hash of themes and their options and their thumbnail images
if ($prefs['feature_jquery'] == 'y') {
	$js = 'var theme_options = {';
	foreach ($themes as $theme => $value) {
		$js.= "\n'$theme':['" . $themelib->get_thumbnail_file($theme, '') . '\',{';
		$options = $themelib->list_theme_options($theme);
		if ($options) {
			foreach ($options as $option) {
				$js.= "'$option':'" . $themelib->get_thumbnail_file($theme, $option) . '\',';
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
	// the var (theme_options) has to be declared in the same block for AJAX call scope
	$none = json_encode(tr('None'));
	$headerlib->add_js(
<<<JS
$js

\$(document).ready( function() {

	var setupThemeSelects = function (themeDropDown, optionDropDown, showPreview) {
		// pick up theme drop-down change
		themeDropDown.change( function() {
			var ops = theme_options[themeDropDown.val()];
			var none = true;
			var current = optionDropDown.val();
			optionDropDown.empty().attr('disabled',false)
					.append(\$('<option/>').attr('value','').text($none));
			if (themeDropDown.val()) {
				\$.each(ops[1], function(i, val) {
					optionDropDown.append(\$('<option/>').attr('value',i).text(i));
					none = false;
				});
			}
			optionDropDown.val(current);
			if (none) {
				optionDropDown.attr('disabled',true);
			}
			optionDropDown.change();
			if (jqueryTiki.chosen) {
				optionDropDown.trigger("chosen:updated");
			}
		}).change();
		optionDropDown.change( function() {
			if (showPreview !== undefined) {
				var t = themeDropDown.val();
				var o = optionDropDown.val();
				var f = theme_options[t][1][o];

				if ( ! f ) {
					f = theme_options[t][0];
				}

				if (f) {
					\$('#theme_thumb').fadeOut('fast').attr('src', f).fadeIn('fast').animate({'opacity': 1}, 'fast');
				} else {
					\$('#theme_thumb').animate({'opacity': 0.3}, 'fast');
				}
			}
		});
	};

	setupThemeSelects(\$('.tab-content select[name=theme]'), \$('.tab-content select[name=theme_option]'), true);
	setupThemeSelects(\$('.tab-content select[name=theme_admin]'), \$('.tab-content select[name=theme_option_admin]'));
});
JS
	);
}

//Theme generator
$reload = false;
if ($prefs['themegenerator_feature'] === 'y') {
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

}
