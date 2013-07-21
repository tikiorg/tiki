<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/*
 * Shared functions for tiki implementation of nkeditor (v3.6.2)
 */

class WYSIWYGLib
{
	static $ckEditor = null;

	function setupInlineEditor()
	{
		global $tikiroot, $prefs;

		if( !empty(self::$ckEditor) ) {
			// Inline editor is already initialized
			return;
		}
		self::$ckEditor = 'ckeditor4';
		// $ckEditor = 'ckeditor'; ... to revert also fix CKEDITOR.addCss in the plugin.js files
		
		global $tikiroot, $prefs;
		$headerlib = TikiLib::lib('header');
		
		$headerlib->add_js_config('window.CKEDITOR_BASEPATH = "'. $tikiroot . 'vendor/ckeditor/ckeditor/";')
			//// for js debugging - copy _source from ckeditor distribution to libs/ckeditor to use
			//// note, this breaks ajax page load via wikitopline edit icon
			//->add_jsfile('lib/ckeditor/ckeditor_source.js');
			->add_jsfile('vendor/ckeditor/ckeditor/ckeditor.js', 0, true)
			// ->add_jsfile('vendor/ckeditor/ckeditor/adapters/jquery.js', 0, true)
			->add_js('window.CKEDITOR.config._TikiRoot = "'.$tikiroot.'";', 1)
			;

		// Inline editing config
		$headerlib->add_js('window.CKEDITOR.inline( "page-data");', 5)
			->add_js('window.CKEDITOR.disableAutoInline = true;', 5)
			->add_js('$("#page-data").attr("contenteditable", true);')
			->add_js('window.CKEDITOR.config.toolbar = [ [ "Bold", "Italic", "Underline", "-", "Table", "-", "Image", "Link", "inlinesave" ] ];')
			->add_js('window.CKEDITOR.config.skin = "moono";')
		;
		
		$dom_id = "page-data";
		$headerlib->add_js(
			'// --- config settings for the autosave plugin ---
window.CKEDITOR.config.ajaxSaveTargetUrl = "'.$tikiroot.'tiki-auto_save.php";	// URL to post to (also used for plugin processing)
window.CKEDITOR.config.extraPlugins += (window.CKEDITOR.config.extraPlugins ? ",inlinesave" : "inlinesave" );
window.CKEDITOR.plugins.addExternal( "inlinesave", "'.$tikiroot.'lib/ckeditor_tiki/plugins/tikiinline/");
window.CKEDITOR.config.ajaxSaveRefreshTime = 30 ;			// RefreshTime
window.CKEDITOR.config.contentsLangDirection = ' . ($prefs['feature_bidi'] === 'y' ? '"rtl"' : '"ui"') . ';
');
// register_id("'.$dom_id.'","'.addcslashes(($_SERVER["HTTP_REFERER"]), '"').'");	// Register auto_save so it gets removed on submit
// ajaxLoadingShow("'.$dom_id.'");
// window.CKEDITOR.config.ajaxSaveSensitivity = 2 ;			// Sensitivity to key strokes
	
	}

	function shutdownInlineEditor()
	{
		global $tikiroot, $prefs;
		$headerlib = TikiLib::lib('header');
		$css = '$("#page-data").attr("contenteditable", false);';
		$headerlib->add_js($css);
		self::$ckEditor = '';
	}
	
	
	function setUpEditor($is_html, $dom_id, $params = array(), $auto_save_referrer = '', $full_page = true)
	{
        static $notallreadyloaded =true;

		global $tikiroot, $prefs;
		$headerlib = TikiLib::lib('header');
        if ($notallreadyloaded) {
			$headerlib->add_js_config('window.CKEDITOR_BASEPATH = "'. $tikiroot . 'vendor/ckeditor/ckeditor/";')
				//// for js debugging - copy _source from ckeditor distribution to libs/ckeditor to use
				//// note, this breaks ajax page load via wikitopline edit icon
				->add_jsfile('vendor/ckeditor/ckeditor/ckeditor.js', 0, true)
				->add_js('window.CKEDITOR.config._TikiRoot = "'.$tikiroot.'";', 1);

			$headerlib->add_js(
				'window.CKEDITOR.config.extraPlugins += (window.CKEDITOR.config.extraPlugins ? ",divarea" : "divarea" );',
				5
			);
		}

		if ($notallreadyloaded && $full_page) {
			$headerlib->add_jsfile('lib/ckeditor_tiki/tikilink_dialog.js');
			$headerlib->add_js(
				'window.CKEDITOR.config.extraPlugins += (window.CKEDITOR.config.extraPlugins ? ",tikiplugin" : "tikiplugin" );
				window.CKEDITOR.plugins.addExternal( "tikiplugin", "'.$tikiroot.'lib/ckeditor_tiki/plugins/tikiplugin/");',
				5
			);
		}
		if ($notallreadyloaded && !$is_html && $full_page) {
			$headerlib->add_js(
				'window.CKEDITOR.config.extraPlugins += (window.CKEDITOR.config.extraPlugins ? ",tikiwiki" : "tikiwiki" );
				window.CKEDITOR.plugins.addExternal( "tikiwiki", "'.$tikiroot.'lib/ckeditor_tiki/plugins/tikiwiki/");',
				5
			);	// before dialog tools init (10)

		}
		if ($auto_save_referrer && $prefs['feature_ajax'] === 'y' &&
				$prefs['ajax_autosave'] === 'y' && $params['autosave'] == 'y') {

			$headerlib->add_js(
				'// --- config settings for the autosave plugin ---
window.CKEDITOR.config.ajaxAutoSaveTargetUrl = "'.$tikiroot.'tiki-auto_save.php";	// URL to post to (also used for plugin processing)
window.CKEDITOR.config.extraPlugins += (window.CKEDITOR.config.extraPlugins ? ",autosave" : "autosave" );
window.CKEDITOR.plugins.addExternal( "autosave", "'.$tikiroot.'lib/ckeditor_tiki/plugins/autosave/");
window.CKEDITOR.config.ajaxAutoSaveRefreshTime = 30 ;			// RefreshTime
window.CKEDITOR.config.contentsLangDirection = ' . ($prefs['feature_bidi'] === 'y' ? '"rtl"' : '"ui"') . ';
window.CKEDITOR.config.ajaxAutoSaveSensitivity = 2 ;			// Sensitivity to key strokes
register_id("'.$dom_id.'","'.addcslashes($auto_save_referrer, '"').'");	// Register auto_save so it gets removed on submit
ajaxLoadingShow("'.$dom_id.'");
', 5
			);	// before dialog tools init (10)
		}

		// finally the toolbar
		$smarty = TikiLib::lib('smarty');

		$params['area_id'] = empty($params['area_id']) ? $dom_id : $params['area_id'];

		$smarty->loadPlugin('smarty_function_toolbars');
		$cktools = smarty_function_toolbars($params, $smarty);
		$cktools = json_encode($cktools);
		$cktools = substr($cktools, 1, strlen($cktools) - 2); // remove surrouding [ & ]
		$cktools = str_replace(']],[[', '],"/",[', $cktools); // add new row chars - done here so as not to break existing f/ck

		if (is_object(ToolbarCombos)) {
			$ckeformattags = ToolbarCombos::getFormatTags('html');
		}

		// js to initiate the editor
		$ckoptions = '{
	toolbar: ' .$cktools.',
	language: "'.$prefs['language'].'",
	customConfig: "",
	autoSaveSelf: "'.addcslashes($auto_save_referrer, '"').'",		// unique reference for each page set up in ensureReferrer()
	font_names: "' . trim($prefs['wysiwyg_fonts']) . '",
	format_tags: "' . $ckeformattags . '",
	stylesSet: "tikistyles:' . $tikiroot . 'lib/ckeditor_tiki/tikistyles.js",
	templates_files: ["' . $tikiroot . 'lib/ckeditor_tiki/tikitemplates.js"],
	skin: "' . ($prefs['wysiwyg_toolbar_skin'] != 'default' ? $prefs['wysiwyg_toolbar_skin'] : 'moono') . '",
	defaultLanguage: "' . $prefs['language'] . '",
 	contentsLangDirection: "' . ($prefs['feature_bidi'] === 'y' ? 'rtl' : 'ltr') . '",
	language: "' . ($prefs['feature_detect_language'] === 'y' ? '' : $prefs['language']) . '"
	'. (empty($params['rows']) ? ',height: "' . (empty($params['height']) ? '400' : $params['height']) . '"' : '') .'
	, resize_dir: "both"
	, allowedContent: true
}';

//	, extraAllowedContent: {		// TODO one day, currently disabling the "Advanced Content Filter" as tiki plugins are too complex
//		"div span": {
//			classes: "tiki_plugin",
//			attributes: "data-plugin data-syntax data-args data-body"
//		}
//	}

        $notallreadyloaded=false;
		return $ckoptions;
	}

	function setUpJisonEditor($is_html, $dom_id, $params = array(), $auto_save_referrer = '', $full_page = true)
	{
		global $tikiroot, $headerlib;
		$headerlib
			->add_cssfile('lib/aloha-editor/css/aloha.css')
			->add_jsfile('lib/aloha-editor/lib/require.js')
			->add_jsfile_with_attr(
				'lib/aloha-editor/lib/aloha.js',
				array(
					'data-aloha-plugins' => 'common/ui,
									common/table,
									common/list,
									common/link,
									common/highlighteditables,
									common/block,
									common/undo,
									common/image,
									common/paste,
									common/commands,
									common/abbr,
									common/format'
				)
			)
			->add_jq_onready(
				"Aloha.ready(function() {
					Aloha.settings.jQuery = jQuery;
					Aloha.bind( 'aloha-add-markup', function( jEvent, markup ) {
						markup.attr('data-t', 'b');
			        });
					$('#$dom_id').aloha();
				});",
				10
			);

		return "<script>Aloha ={};Aloha.settings = {};Aloha.settings.bundles = {};Aloha.settings.bundles['tiki'] = '../../aloha-editor_tiki/plugins';</script>";
	}
}

global $wysiwyglib;
$wysiwyglib = new WYSIWYGLib();

