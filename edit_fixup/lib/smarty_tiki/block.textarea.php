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
 *    _enlarge: if set to 'y', display the enlarge buttons above the textarea
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

	$params['_wysiwyg'] = isset($params['_wysiwyg']) ? $params['_wysiwyg'] : 'n';
	$params['rows'] = isset($params['rows']) ? $params['rows'] : 20;
	$params['cols'] = isset($params['cols']) ? $params['cols'] : 80;
	$params['name'] = isset($params['name']) ? $params['name'] : 'edit';
	$params['id'] = isset($params['id']) ? $params['id'] : 'editwiki';
	
	if ( isset($params['_zoom']) && $params['_zoom'] == 'n' ) {
		$feature_template_zoom_orig = $prefs['feature_template_zoom'];
		$prefs['feature_template_zoom'] = 'n';
	}
	if ( ! isset($params['_section']) ) {
		global $section;
		$params['_section'] = $section ? $section: 'wiki';
	}
	if ( ! isset($params['style']) ) $params['style'] = 'width:99%';
	$html = '';

	if ( $params['_wysiwyg'] == 'y' ) {
// TODO
//		{editform Meat=$pagedata InstanceName='edit' ToolbarSet="Tiki"}
//		<input type="hidden" name="wysiwyg" value="y" />
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

		if ( $textarea_attributes != '' ) {
			$smarty->assign('textarea_attributes', $textarea_attributes);
		}
		$smarty->assign_by_ref('pagedata', $content);

		$html .= $smarty->fetch('wiki_edit.tpl');

		$html .= "\n".'<input type="hidden" name="rows" value="'.$params['rows'].'"/>'
			."\n".'<input type="hidden" name="cols" value="'.$params['cols'].'"/>'
			."\n".'<input type="hidden" name="wysiwyg" value="'.$params['_wysiwyg'].'" />';


		if ( isset($params['_zoom']) && $params['_zoom'] == 'n' ) {
			$prefs['feature_template_zoom'] = $feature_template_zoom_orig;
		}
		
		if ($prefs['feature_ajax'] == 'y' && $prefs['feature_ajax_autosave'] == 'y') {
			$headerlib->add_js("register_id('$textarea_id');auto_save();");
		}
		
	}	// wiki
	
// Display edit time out

	$js = "
// edit timeout warnings
function editTimerTick() {
	editTimeElapsedSoFar++;
	
	var seconds = editTimeoutSeconds - editTimeElapsedSoFar;
	
	if (editTimerWarnings == 0 && seconds <= 60) {
		alert('".tra('Your edit session will expire in').' 1 '.tra('minute').'.'.
				tra('You must PREVIEW or SAVE your work now, to avoid losing your edits.')."');
		editTimerWarnings++;
	} else if (seconds <= 0) {
		clearInterval(editTimeoutIntervalId);
	}
	
	window.status = '".tra('Your edit session will expire in:')."' + Math.floor(seconds / 60) + ': ' + ((seconds % 60 < 10) ? '0' : '') + (seconds % 60);
	if (seconds % 60 == 0 && \$jq('#edittimeout')) {
		\$jq('#edittimeout').text(Math.floor(seconds / 60));
	}
}

function confirmExit() {
	if (needToConfirm) {
		return '".tra('You are about to leave this page. If you have made any changes without Saving, your changes will be lost.  Are you sure you want to exit this page?')."';
	}
}

window.onbeforeunload = confirmExit;
\$jq('document').ready( function() { editTimeoutIntervalId = setInterval(editTimerTick, 1000); });
//window.onload = editTimerStart;

var needToConfirm = true;
var editTimeoutSeconds = ".ini_get('session.gc_maxlifetime').";
var editTimeElapsedSoFar = 0;
var editTimeoutIntervalId;
var editTimerWarnings = 0;
// end edit timeout warnings
";
	$headerlib->add_js($js);

	return $html;
}
