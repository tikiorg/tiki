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

	if (!isset($params['_wysiwyg'])) {	// should not be set usually(?)
		include_once 'tiki-parsemode_setup.php';
		$params['_wysiwyg'] = $_SESSION['wysiwyg'];
	}
	
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
//		{editform Meat=$pagedata InstanceName='edit' ToolbarSet="Tiki"}
		global $url_path;
		include_once 'lib/tikifck.php';
		if (!isset($params['name']))       $params['name'] = 'fckedit';
		$fcked = new TikiFCK($params['name']);
		
		if (isset($content))			$fcked->Meat = $content;
		if (isset($params['Width']))	$fcked->Width = $params['Width'];
		if (isset($params['Height']))	$fcked->Height = $params['Height'];
		if ($prefs['feature_ajax'] == 'y' && $prefs['feature_ajax_autosave'] == 'y') {
			$fcked->Config['autoSaveSelf'] = htmlentities($_SERVER['REQUEST_URI']);
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
		$fcked->Config['CustomConfigurationsPath'] = $url_path.'setup_fckeditor.php';
		$html .= $fcked->CreateHtml();
		
		$html .= '<input type="hidden" name="wysiwyg" value="y" />';
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


/*** removed from tiki-editpage.tpl for safe keeping
 * TODO - move to PHP above...

				{*if $wysiwyg ne 'y' or $prefs.javascript_enabled ne 'y'*}
					{*include file='wiki_edit.tpl'*}
<!--					<input type="hidden" name="rows" value="{$rows}"/>-->
<!--					<input type="hidden" name="cols" value="{$cols}"/>-->
<!--					<input type="hidden" name="wysiwyg" value="n" />-->
					{*textarea _toolbars="y"}{$pagedata}{/textarea}
				{else}
					{capture name=autosave}
						{if $prefs.feature_ajax eq 'y' and $prefs.feature_ajax_autosave eq 'y' and $noautosave neq 'y'}
							{autosave test='n' id='edit' default=$pagedata preview=$preview}
						{else}
							{$pagedata}
						{/if}
					{/capture}
					{if $prefs.feature_ajax eq 'y' and $prefs.feature_ajax_autosave eq 'y' and $noautosave neq 'y' and $has_autosave eq 'y'}
						{remarksbox type="warning" title="{tr}AutoSave{/tr}"}
							{tr}If you want the saved version instead of the autosaved one{/tr}&nbsp;{self_link noautosave='y' _ajax='n'}{tr}Click Here{/tr}{/self_link}
						{/remarksbox}
					{/if*}
				</td>
			</tr>
			<tr>
				<td colspan="2">
					{editform Meat=$smarty.capture.autosave InstanceName='edit' ToolbarSet="Tiki"}
					<input type="hidden" name="wysiwyg" value="y" />
				{/if}
*/

