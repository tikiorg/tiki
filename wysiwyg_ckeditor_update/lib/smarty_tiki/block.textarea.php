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
	
	$params['rows'] = isset($params['rows']) ? $params['rows'] : 20;
	$params['cols'] = isset($params['cols']) ? $params['cols'] : 80;
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
//	if ($params['_wysiwyg'] != 'y') {
		$as_id = $params['id'];
//	} else {
//		$as_id = $params['name'];
//	}
	if ($prefs['feature_ajax'] == 'y' && $prefs['feature_ajax_autosave'] == 'y' && $params['_simple'] == 'n') {	// retrieve autosaved content
		$auto_save_referrer = ensureReferrer();

		if (empty($_REQUEST['noautosave']) || $_REQUEST['noautosave'] != 'y') {
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
			
			$headerlib->add_jq_onready('$(".fckeditzone").resizable({ minWidth: $("#'.$as_id.'").width(), minHeight: 50 });');

		} else {									// new ckeditor implementation 2010

			//include_once 'lib/tiki_ckeditor.php';
			if (!isset($params['name'])) { $params['name'] = 'edit'; }
			//$cked = new TikiCK($params['name']);
		
			$headerlib->add_jsfile('lib/ckeditor/ckeditor_source.js');
			$headerlib->add_jsfile('lib/ckeditor/adapters/jquery.js');
		
//			if ($prefs['feature_ajax'] == 'y' && $prefs['feature_ajax_autosave'] == 'y') {
//				$cked->Config['autoSaveSelf'] = $auto_save_referrer;		// this doesn't need to be the 'self' URI - just a unique reference for each page set up in ensureReferrer();
//				$cked->Config['autoSaveEditorId'] = $as_id;
//			}
//			if (isset($params['ToolbarSet'])) {
//				$cked->ToolbarSet = $params['ToolbarSet'];
//			} else {
//				$cked->ToolbarSet = 'Tiki';
//			}
//			$headerlib->add_jq_onready(<<< JS
//JS
//);
			include_once( $smarty->_get_plugin_filepath('function', 'toolbars') );
			$cktools = smarty_function_toolbars($params, $smarty);
			$cktools = json_encode($cktools);
			$cktools = substr($cktools, 1, strlen($cktools) - 2);	// remove surrouding [ & ]
			$cktools = str_replace(']],[[', '],"/",[', $cktools);	// add new row chars - done here so as not to break existing fck
						
//			if ($prefs['feature_detect_language'] == 'y') {
//				$cked->Config['AutoDetectLanguage'] = true;
//			} else {
//				$cked->Config['AutoDetectLanguage'] = false;
//			}
			//$cked->Config['DefaultLanguage'] = $prefs['language'];
			//$cked->Config['CustomConfigurationsPath'] = $url_path.'setup_ckeditor.php'.(isset($params['_section']) ? '?section='.urlencode($params['_section']) : '');
			//$html .= $cked->CreateHtml();
			
			$html .= '<input type="hidden" name="wysiwyg" value="y" />';
			global $tikiroot;
			$headerlib->add_jq_onready('
CKEDITOR.config._TikiRoot = "'.$tikiroot.'";
');	// before all
		
		if ($prefs['wysiwyg_htmltowiki'] === 'y') {
			$headerlib->add_jq_onready('
CKEDITOR.config.extraPlugins += (CKEDITOR.config.extraPlugins ? ",tikiwiki" : "tikiwiki" );
CKEDITOR.plugins.addExternal( "tikiwiki", "'.$tikiroot.'lib/ckeditor_tiki/plugins/tikiwiki/");
', 5);	// before dialog tools init (10)
		}
			$headerlib->add_jq_onready('
$( "#'.$as_id.'" ).ckeditor(CKeditor_OnComplete, {
	toolbar_Tiki: '.$cktools.',
	toolbar: "Tiki",
	language: "'.$prefs['language'].'",
	customConfig : ""
});
', 20);	// after dialog tools init (10)

			$html .= '<textarea class="wikiedit" name="'.$params['name'].'" id="'.$as_id.'" style="visibility:hidden; width: '.$params['width'].'; height: '.$params['height'].';">'.htmlspecialchars($content).'</textarea>';
			
			$headerlib->add_js('
var fckEditorInstances = new Array();
function CKeditor_OnComplete() {
	fckEditorInstances[fckEditorInstances.length] = this;
	this.resetDirty();
};');
			
		}	// end both wysiwyg setups

	} else {
		
		// setup for wiki editor
		
		$textarea_attributes = '';
		foreach ( $params as $k => $v ) {
			if ( $k == 'id' || $k == 'name' || $k == 'class' ) {
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
		window.status = '".addslashes(tra('Your edit session has expired'))."';
	} else {
		window.status = '".addslashes(tra('Your edit session will expire in:'))."' + Math.floor(seconds / 60) + ': ' + ((seconds % 60 < 10) ? '0' : '') + (seconds % 60);
	}
	if (seconds % 60 == 0 && \$('#edittimeout')) {
		\$('#edittimeout').text(Math.floor(seconds / 60));
	}
}

\$('document').ready( function() {
	editTimeoutIntervalId = setInterval(editTimerTick, 1000);
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
