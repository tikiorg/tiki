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
 *    _quicktags: if set to 'y', display quicktags above the textarea
 *    _wikiparsed: y|n|optional_on|optional_off. If set to 'optional_on' or 'optional_off', a checkbox will be added to enable/disable wiki parsing and to show/hide quicktags 
 *
 * usage: {textarea id='my_area' name='my_area'}{tr}My Text{/tr}{/textarea}
 *
 */

function smarty_block_textarea($params, $content, &$smarty, $repeat) {
	global $prefs;
	if ( $repeat || $content == '' ) return;

	if ( ! isset($params['_quicktags']) ) $params['quicktags'] = 'n';
	if ( ! isset($params['_wikiparsed']) ) {
		// Quicktags implies wiki parsing
		$params['_wikiparsed'] = $params['quicktags'];
	}
	if ( ! isset($params['_wysiwyg']) ) $params['_wysiwyg'] = 'n';
	if ( ! isset($params['_section']) ) {
		global $section;
		$params['_section'] = $section;
	}
	if ( ! isset($params['style']) ) $params['style'] = 'width:99%';
	$html = '';

	if ( $params['_wysiwyg'] == 'y' ) {
// TODO
//		{editform Meat=$pagedata InstanceName='edit' ToolbarSet="Tiki"}
//		<input type="hidden" name="wysiwyg" value="y" />
	} else {
		if ( $params['_wikiparsed'] == 'optional_on' || $params['_wikiparsed'] == 'optional_off' ) {
			$html .= tra('Allow wiki syntax:')
				.'<input type="checkbox" name="'.$params['name'].'IsParsed"'
				.( $params['_wikiparsed'] == 'optional_on' ? ' checked="checked"' : '' )
				.' onclick="toggleBlock(\'qt'.$params['name'].'\');" /><br />';
		}
		if ( $params['_quicktags'] == 'y' ) {
			include_once ('lib/quicktags/quicktagslib.php');
			$quicktags = $quicktagslib->list_quicktags(0, -1, 'taglabel_desc', '', $params['_section']);
			$smarty->assign_by_ref('quicktags', $quicktags["data"]);
		} else {
			$smarty->clear_assign('quicktags');
		}

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
		$smarty->assign('pagedata', $content);

		$html .= $smarty->fetch('wiki_edit.tpl');

		$html .= "\n".'<input type="hidden" name="rows" value="'.$params['rows'].'"/>'
			."\n".'<input type="hidden" name="cols" value="'.$params['cols'].'"/>'
			."\n".'<input type="hidden" name="wysiwyg" value="'.$params['_wysiwyg'].'" />';
	}

	return $html;
}
