<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/wizard/wizard.php');

/**
 * The Wizard's editor type selector handler 
 */
class AdminWizardLookAndFeel extends Wizard 
{
	function pageTitle ()
	{
		return tra('Set up Look & Feel');
	}
	function isEditable ()
	{
		return true;
	}
	
	function onSetupPage ($homepageUrl) 
	{
		global $prefs;
		$smarty = TikiLib::lib('smarty');
		$tikilib = TikiLib::lib('tiki');
		$themelib = TikiLib::lib('theme');
		$csslib = TikiLib::lib('css');
		$headerlib = TikiLib::lib('header');
		// Run the parent first
		parent::onSetupPage($homepageUrl);

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

		$theme_layouts = TikiLib::lib('css')->list_layouts();
		$smarty->assign('theme_layouts', $theme_layouts);

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

			$js .= 'var theme_layouts = ';
			foreach ($themes as $theme => $value) {
				$theme_layouts[$theme] = $csslib->list_user_selectable_layouts($theme);
				$options = $themelib->list_theme_options($theme);
				if ($options) {
					foreach ($options as $option) {
						$theme_layouts[$theme.':'.$option] = $csslib->list_user_selectable_layouts($theme,$option);
					}
				}
			}
			// JS to handle theme/option changes client-side
			// the var (theme_options) has to be declared in the same block for AJAX call scope
			$none = json_encode(tr('None'));
			$headerlib->add_cssfile('themes/base_files/feature_css/admin.css');
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

	setupThemeSelects(\$('#wizardBody select[name=theme]'), \$('#wizardBody select[name=theme_option]'), true);
	setupThemeSelects(\$('#wizardBody select[name=theme_admin]'), \$('#wizardBody select[name=theme_option_admin]'));

	var setupThemeLayouts = function (themeDropDown, optionDropDown, layoutDropDown) {
		themeDropDown,optionDropDown.change( function() {
			var theme_name = themeDropDown.val();
			if (optionDropDown.val()){
				theme_name += ":" + optionDropDown.val();
			}
			var layouts = theme_layouts[theme_name];
			var current = layoutDropDown.val();
			layoutDropDown.empty();
			//if no theme, it means it's the admin dropdown and is set to site theme. default to site layout
			if (!theme_name){
				layoutDropDown.append(\$('<option/>').attr('value','site_layout').text('Site layout'));
				layoutDropDown.attr('disabled',true);
				layoutDropDown.val('site_layout');
			} else {
				layoutDropDown.attr('disabled',false);
				\$.each(layouts, function(i, val) {
					layoutDropDown.append(\$('<option/>').attr('value',i).text(val));
				});

				//try setting the option to the previously selected option and if no layout matched, set to 'basic'
				layoutDropDown.val(current);
				if (!layoutDropDown.val()){
					layoutDropDown.val('basic');
				}
			}

			layoutDropDown.change();

		}).change();
	};

	setupThemeLayouts(\$('#wizardBody select[name=theme]'), \$('#wizardBody select[name=theme_option]'), \$('#wizardBody select[name=site_layout]') );
	setupThemeLayouts(\$('#wizardBody select[name=theme_admin]'), \$('#wizardBody select[name=theme_option_admin]'), \$('#wizardBody select[name=site_layout_admin]') );
});
JS
			);
		}

//        // find thumbnail if there is one
//		$a_style = $prefs['site_style'];
//			// just changed theme menu, so refill options
//		if (isset($_REQUEST['style']) && $_REQUEST['style'] != '') {
//			$a_style = $_REQUEST['style'];
//		}
//		$thumbfile = $this->get_thumbnail_file($a_style, $prefs['site_style_option']);
//		if (empty($thumbfile)) {
//			$thumbfile = $this->get_thumbnail_file($a_style);
//		}
//		if (empty($thumbfile)) {
//			$thumbfile = 'img/trans.png';
//		}
//		if (!empty($thumbfile)) {
//			$smarty->assign('thumbfile', $thumbfile);
//		}
//
//		$styles = $tikilib->list_styles();
//		$smarty->assign_by_ref('styles', $styles);
//		$smarty->assign('a_style', $a_style);
//		$smarty->assign('style_options', $tikilib->list_style_options($a_style));
//
//		$this->setupThumbnailScript($styles);
//
		return true;
	}

	function getTemplate()
	{
		$wizardTemplate = 'wizard/admin_look_and_feel.tpl';
		return $wizardTemplate;
	}

	function onContinue ($homepageUrl) 
	{
		// Run the parent first
		$changes = parent::onContinue($homepageUrl);
		if (array_key_exists('style', $changes) || array_key_exists('style_option', $changes)) {
			$query = array('url' => $_REQUEST['url'], 'wizard_step' => $_REQUEST['wizard_step'], 'showOnLogin' => $_REQUEST['showOnLogin']);
			TikiLib::lib('access')->redirect($_SERVER['PHP_SELF'] . '?' . http_build_query($query, '', '&') );
		}
	}
	
	/**
	 * @param $stl - style file name (e.g. thenews.css)
	 * @param $opt - optional option file name
	 * @return string path to thumbnail file
	 */
	function get_thumbnail_file($stl, $opt = '') // find thumbnail if there is one
	{
		$tikilib = TikiLib::lib('tiki');
		if (!empty($opt) && $opt != tr('None')) {
			$filename = preg_replace('/\.css$/i', '.png', $opt); // change .css to .png

		} else {
			$filename = preg_replace('/\.css$/i', '.png', $stl); // change .css to .png
			$opt = '';
		}
		return $tikilib->get_style_path($stl, $opt, $filename);
	}	

	function setupThumbnailScript($styles)
	{
		global	$prefs;
		$headerlib = TikiLib::lib('header');
		$tikilib = TikiLib::lib('tiki');
		
		if ($prefs['feature_jquery'] == 'y') {
			// hash of themes and their options and their thumbnail images
			$js = 'var style_options = {';
			foreach ($styles as $s) {
				$js.= "\n'$s':['" . $this->get_thumbnail_file($s, '') . '\',{';
				$options = $tikilib->list_style_options($s);
				if ($options) {
					foreach ($options as $o) {
						$js.= "'$o':'" . $this->get_thumbnail_file($s, $o) . '\',';
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
			$none = json_encode(tr('None'));

			$headerlib->add_js(
<<<JS
$js

\$(document).ready( function() {
	var setupStyleSelects = function (styleDropDown, optionDropDown, showPreview) {
		// pick up theme drop-down change
		styleDropDown.change( function() {
			var ops = style_options[styleDropDown.val()];
			var none = true;
			var current = optionDropDown.val();
			optionDropDown.empty().attr('disabled',false)
					.append(\$('<option/>').attr('value',$none).text($none));
			if (styleDropDown.val()) {
				\$.each(ops[1], function(i, val) {
					optionDropDown.append(\$('<option/>').attr('value',i).text(i.replace(/\.css\$/, '')));
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
				var t = styleDropDown.val();
				var o = optionDropDown.val();
				var f = style_options[t][1][o];

				if ( ! f ) {
					f = style_options[t][0];
				}

				if (f) {
					\$('#style_thumb').fadeOut('fast').attr('src', f).fadeIn('fast').animate({'opacity': 1}, 'fast');
				} else {
					\$('#style_thumb').animate({'opacity': 0.3}, 'fast');
				}
			}
		});
	};
	setupStyleSelects(\$('select[name=style]'), \$('select[name=style_option]'), true);
	setupStyleSelects(\$('select[name=style_admin]'), \$('select[name=style_admin_option]'));
});
JS
			);
		}
	}
}
