<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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

	function setupInlineEditor($pageName)
	{
		global $tikiroot, $prefs, $user;

		// Validate user permissions
		$tikilib = TikiLib::lib('tiki');
		if (!$tikilib->user_has_perm_on_object($user, $pageName, 'wiki page', 'edit')) {
			// Check if the user has inline edit permissions
			if (!$tikilib->user_has_perm_on_object($user, $pageName, 'wiki page', 'edit_inline')) {
				// User has no permission
				return;
			}
		}

		// If the page uses flagged revisions, check if the page can be edited.
		//	Inline edit sessions can cross page boundaries, thus the page attempts to start in inline edit mode
		if ($prefs['flaggedrev_approval'] == 'y') {
			$flaggedrevisionlib = TikiLib::lib('flaggedrevision');
			if ($flaggedrevisionlib->page_requires_approval($pageName)) {
				if (!isset($_REQUEST['latest']) || $_REQUEST['latest'] != '1') {
					// The page cannot be edited
					return;
				}
			}
		}

		if ( !empty(self::$ckEditor) ) {
			// Inline editor is already initialized
			return;
		}
		self::$ckEditor = 'ckeditor4';

		$headerlib = TikiLib::lib('header');

		$headerlib->add_js_config('window.CKEDITOR_BASEPATH = "'. $tikiroot . 'vendor/ckeditor/ckeditor/";')
			->add_jsfile('vendor/ckeditor/ckeditor/ckeditor.js', true)
			->add_js('window.CKEDITOR.config._TikiRoot = "'.$tikiroot.'";', 1);

		// Inline editing config
		$skin = $prefs['wysiwyg_toolbar_skin'] != 'default' ? $prefs['wysiwyg_toolbar_skin'] : 'moono';

		// the toolbar TODO refactor as duplicated from below
		$smarty = TikiLib::lib('smarty');

		$info = $tikilib->get_page_info($pageName, false);	// Don't load page data.
		$params = array(
			'_wysiwyg' => 'y',
			'area_id' => 'page-data',
			'comments' => '',
			'is_html' => $info['is_html'],	// temporary element id
			'switcheditor' => 'n',
		);

		$smarty->loadPlugin('smarty_function_toolbars');
		$cktools = smarty_function_toolbars($params, $smarty);
		$cktools[0][count($cktools[0]) - 1][] = 'inlinesave';
		$cktools[0][count($cktools[0]) - 1][] = 'inlinecancel';
		$cktools = json_encode($cktools);
		$cktools = substr($cktools, 1, strlen($cktools) - 2); // remove surrouding [ & ]
		$cktools = str_replace(']],[[', '],"/",[', $cktools); // add new row chars - done here so as not to break existing f/ck
		require_once('lib/toolbars/toolbarslib.php');
		$ckeformattags = ToolbarCombos::getFormatTags($info['is_html'] ? 'html' : 'wiki');


		$headerlib->add_jsfile('lib/ckeditor_tiki/tiki-ckeditor.js')
			->add_js(
				'// --- config settings for the inlinesave plugin ---
window.CKEDITOR.config.extraPlugins = "";
window.CKEDITOR.config.extraPlugins += (window.CKEDITOR.config.extraPlugins ? ",inlinesave" : "inlinesave" );
window.CKEDITOR.plugins.addExternal( "inlinesave", "'.$tikiroot.'lib/ckeditor_tiki/plugins/inlinesave/");
window.CKEDITOR.config.extraPlugins += (window.CKEDITOR.config.extraPlugins ? ",inlinecancel" : "inlinecancel" );
window.CKEDITOR.plugins.addExternal( "inlinecancel", "'.$tikiroot.'lib/ckeditor_tiki/plugins/inlinecancel/");
window.CKEDITOR.config.ajaxSaveRefreshTime = 30 ;			// RefreshTime
window.CKEDITOR.config.contentsLangDirection = ' . ($prefs['feature_bidi'] === 'y' ? '"rtl"' : '"ui"') . ';
// --- plugins
window.CKEDITOR.config.autoSavePage = "' . addcslashes($pageName, '"') . '";		// unique reference for each page
window.CKEDITOR.config.allowedContent = true;
// --- other configs

window.CKEDITOR.config.skin = "'.$skin.'";
window.CKEDITOR.disableAutoInline = true;
window.CKEDITOR.config.toolbar = ' .$cktools.';
//window.CKEDITOR.config.format_tags = "' . $ckeformattags . '";

'
);
		$headerlib->add_jsfile('lib/ckeditor_tiki/tikilink_dialog.js');
		$headerlib->add_js(
			'//window.CKEDITOR.config.extraPlugins += (window.CKEDITOR.config.extraPlugins ? ",tikiplugin" : "tikiplugin" );
//			window.CKEDITOR.plugins.addExternal( "tikiplugin", "'.$tikiroot.'lib/ckeditor_tiki/plugins/tikiplugin/");',
			5
		);

	}


	function setUpEditor($is_html, $dom_id, $params = array(), $auto_save_referrer = '', $full_page = true)
	{
		global $tikiroot, $prefs;
		$headerlib = TikiLib::lib('header');

		$headerlib->add_js('window.CKEDITOR.config.extraPlugins = "' . $prefs['wysiwyg_extra_plugins'] . '";');
		$headerlib->add_js_config('window.CKEDITOR_BASEPATH = "'. $tikiroot . 'vendor/ckeditor/ckeditor/";')
			//// for js debugging - copy _source from ckeditor distribution to libs/ckeditor to use
			//// note, this breaks ajax page load via wikitopline edit icon
			->add_jsfile('vendor/ckeditor/ckeditor/ckeditor.js', true)
			->add_js('window.CKEDITOR.config._TikiRoot = "'.$tikiroot.'";', 1);

		$headerlib->add_js(
			'window.CKEDITOR.config.extraPlugins += (window.CKEDITOR.config.extraPlugins ? ",divarea" : "divarea" );',
			5
		);

		if ($full_page) {
			$headerlib->add_jsfile('lib/ckeditor_tiki/tikilink_dialog.js');
			$headerlib->add_js(
				'window.CKEDITOR.config.extraPlugins += (window.CKEDITOR.config.extraPlugins ? ",tikiplugin" : "tikiplugin" );
				window.CKEDITOR.plugins.addExternal( "tikiplugin", "'.$tikiroot.'lib/ckeditor_tiki/plugins/tikiplugin/");',
				5
			);
			$headerlib->add_css('.ui-front {z-index: 9999;}');	// so the plugin edit dialogs show up
		}
		if (!$is_html && $full_page) {
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

		$ckeformattags = ToolbarCombos::getFormatTags($is_html ? 'html' : 'wiki');

		// js to initiate the editor
		$ckoptions = '{
	toolbar: ' .$cktools.',
	customConfig: "",
	autoSaveSelf: "'.addcslashes($auto_save_referrer, '"').'",		// unique reference for each page set up in ensureReferrer()
	font_names: "' . trim($prefs['wysiwyg_fonts']) . '",
	format_tags: "' . $ckeformattags . '",
	stylesSet: "tikistyles:' . $tikiroot . 'lib/ckeditor_tiki/tikistyles.js",
	templates_files: ["' . $tikiroot . 'lib/ckeditor_tiki/tikitemplates.js"],
	skin: "' . ($prefs['wysiwyg_toolbar_skin'] != 'default' ? $prefs['wysiwyg_toolbar_skin'] : 'moono') . '",
	defaultLanguage: "' . $this->languageMap($prefs['language']) . '",
 	contentsLangDirection: "' . ($prefs['feature_bidi'] === 'y' ? 'rtl' : 'ltr') . '",
	language: "' . ($prefs['feature_detect_language'] === 'y' ? '' : $this->languageMap($prefs['language'])) . '"
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

		return $ckoptions;
	}

	/** Map between tiki lang codes and ckeditor's (mostly the same)
	 *
	 * @param string $lang	Tiki language code
	 * @return string		mapped language code - defaults to the same if not found
	 */
	private function languageMap ($lang)
	{

		$langMap = array(
			//'ar' => 'ar',			// Arabic = United Arab Emirates - English ok?
			//'bg' => 'bg',			// Bulgarian
			//'ca' => 'ca',			// Catalan
			'cn' => 'zh-cn',		// China - Simplified Chinese
			//'cs' => 'cs',			// Czech
			//'cy' => 'cy',			// Welsh
			//'da' => 'da',			// Danish
			'de' => 'de',			// Germany - German
			'en-uk' => 'en-gb',		// United Kingdom - English
			//'en' => 'en',			// United States - English
			//'es' => 'es',			// Spain - Spanish
			//'el' => 'el',			// Greek
			//'fa' => 'fa',			// Farsi
			//'fi' => 'fi',			// Finnish
			'fj' => 'en',			// Fijian	(not supported)
			//'fr' => 'fr',			// France - French
			'fy-NL' => 'nl',		// Netherlands - Dutch
			'gl' => 'es',				// Galician
			//'he' => 'he',			// Israel - Hebrew
			//'hr' => 'hr',			// Croatian
			//'id' => 'id',			// Indonesian
			//'is' => 'is',			// Icelandic
			//'it' => 'it',			// Italy - Italian
			'iu' => 'en',			// Inuktitut	(not supported)
			'iu-ro' => 'en',		// Inuktitut (Roman)	(not supported)
			'iu-iq' => 'en',		// Iniunnaqtun	(not supported)
			//'ja' => 'ja',			// Japan - Japanese
			//'ko' => 'ko',			// Korean
			//'hu' => 'hu',			// Hungarian
			//'lt' => 'lt',			// Lithuanian
			'nds' => 'de',			// Low German
			//'nl' => 'nl',			// Netherlands - Dutch
			//'no' => 'no',			// Norway - Norwegian
			//'pl' => 'pl',			// Poland - Polish
			//'pt' => 'pt',			// Portuguese
			//'pt-br' => 'pt-br',	// Brazil - Portuguese
			//'ro' => 'ro',			// Romanian
			'rm' => 'en',			// Romansh	(not supported)
			//'ru' => 'ru',			// Russia - Russian
			'sb' => 'en',			// Pijin Solomon	(not supported)
			//'si' => 'si',			// Sinhala
			//'sk' => 'sk',			// Slovak
			//'sl' => 'sl',			// Slovene
			//'sq' => 'sq',			// Albanian
			//'sr-latn' => 'sr-latn',	// Serbian Latin
			//'sv' => 'sv',			// Sweden - Swedish
			'tv' => 'en',			// Tuvaluansr-latn
			//'tr' => 'tr',			// Turkey - Turkish
			'tw' => 'zh',			// Taiwan - Traditional Chinese
			//'uk' => 'uk',			// Ukrainian
			//'vi' => 'vi',			// Vietnamese
		);

		return isset($langMap[$lang]) ? $langMap[$lang] : $lang;
	}


}

global $wysiwyglib;
$wysiwyglib = new WYSIWYGLib();

