<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
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
	if ($prefs['mobile_feature'] === 'y' && $prefs['mobile_mode'] === 'y') {
		$params['_toolbars'] = 'n';
	}
	if ( $prefs['javascript_enabled'] != 'y') $params['_toolbars'] = 'n';

	if (!isset($params['_wysiwyg'])) {	// should not be set usually(?)
		include_once 'lib/setup/editmode.php';
		$params['_wysiwyg'] = $_SESSION['wysiwyg'];
	}
	
	if ($params['_wysiwyg'] !== 'y') {
		$params['rows'] = !empty($params['rows']) ? $params['rows'] : 20;
		$params['cols'] = !empty($params['cols']) ? $params['cols'] : 80;
	}
	$params['name'] = isset($params['name']) ? $params['name'] : 'edit';
	$params['id'] = isset($params['id']) ? $params['id'] : 'editwiki';
	$params['area_id'] = isset($params['area_id']) ? $params['area_id'] : $params['id'];	// legacy param for toolbars?
	$params['class'] = isset($params['class']) ? $params['class'] : 'wikiedit';

	//codemirror integration
	$params['data-codemirror'] = isset($params['codemirror']) ? $params['codemirror'] : '';
	$params['data-syntax'] = isset($params['syntax']) ? $params['syntax'] : '';
	//keep params html5 friendly
	unset($params['codemirror']);
	unset($params['syntax']);
	
	// mainly for modules admin - preview is for the module, not the user module so don;t need to confirmExit
	$params['_previewConfirmExit'] = isset($params['_previewConfirmExit']) ? $params['_previewConfirmExit'] : 'y';
	
	$params['_simple'] = isset($params['_simple']) ? $params['_simple'] : 'n';
	
	if ( ! isset($params['section']) ) {
		global $section;
		$params['section'] = $section ? $section: 'wiki page';
	}
	if ( ! isset($params['style']) ) $params['style'] = 'width:99%';
	$html = '';
	$html .= '<input type="hidden" name="mode_wysiwyg" value="" /><input type="hidden" name="mode_normal" value="" />';
	
	$auto_save_referrer = '';
	$auto_save_warning = '';
	$as_id = $params['id'];
	
	include_once('lib/smarty_tiki/block.remarksbox.php');
	$editWarning = $prefs['wiki_timeout_warning'] == 'y' && isset($smarty->_tpl_vars['page']) && $smarty->_tpl_vars['page'] != 'sandbox';
	if ($params['_simple'] === 'n' && $editWarning) {
		$html .= smarty_block_remarksbox( array( 'type'=>'tip', 'title'=>tra('Tip')),
			tra('This edit session will expire in') .
				' <span id="edittimeout">' . (ini_get('session.gc_maxlifetime') / 60) .'</span> '. tra('minutes') . '. ' .
				tra('<strong>Preview</strong> (if available) or <strong>Save</strong> your work to restart the edit session timer'),
			$smarty)."\n";
		if ($prefs['javascript_enabled'] === 'y') {
			$html = str_replace('<div class="clearfix rbox tip">', '<div class="clearfix rbox tip" style="display:none;">', $html);	// quickfix to stop this box appearing before doc.ready
		}
	}

	if ($prefs['feature_ajax'] == 'y' && $prefs['ajax_autosave'] == 'y' && $params['_simple'] == 'n') {	// retrieve autosaved content
		require_once("lib/ajax/autosave.php");
		include_once('lib/smarty_tiki/block.self_link.php');
		$auto_save_referrer = ensureReferrer();
		if (empty($_REQUEST['autosave'])) {
			$_REQUEST['autosave'] = 'n';
		}
		if (has_autosave($as_id, $auto_save_referrer)) {		//  and $params['preview'] == 0 -  why not?
			$auto_saved = str_replace("\n","\r\n", get_autosave($as_id, $auto_save_referrer));
			if ( strcmp($auto_saved, $content) === 0 ) {
				$auto_saved = '';
			}
			if (empty($auto_saved) || (isset($_REQUEST['mode_wysiwyg']) && $_REQUEST['mode_wysiwyg'] === 'y')) {	// switching modes, ignore auto save
				remove_save($as_id, $auto_save_referrer);
			} else {
				$msg = '<div class="mandatory_star"><span class="autosave_message">'.tra('There is an autosaved version of this content, to use it instead of this saved one').'</span>&nbsp;' .
							'<span class="autosave_message_2" style="display:none;">'.tra('If you want the saved version instead of this autosaved draft').'</span>' .
							smarty_block_self_link( array( '_ajax'=>'n', '_onclick' => 'toggle_autosaved(\''.$as_id.'\',\''.$auto_save_referrer.'\');return false;'), tra('click here'), $smarty)."</div>";
				$auto_save_warning = smarty_block_remarksbox( array( 'type'=>'info', 'title'=>tra('AutoSave')), $msg, $smarty)."\n";
			}
		}
		$headerlib->add_jq_onready("register_id('$as_id','" . addcslashes($auto_save_referrer, "'") . "');");
		$headerlib->add_js("var autoSaveId = '" . addcslashes($auto_save_referrer, "'") . "';");
		$smarty->assign( 'autosave_js', "remove_save('$as_id','" . addcslashes($auto_save_referrer, "'") . "');");	// for cancel buttons etc that don't submit the form
	} else {
		$smarty->assign( 'autosave_js', '');
	}

	if ( $params['_wysiwyg'] == 'y' && $params['_simple'] == 'n') {
		
		// new ckeditor implementation 2010
		if ($prefs['feature_ajax'] !== 'y' || $prefs['ajax_autosave'] !== 'y' ||
				$prefs['feature_wiki_paragraph_formatting'] !== 'y' || $prefs['feature_wiki_paragraph_formatting_add_br'] !== 'y' ||
				$prefs['wysiwyg_wiki_parsed'] !== 'y') {
			
			// show dev notice
			include_once('lib/smarty_tiki/block.remarksbox.php');
			$msg = tra('<strong>Thank you for trying the new ckeditor implementation for Tiki 6</strong><br /><br />');
			
			global $tiki_p_admin;
			if ($tiki_p_admin) {
				$profile_link = 'tiki-admin.php?profile=WYSIWYG_6x&repository=http%3A%2F%2Fprofiles.tiki.org%2Fprofiles&page=profiles&list=List';
				$msg .= tra("Some of your preferences should be set differently for this to work at it's best. Please click this to apply the recommended profile:") .
				   ' <a href="'.$profile_link.'">WYSIWYG_6x</a>';
			} else {
				$msg .= tra('Some of the settings at this site should be set differently for this to work best. Please ask the administrator to try this.');
			}
			
			$html .= smarty_block_remarksbox( array( 'type'=>'info', 'icon'=>'bricks', 'title'=>tra('Ckeditor Development Notice')), $msg, $smarty)."\n";
		}

		// set up ckeditor
		if (!isset($params['name'])) { $params['name'] = 'edit'; }
	
		global $tikiroot;
		$headerlib->add_js_config('window.CKEDITOR_BASEPATH = "'. $tikiroot . 'lib/ckeditor/";');
		//// for js debugging - copy _source from ckeditor distribution to libs/ckeditor to use
		//// note, this breaks ajax page load via wikitopline edit icon
		//$headerlib->add_jsfile('lib/ckeditor/ckeditor_source.js');
		$headerlib->add_jsfile('lib/ckeditor/ckeditor.js',0 , true);
		$headerlib->add_jsfile('lib/ckeditor/adapters/jquery.js', 0, true);
		$headerlib->add_jsfile('lib/ckeditor_tiki/tikilink_dialog.js');
	
		include_once( $smarty->_get_plugin_filepath('function', 'toolbars') );
		$cktools = smarty_function_toolbars($params, $smarty);
		$cktools = json_encode($cktools);
		$cktools = substr($cktools, 1, strlen($cktools) - 2);	// remove surrouding [ & ]
		$cktools = str_replace(']],[[', '],"/",[', $cktools);	// add new row chars - done here so as not to break existing f/ck
		
		$html .= '<input type="hidden" name="wysiwyg" value="y" />';
		$headerlib->add_jq_onready('
window.CKEDITOR.config._TikiRoot = "'.$tikiroot.'";

window.CKEDITOR.config.extraPlugins += (window.CKEDITOR.config.extraPlugins ? ",tikiplugin" : "tikiplugin" );
window.CKEDITOR.plugins.addExternal( "tikiplugin", "'.$tikiroot.'lib/ckeditor_tiki/plugins/tikiplugin/");
window.CKEDITOR.config.ajaxAutoSaveTargetUrl = "'.$tikiroot.'tiki-auto_save.php";	// URL to post to (also used for plugin processing)
');	// before all
		
		if ($prefs['wysiwyg_htmltowiki'] === 'y') {
			$headerlib->add_jq_onready('
window.CKEDITOR.config.extraPlugins += (window.CKEDITOR.config.extraPlugins ? ",tikiwiki" : "tikiwiki" );
window.CKEDITOR.plugins.addExternal( "tikiwiki", "'.$tikiroot.'lib/ckeditor_tiki/plugins/tikiwiki/");
', 5);	// before dialog tools init (10)
		}
		if ($prefs['feature_ajax'] === 'y' && $prefs['ajax_autosave'] === 'y') {
			$headerlib->add_jq_onready('
// --- config settings for the autosave plugin ---
window.CKEDITOR.config.extraPlugins += (window.CKEDITOR.config.extraPlugins ? ",autosave" : "autosave" );
window.CKEDITOR.plugins.addExternal( "autosave", "'.$tikiroot.'lib/ckeditor_tiki/plugins/autosave/");
window.CKEDITOR.config.ajaxAutoSaveRefreshTime = 30 ;			// RefreshTime
window.CKEDITOR.config.ajaxAutoSaveSensitivity = 2 ;			// Sensitivity to key strokes
window.CKEDITOR.config.contentsLangDirection = ' . ($prefs['feature_bidi'] === 'y' ? '"rtl"' : '"ui"') . '
register_id("'.$as_id.'","'.addcslashes($auto_save_referrer, '"').'");	// Register auto_save so it gets removed on submit
ajaxLoadingShow("'.$as_id.'");
', 5);	// before dialog tools init (10)
		}
			
		// work out current theme/option (surely in tikilib somewhere?)
		global $tikilib, $tc_theme, $tc_theme_option;
		$ckstyleoption = '';
		if (!empty($tc_theme)) {
			$ckstyle = $tikiroot . $tikilib->get_style_path('', '', $tc_theme);
			if (!empty($tc_theme_option)) {
				$ckstyle .= '","' . $tikiroot . $tikilib->get_style_path($tc_theme, $tc_theme_option, $tc_theme_option);
			}
		} else {
			$ckstyle = $tikiroot . $tikilib->get_style_path('', '', $prefs['style']);
			if (!empty($prefs['style_option']) && $tikilib->get_style_path($prefs['style'], $prefs['style_option'], $prefs['style_option'])) {
				$ckstyle .= '","' . $tikiroot . $tikilib->get_style_path($prefs['style'], $prefs['style_option'], $prefs['style_option']);
			}
		}

			$headerlib->add_jq_onready('
$( "#'.$as_id.'" ).ckeditor(CKeditor_OnComplete, {
	toolbar_Tiki: '.$cktools.',
	toolbar: "Tiki",
	language: "'.$prefs['language'].'",
	customConfig: "",
	autoSaveSelf: "'.addcslashes($auto_save_referrer, '"').'",		// unique reference for each page set up in ensureReferrer()
	font_names: "' . trim($prefs['wysiwyg_fonts']) . '",
	stylesSet: "tikistyles:' . $tikiroot . 'lib/ckeditor_tiki/tikistyles.js",
	templates_files: "' . $tikiroot . 'lib/ckeditor_tiki/tikitemplates.js",
	contentsCss: ["' . $ckstyle . '"],
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

	} else {		// end of if ( $params['_wysiwyg'] == 'y' && $params['_simple'] == 'n')
		
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
		$smarty->assign_by_ref('pagedata', htmlspecialchars($content));
		$smarty->assign('comments', isset($params['comments']) ? $params['comments'] : $params['_simple'] === 'y' ? 'y' : 'n');
		$smarty->assign('switcheditor', isset($params['switcheditor']) ? $params['switcheditor'] : 'n');
		$smarty->assign('toolbar_section', $params['section']);
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
var editTimeoutSeconds = ".((int)ini_get('session.gc_maxlifetime')).";
var editTimeElapsedSoFar = 0;
var editTimeoutIntervalId;
var editTimerWarnings = 0;
var editTimeoutTipIsDisplayed = false;
var minutes;

// edit timeout warnings
function editTimerTick() {
	editTimeElapsedSoFar++;

	var seconds = editTimeoutSeconds - editTimeElapsedSoFar;
	var edittimeout = \$('#edittimeout');

	if ( edittimeout && seconds <= 300 ) {
		if ( ! editTimeoutTipIsDisplayed ) {
			edittimeout.parents('.rbox:first').fadeIn();
			editTimeoutTipIsDisplayed = true;
		}
		if ( seconds > 0 && seconds % 60 == 0 ) {
			minutes = seconds / 60;
			edittimeout.text( minutes );
		} else if ( seconds <= 0 ) {
			edittimeout.parents('.rbox-data:first').text('".addslashes(tra('Your edit session has expired'))."');
		}
	}

	if (editTimerWarnings == 0 && seconds <= 60 && window.editorDirty) {
		alert('".addslashes(tra('Your edit session will expire in:')).' 1 '.tra('minute').'. '.
				addslashes(tra('You must PREVIEW or SAVE your work now, to avoid losing your edits.'))."');
		editTimerWarnings++;
	} else if (seconds <= 0) {
		clearInterval(editTimeoutIntervalId);
		editTimeoutIntervalId = 0;
		window.status = '".addslashes(tra('Your edit session has expired'))."';
	} else if (seconds <= 300) {		// don't bother until 5 minutes to go
		window.status = '".addslashes(tra('Your edit session will expire in:'))."' + \" \" + minutes + ':' + ((seconds % 60 < 10) ? '0' : '') + (seconds % 60);
	}
}

\$('document').ready( function() {
	editTimeoutIntervalId = setInterval(editTimerTick, 1000);
	\$('#edittimeout').parents('.rbox:first').hide();
} );

// end edit timeout warnings

";

		$js_editconfirm .= "
function confirmExit() {
	if (window.needToConfirm) {
		if (typeof window.ckEditorInstances != 'undefined' && window.ckEditorInstances) {
			for( var e = 0; e < window.ckEditorInstances.length; e++ ) {
				if (window.ckEditorInstances[e].mayBeDirty && window.ckEditorInstances[e].checkDirty()) {
					window.editorDirty = true;
				}
			}
		}
		if (window.editorDirty) {
			return '".tra('You are about to leave this page. Changes since your last save will be lost. Are you sure you want to exit this page?')."';
		}
	}
}

window.onbeforeunload = confirmExit;

\$('document').ready( function() {
	// attach dirty function to all relevant inputs etc for wiki/newsletters, blog, article and trackers (trackers need {teaxtarea} implementing)
	if ('$as_id' === 'editwiki' || '$as_id' === 'blogedit' || '$as_id' === 'body' || '$as_id'.indexOf('area_') > -1) {
		\$(\$('#$as_id').attr('form')).find('input, textarea, select').change( function () { if (!window.editorDirty) { window.editorDirty = true; } });
	} else {	// modules admin exception, only attach to this textarea, although these should be using _simple mode
		\$('#$as_id').change( function () { if (!window.editorDirty) { window.editorDirty = true; } });
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
	
	if (typeof CodeMirror === 'undefined') { //so as not to conflict with CodeMirror resize
		\$('#$as_id')
			.resizable( {
				minWidth: \$('#$as_id').width(),
				minHeight: 50
		});
	}
	
	if (hiddenParents.length) { hiddenParents.hide(); }
";
		}
			
		if( $editWarning ) {
			$headerlib->add_js($js_editlock);
		}
		$headerlib->add_js($js_editconfirm);
	}	// end if ($params['_simple'] == 'n')

	return $auto_save_warning.$html;
}
