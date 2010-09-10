<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
 * smarty_block_textarea : add a textarea to a template.
 *
 * special params:
 *    _toolbars: if set to 'y', display toolbars above the textarea
 *    _previewConfirmExit: if set to 'n' doesn't warn about lost edits after preview
 *    _simple: if set to 'y' does no wysiwyg, auto_save, lost edit warning etc
 *
 * usage: {textarea id='my_area' name='my_area'}{tr}My Text{/tr}{/textarea}
 *
 */

function smarty_block_textarea($params, $content, &$smarty, $repeat) {
	global $prefs, $headerlib, $smarty;
	
	if ( $repeat ) return;

	// some defaults
	$params['_toolbars'] = isset($params['_toolbars']) ? $params['_toolbars'] : 'y';
	if ( $prefs['javascript_enabled'] != 'y') $params['_toolbars'] = 'n';

	if (!isset($params['_wysiwyg'])) {	// should not be set usually(?)
		include_once 'lib/setup/editmode.php';
		$params['_wysiwyg'] = $_SESSION['wysiwyg'];
	}
	
	$params['rows'] = !empty($params['rows']) || ($prefs['wysiwyg_ckeditor'] === 'y' && $params['_wysiwyg'] === 'y') ? $params['rows'] : 20;
	$params['cols'] = !empty($params['cols']) || ($prefs['wysiwyg_ckeditor'] === 'y' && $params['_wysiwyg'] === 'y') ? $params['cols'] : 80;
	$params['name'] = isset($params['name']) ? $params['name'] : 'edit';
	$params['id'] = isset($params['id']) ? $params['id'] : 'editwiki';
	$params['class'] = isset($params['class']) ? $params['class'] : 'wikiedit';
	
	// mainly for modules admin - preview is for the module, not the user module so don;t need to confirmExit
	$params['_previewConfirmExit'] = isset($params['_previewConfirmExit']) ? $params['_previewConfirmExit'] : 'y';
	
	$params['_simple'] = isset($params['_simple']) ? $params['_simple'] : 'n';
	
	if ( isset($params['_zoom']) && $params['_zoom'] == 'n' ) {
		$feature_template_zoom_orig = $prefs['feature_template_zoom'];
		$prefs['feature_template_zoom'] = 'n';
	}
	if ( ! isset($params['_section']) ) {
		global $section;
		$params['_section'] = $section ? $section: 'wiki page';
	}
	if ( ! isset($params['style']) ) $params['style'] = 'width:99%';
	$html = '';
	$html .= '<input type="hidden" name="mode_wysiwyg" value="" /><input type="hidden" name="mode_normal" value="" />';
	
	$auto_save_referrer = '';
	$auto_save_warning = '';
	$as_id = $params['id'];
	
	// fix for Firefox 3.5 and newer. *lite.css defined the document body as display:table, which seems to upset Firefox
	// and makes it lose it's selection info when the DOM changes (like when a picker or menu is generated)
	// this fixes it but apparently (according to *lite.css author Luci) will cause some layout issues:
	// He says: "it's necessary for expanding content (pushing right column) to the right properly"
	// but it looks fine to me
	if (preg_match('/Firefox\/(\d)+\.(\d)+/i', $_SERVER['HTTP_USER_AGENT'], $m) &&count($m) > 2 && $m[1] >=3 && ($m[2] >=5 || $m[1] > 3)) {
		//$headerlib->add_css('body {display: block; }', 10);	// xajax/loadComponent() doesn't re-parse CSS on AJAX loads (yet), so use JS instead
		$headerlib->add_jq_onready('$("body").css("display", "block")');
	}
	if ($prefs['feature_ajax'] == 'y' && $prefs['feature_ajax_autosave'] == 'y' && $params['_simple'] == 'n') {	// retrieve autosaved content
		$auto_save_referrer = ensureReferrer();

		if ((empty($_REQUEST['noautosave']) || $_REQUEST['noautosave'] != 'y') && (!isset($_REQUEST['mode_wysiwyg']) || $_REQUEST['mode_wysiwyg'] !== 'y')) {
			if (has_autosave($as_id, $auto_save_referrer)) {		//  and $params['preview'] == 0 -  why not?
				$auto_saved = str_replace("\n","\r\n", get_autosave($as_id, $auto_save_referrer));
				
				if ( strcmp($auto_saved, $content) != 0 ) {
					$content = $auto_saved;
					include_once('lib/smarty_tiki/block.self_link.php');
					include_once('lib/smarty_tiki/block.remarksbox.php');
					$msg = tra('If you want the saved version instead of this autosaved one').'&nbsp;'.smarty_block_self_link( array( 'noautosave'=>'y', '_ajax'=>'n'), tra('Click Here'), $smarty);
					$auto_save_warning = smarty_block_remarksbox( array( 'type'=>'info', 'title'=>tra('AutoSave')), $msg, $smarty)."\n";
				}
			}
		}
	}



	if ( $params['_wysiwyg'] == 'y' && $params['_simple'] == 'n') {
		
		if ($prefs['wysiwyg_ckeditor'] != 'y') {	// tried and tested FCKEditor
			global $url_path;
			include_once 'lib/tikifck.php';
			if (!isset($params['name']))       $params['name'] = 'fckedit';
			$fcked = new TikiFCK($params['name']);
		
			if (isset($content))			$fcked->Meat = $content;
			if (isset($params['Width']))	$fcked->Width = $params['Width'];
			if (isset($params['Height']))	$fcked->Height = $params['Height'];
			
			if ($prefs['feature_ajax'] == 'y' && $prefs['feature_ajax_autosave'] == 'y') {
				$fcked->Config['autoSaveSelf'] = $auto_save_referrer;		// this doesn't need to be the 'self' URI - just a unique reference for each page set up in ensureReferrer();
				$fcked->Config['autoSaveEditorId'] = $as_id;
			}
			if (isset($params['ToolbarSet'])) {
				$fcked->ToolbarSet = $params['ToolbarSet'];
			} else {
				$fcked->ToolbarSet = 'Tiki';
			}
			if ($prefs['feature_detect_language'] == 'y') {
				$fcked->Config['AutoDetectLanguage'] = true;
			} else {
				$fcked->Config['AutoDetectLanguage'] = false;
			}
			$fcked->Config['DefaultLanguage'] = $prefs['language'];
			$fcked->Config['CustomConfigurationsPath'] = $url_path.'setup_fckeditor.php?page=' . $_REQUEST['page']
						.(isset($params['_section']) ? '&section='.urlencode($params['_section']) : '');
			
			// this JS needs to be there before the iframe always - at end of page is too late
			
			$html .= $headerlib->wrap_js('
var fckEditorInstances = new Array();
function FCKeditor_OnComplete( editorInstance ) {
	fckEditorInstances[fckEditorInstances.length] = editorInstance;
	editorInstance.ResetIsDirty();
};');
		
			$html .= $fcked->CreateHtml();
			
			$html .= '<input type="hidden" name="wysiwyg" value="y" />';
			
			//$headerlib->add_jq_onready('$(".fckeditzone").resizable({ minWidth: $("#'.$as_id.'").width(), minHeight: 50 });');

		} else {									// new ckeditor implementation 2010

			if ($prefs['feature_ajax'] !== 'y' || $prefs['feature_ajax_autosave'] !== 'y' ||
					$prefs['feature_wiki_paragraph_formatting'] !== 'y' || $prefs['feature_wiki_paragraph_formatting_add_br'] !== 'y' ||
					$prefs['wysiwyg_wiki_parsed'] !== 'y') {
				
				// show dev notice
				include_once('lib/smarty_tiki/block.remarksbox.php');
				$msg = tra('<strong>Thank you for trying the new ckeditor implementation for Tiki 6</strong><br /><br />');
				
				global $tiki_p_admin;
				if ($tiki_p_admin) {
					$msg .= tra("Some of your preferences should be set differently for this to work at it's best. Please click this to apply the recommended profile:") .
					   ' <a href="tiki-admin.php?profile=WYSIWYG_6x&repository=&page=profiles&list=List">WYSIWYG_6x</a>';
				} else {
					$msg .= tra('Some of the settings at this site should be set differently for this to work best. Please ask the administrator to try this.');
				}
				
				$html .= smarty_block_remarksbox( array( 'type'=>'info', 'icon'=>'bricks', 'title'=>tra('Ckeditor Development Notice')), $msg, $smarty)."\n";
			}

			// set up ckeditor
			if (!isset($params['name'])) { $params['name'] = 'edit'; }
		
			//// for js debugging - copy _source from ckeditor distribution to libs/ckeditor to use
			//// note, this breaks ajax page load via wikitopline edit icon
			//$headerlib->add_jsfile('lib/ckeditor/ckeditor_source.js');
			$headerlib->add_js_config('CKEDITOR_BASEPATH = "'. $tikiroot . 'lib/ckeditor/";');
			$headerlib->add_jsfile('lib/ckeditor/ckeditor.js', 'minified');
			$headerlib->add_jsfile('lib/ckeditor/adapters/jquery.js', 'minified');
		
			include_once( $smarty->_get_plugin_filepath('function', 'toolbars') );
			$cktools = smarty_function_toolbars($params, $smarty);
			$cktools = json_encode($cktools);
			$cktools = substr($cktools, 1, strlen($cktools) - 2);	// remove surrouding [ & ]
			$cktools = str_replace(']],[[', '],"/",[', $cktools);	// add new row chars - done here so as not to break existing fck
			
			$html .= '<input type="hidden" name="wysiwyg" value="y" />';
			global $tikiroot;
			$headerlib->add_jq_onready('
CKEDITOR.config._TikiRoot = "'.$tikiroot.'";

CKEDITOR.config.extraPlugins += (CKEDITOR.config.extraPlugins ? ",tikiplugin" : "tikiplugin" );
CKEDITOR.plugins.addExternal( "tikiplugin", "'.$tikiroot.'lib/ckeditor_tiki/plugins/tikiplugin/");
CKEDITOR.config.ajaxAutoSaveTargetUrl = "'.$tikiroot.'tiki-auto_save.php";	// URL to post to (also used for plugin processing)
');	// before all
		
			if ($prefs['wysiwyg_htmltowiki'] === 'y') {
				$headerlib->add_jq_onready('
CKEDITOR.config.extraPlugins += (CKEDITOR.config.extraPlugins ? ",tikiwiki" : "tikiwiki" );
CKEDITOR.plugins.addExternal( "tikiwiki", "'.$tikiroot.'lib/ckeditor_tiki/plugins/tikiwiki/");
', 5);	// before dialog tools init (10)
			}
			if ($prefs['feature_ajax'] === 'y' && $prefs['feature_ajax_autosave'] === 'y') {
				$headerlib->add_jq_onready('
// --- config settings for the autosave plugin ---
CKEDITOR.config.extraPlugins += (CKEDITOR.config.extraPlugins ? ",autosave" : "autosave" );
CKEDITOR.plugins.addExternal( "autosave", "'.$tikiroot.'lib/ckeditor_tiki/plugins/autosave/");
CKEDITOR.config.ajaxAutoSaveRefreshTime = 30 ;			// RefreshTime
CKEDITOR.config.ajaxAutoSaveSensitivity = 2 ;			// Sensitivity to key strokes
register_id("'.$as_id.'"); auto_save();					// Register auto_save so it gets removed on submit
ajaxLoadingShow("'.$as_id.'");
', 5);	// before dialog tools init (10)
			}
			
			// work out current theme/option (surely in tikilib somewhere?)
			global $tikilib, $tc_theme, $tc_theme_option;
			$ckstyleoption = '';
			if (!empty($tc_theme)) {
				$ckstyle = $tikilib->get_style_path('', '', $tc_theme);
				if (!empty($tc_theme_option)) {
					$ckstyleoption = $tikilib->get_style_path($tc_theme, $tc_theme_option, $tc_theme_option);
				}
			} else {
				$ckstyle = $tikilib->get_style_path('', '', $prefs['style']);
				if (!empty($prefs['style_option'])) {
					$ckstyleoption = $tikilib->get_style_path($prefs['style'], $prefs['style_option'], $prefs['style_option']);
				}
			}

			$headerlib->add_jq_onready('
$( "#'.$as_id.'" ).ckeditor(CKeditor_OnComplete, {
	toolbar_Tiki: '.$cktools.',
	toolbar: "Tiki",
	language: "'.$prefs['language'].'",
	customConfig: "",
	autoSaveSelf: "'.$auto_save_referrer.'",		// unique reference for each page set up in ensureReferrer()
	font_names: "' . $prefs['wysiwyg_fonts'] . '",
	stylesSet: "tikistyles:' . $tikiroot . 'lib/ckeditor_tiki/tikistyles.js",
	templates_files: "' . $tikiroot . 'lib/ckeditor_tiki/tikitemplates.js",
	contentsCss: ["' . $tikiroot . $ckstyle . '","' . $tikiroot . $ckstyleoption . '"],
	skin: "' . ($prefs['wysiwyg_toolbar_skin'] != 'default' ? $prefs['wysiwyg_toolbar_skin'] : 'kama') . '",
	defaultLanguage: "' . $prefs['language'] . '",
	language: "' . ($prefs['feature_detect_language'] === 'y' ? '' : $prefs['language']) . '",
	'. (empty($params['cols']) ? 'height: 400,' : '') .'
	contentsLangDirection: "' . ($prefs['feature_bidi'] === 'y' ? 'rtl' : 'ltr') . '"
});
', 20);	// after dialog tools init (10)

			$html .= '<textarea class="wikiedit" name="'.$params['name'].'" id="'.$as_id.'" style="visibility:hidden;';	// missing closing quotes, closed in condition
			if (empty($params['cols'])) {	
				$html .= 'width:100%;'. (empty($params['cols']) ? 'height:500px;' : '') .'"';
			} else {
				$html .= '" cols="'.$params['cols'].'"';
			}
			if (!empty($params['rows'])) {	
				$html .= ' rows="'.$params['rows'].'"';
			}
			$html .= '>'.htmlspecialchars($content).'</textarea>';
			
			$headerlib->add_js('
var ckEditorInstances = new Array();
function CKeditor_OnComplete() {
	if (typeof ajaxLoadingHide == "function") { ajaxLoadingHide(); }
	ckEditorInstances[ckEditorInstances.length] = this;
	this.resetDirty();
};');
		}	// end both wysiwyg setups

	} else {
		
		// setup for wiki editor
		
		$textarea_attributes = '';
		foreach ( $params as $k => $v ) {
			if ( $k == 'id' || $k == 'name' || $k == 'class' || $k == '_toolbars' ) {
				$smarty->assign('textarea_'.$k, $v);
			} elseif ( $k[0] != '_' ) {
				$textarea_attributes .= ' '.$k.'="'.$v.'"';
			}
		}
		if (empty($textarea_id)) { $textarea_id = $params['id']; }
		if ( $textarea_attributes != '' ) {
			$smarty->assign('textarea_attributes', $textarea_attributes);
		}
		if ( isset($params['_zoom']) && $params['_zoom'] == 'n' ) {
			$prefs['feature_template_zoom'] = $feature_template_zoom_orig;
		}
		
		if ($prefs['feature_ajax'] == 'y' && $prefs['feature_ajax_autosave'] == 'y' && $params['_simple'] == 'n') {
			$headerlib->add_jq_onready("register_id('$textarea_id'); auto_save();");
			$headerlib->add_js("var autoSaveId = '$auto_save_referrer';");	// onready is too late...
		}

		$smarty->assign_by_ref('pagedata', htmlspecialchars($content));
		$smarty->assign('comments', isset($params['comments']) ? $params['comments'] : 'n');
		$html .= $smarty->fetch('wiki_edit.tpl');

		$html .= "\n".'<input type="hidden" name="rows" value="'.$params['rows'].'"/>'
			."\n".'<input type="hidden" name="cols" value="'.$params['cols'].'"/>'
			."\n".'<input type="hidden" name="wysiwyg" value="n" />';

	}	// wiki or wysiwyg

	$js_editconfirm = '';
	$js_editlock = '';

	if ($params['_simple'] == 'n') {
// Display edit time out

		$js_editlock .= "
// edit timeout warnings
function editTimerTick() {
	editTimeElapsedSoFar++;
	
	var seconds = editTimeoutSeconds - editTimeElapsedSoFar;
	
	if (editTimerWarnings == 0 && seconds <= 60 && window.editorDirty) {
		alert('".addslashes(tra('Your edit session will expire in')).' 1 '.tra('minute').'.'.
				addslashes(tra('You must PREVIEW or SAVE your work now, to avoid losing your edits.'))."');
		editTimerWarnings++;
	} else if (seconds <= 0) {
		clearInterval(editTimeoutIntervalId);
		editTimeoutIntervalId = 0;
		window.status = '".addslashes(tra('Your edit session has expired'))."';
	} else if (seconds < 600) {		// don't bother until 5 minutes to go
		\$('#edittimeout').parents('.rbox:first').fadeIn();
		window.status = '".addslashes(tra('Your edit session will expire in:'))."' +\" \" + + Math.floor(seconds / 60) + ': ' + ((seconds % 60 < 10) ? '0' : '') + (seconds % 60);
		if (seconds % 60 == 0 && \$('#edittimeout')) {
			\$('#edittimeout').text(Math.floor(seconds / 60));
		}
	}
}

\$('document').ready( function() {
	editTimeoutIntervalId = setInterval(editTimerTick, 1000);
	\$('#edittimeout').parents('.rbox:first').hide();
} );
var editTimeoutSeconds = ".ini_get('session.gc_maxlifetime').";
var editTimeElapsedSoFar = 0;
var editTimeoutIntervalId;
var editTimerWarnings = 0;
// end edit timeout warnings

";

		$js_editconfirm .= "
function confirmExit() {
	if (window.needToConfirm && typeof fckEditorInstances != 'undefined' && fckEditorInstances.length > 0) {
		var version2 = (typeof CKeditor_OnComplete == 'undefined');
		for(var ed = 0; ed < fckEditorInstances.length; ed++) {
			if ((version2 && fckEditorInstances[ed].IsDirty()) || (!version2 && fckEditorInstances[ed].checkDirty())) {
				window.editorDirty = true;
				break;
			}
		}
	}
	if (window.needToConfirm && window.editorDirty) {
		return '".tra('You are about to leave this page. Changes since your last save will be lost. Are you sure you want to exit this page?')."';
	}
}

window.onbeforeunload = confirmExit;

\$('document').ready( function() {
	// attach dirty function to all relevant inputs etc
	if ('$as_id' != 'editwiki') {	// modules admin exception
		\$('#$as_id').change( function () { if (!editorDirty) { editorDirty = true; } });
	} else {
		\$(\$('#$as_id').attr('form')).find('input, textarea, select').change( function () { if (!editorDirty) { editorDirty = true; } });
	}
});

window.needToConfirm = true;
window.editorDirty = ".(isset($_REQUEST["preview"]) && $params['_previewConfirmExit'] == 'y' ? 'true' : 'false').";
";

		if ($prefs['feature_wysiwyg'] == 'y' && $prefs['wysiwyg_optional'] == 'y') {
			$js_editconfirm .= '
function switchEditor(mode, form) {
	window.needToConfirm=false;
	var w;
	if (mode=="wysiwyg") {
		$(form).find("input[name=mode_wysiwyg]").val("y");
		$(form).find("input[name=wysiwyg]").val("y");
	} else {
		$(form).find("input[name=mode_normal]").val("y");
		$(form).find("input[name=wysiwyg]").val("n");
	}
	form.submit();
}';
		}
		
		if ( $prefs['feature_jquery_ui'] == 'y' && $params['_wysiwyg'] != 'y') {	// show hidden parent before applying resizable
			$js_editconfirm .= "
var hiddenParents = \$('#$as_id').parents('fieldset:hidden:last');
if (hiddenParents.length) { hiddenParents.show(); }
\$('#$as_id').resizable( { minWidth: \$('#$as_id').width(), minHeight: 50 });
if (hiddenParents.length) { hiddenParents.hide(); }
";
		}
			
		if( $prefs['wiki_timeout_warning'] == 'y' ) {
			$headerlib->add_js($js_editlock);
		}
		$headerlib->add_js($js_editconfirm);
	}	// end if ($params['_simple'] == 'n')

	return $auto_save_warning.$html;
}
