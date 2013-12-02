<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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
		global	$smarty, $prefs, $tikilib;

		// Run the parent first
		parent::onSetupPage($homepageUrl);

		// find thumbnail if there is one
		$a_style = $prefs['site_style'];
			// just changed theme menu, so refill options
		if (isset($_REQUEST['style']) && $_REQUEST['style'] != '') {
			$a_style = $_REQUEST['style'];
		}
		$thumbfile = $this->get_thumbnail_file($a_style, $prefs['site_style_option']);
		if (empty($thumbfile)) {
			$thumbfile = $this->get_thumbnail_file($a_style);
		}
		if (empty($thumbfile)) {
			$thumbfile = 'img/trans.png';
		}
		if (!empty($thumbfile)) {
			$smarty->assign('thumbfile', $thumbfile);
		}

		$styles = $tikilib->list_styles();
		$smarty->assign_by_ref('styles', $styles);
		$smarty->assign('a_style', $a_style);
		$smarty->assign('style_options', $tikilib->list_style_options($a_style));

		$this->setupThumbnailScript($styles);
		
		// Assign the page tempalte
		$wizardTemplate = 'wizard/admin_look_and_feel.tpl';
		$smarty->assign('wizardBody', $wizardTemplate);
		
		return true;
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
		global $tikilib;
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
		global	$prefs, $tikilib, $headerlib;
		
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
