<?php

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
 *    previewConfirmExit: if set to 'n' doesn't warn about lost edits after preview
 *    simple: if set to 'y' does no wysiwyg, auto_save, lost edit warning etc
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
	$params['previewConfirmExit'] = isset($params['previewConfirmExit']) ? $params['previewConfirmExit'] : 'y';
	
	$params['simple'] = isset($params['simple']) ? $params['simple'] : 'n';
	
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
	if ($params['_wysiwyg'] != 'y') {
		$as_id = $params['id'];
	} else {
		$as_id = $params['name'];
	}
	if ($prefs['feature_ajax'] == 'y' && $prefs['feature_ajax_autosave'] == 'y' && $params['simple'] == 'n') {	// retrieve autosaved content
		$auto_save_referrer = ensureReferrer();

		if (empty($_REQUEST['noautosave']) || $_REQUEST['noautosave'] != 'y') {
			if (has_autosave($as_id, $auto_save_referrer)) {		//  and $params['preview'] == 0 -  why not?
				$auto_saved = str_replace("\n","\r\n", get_autosave($as_id, $auto_save_referrer));
				
				if ( strcmp($auto_saved, $content) != 0 ) {
					$content = $auto_saved;
					include_once('lib/smarty_tiki/block.self_link.php');
					include_once('lib/smarty_tiki/block.remarksbox.php');
					$msg = tra('If you want the saved version instead of this autosaved one').'&nbsp;'.smarty_block_self_link( array( 'noautosave'=>'y', '_ajax'=>'n'), tra('Click Here'), $smarty);
					$auto_save_warning = smarty_block_remarksbox( array( 'type'=>'warning', 'title'=>tra('AutoSave')), $msg, $smarty)."\n";
				}
			}
		}
	}



	if ( $params['_wysiwyg'] == 'y' && $params['simple'] == 'n') {

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
		$fcked->Config['CustomConfigurationsPath'] = $url_path.'setup_fckeditor.php'.(isset($params['_section']) ? '?section='.urlencode($params['_section']) : '');
		$html .= $fcked->CreateHtml();
		
		$html .= '<input type="hidden" name="wysiwyg" value="y" />';
		
		// fix for Safari which refuses to make the edit box 100% height
		$h = str_replace('px','', $fcked->Height);
		if ($h) { $headerlib->add_js('
var fckEditorInstances = new Array();
function FCKeditor_OnComplete( editorInstance ) {
	fckEditorInstances[fckEditorInstances.length] = editorInstance;
	editorInstance.ResetIsDirty();
};'); }


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
		
		if ($prefs['feature_ajax'] == 'y' && $prefs['feature_ajax_autosave'] == 'y' && $params['simple'] == 'n') {
			$headerlib->add_jq_onready("register_id('$textarea_id'); auto_save();");
			$headerlib->add_js("var autoSaveId = '$auto_save_referrer';");	// onready is too late...
		}

		$smarty->assign_by_ref('pagedata', htmlspecialchars($content));
		$html .= $smarty->fetch('wiki_edit.tpl');

		$html .= "\n".'<input type="hidden" name="rows" value="'.$params['rows'].'"/>'
			."\n".'<input type="hidden" name="cols" value="'.$params['cols'].'"/>'
			."\n".'<input type="hidden" name="wysiwyg" value="n" />';

	}	// wiki or wysiwyg


	if ($params['simple'] == 'n') {
// Display edit time out

		$js = "
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
	if (seconds % 60 == 0 && \$jq('#edittimeout')) {
		\$jq('#edittimeout').text(Math.floor(seconds / 60));
	}
}

function confirmExit() {
	if (window.needToConfirm && typeof fckEditorInstances != 'undefined' && fckEditorInstances.length > 0) {
		for(ed in fckEditorInstances) {
			if (fckEditorInstances[ed].IsDirty()) {
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
\$jq('document').ready( function() {
	editTimeoutIntervalId = setInterval(editTimerTick, 1000);
	// attach dirty function to all relevant inputs etc
	if ('$as_id' != 'editwiki') {	// modules admin exception
		\$jq('#$as_id').change( function () { if (!editorDirty) { editorDirty = true; } });
	} else {
		\$jq(\$jq('#$as_id').attr('form')).find('input, textarea, select').change( function () { if (!editorDirty) { editorDirty = true; } });
	}
});

window.needToConfirm = true;
window.editorDirty = ".(isset($_REQUEST["preview"]) && $params['previewConfirmExit'] == 'y' ? 'true' : 'false').";
var editTimeoutSeconds = ".ini_get('session.gc_maxlifetime').";
var editTimeElapsedSoFar = 0;
var editTimeoutIntervalId;
var editTimerWarnings = 0;
// end edit timeout warnings
";
		if ($prefs['feature_wysiwyg'] && $prefs['wysiwyg_optional']) {
			$js .= '
function switchEditor(mode, form) {
	window.needToConfirm=false;
	var w;
	if (mode=="wysiwyg") {
		$jq(form).find("input[name=mode_wysiwyg]").val("y");
		$jq(form).find("input[name=wysiwyg]").val("y");
	} else {
		$jq(form).find("input[name=mode_normal]").val("y");
		$jq(form).find("input[name=wysiwyg]").val("n");
	}
	form.submit();
}';
		}
	
		$headerlib->add_js($js);
	}	// end if ($params['simple'] == 'n')

	return $auto_save_warning.$html;
}
