<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// Called by FCKEditor and defined in setup_fckeditor.tpl - FCKConfig.ajaxAutoSaveTargetUrl
// also now CKEDITOR.config.ajaxAutoSaveTargetUrl defined in block.textarea.php

// Used by ckeditor tikiwiki plugin and to reparse plugins in html mode
// possibly should be renamed?

$inputConfiguration = array( array(
	'staticKeyFilters' => array(
		'editor_id' => 'alpha',
//		'data' => 'alpha',
//		'script' => 'alpha',
	),
) );

require_once('tiki-setup.php');

if ($prefs['feature_ajax'] != 'y' || ($prefs['ajax_autosave'] != 'y' && $prefs['wysiwyg_ckeditor'] != 'y')) {
	return;
}

require_once('lib/ajax/autosave.php');

function send_ajax_response($command, $data ) {
	header( 'Content-Type:text/xml; charset=UTF-8' );	// TODO refactor
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo '<adapter command="' . $command . '">';
	echo '<data><![CDATA[' .  $data . ']]></data>';
	echo '</adapter>';
	exit;
}

if (isset($_REQUEST['editor_id'])) {
	if (isset($_REQUEST['command']) && isset($_REQUEST['data']) && $_REQUEST['data'] != 'ajax error') {
		$_REQUEST['referer'] = urldecode($_REQUEST['referer']);
		if ($_REQUEST['command'] == 'toWikiFormat') {
			global $editlib; include_once 'lib/wiki/editlib.php';
			$res = $editlib->parseToWiki(urldecode($_REQUEST['data']));
		} else if ($_REQUEST['command'] == 'toHtmlFormat') {
			global $editlib; include_once 'lib/wiki/editlib.php';
			$res = $editlib->parseToWysiwyg(urldecode($_REQUEST['data']));
		} else if ($_REQUEST['command'] == 'auto_save') {
			include_once 'lib/ajax/autosave.php';
			$res = auto_save( $_REQUEST['editor_id'], $_REQUEST['data'], $_REQUEST['referer'] );
		} else if ($_REQUEST['command'] == 'auto_remove') {
			include_once 'lib/ajax/autosave.php';
			remove_save($_REQUEST['editor_id'], $_REQUEST['referer'] );
		} else if ($_REQUEST['command'] == 'auto_get') {
			include_once 'lib/ajax/autosave.php';
			$res = get_autosave($_REQUEST['editor_id'], $_REQUEST['referer'] );
		}
		send_ajax_response( $_REQUEST['command'], $res );
	} else if (isset($_REQUEST['data']) && $_REQUEST['data'] != 'ajax error') {	// autosave

		$res = auto_save($_REQUEST['editor_id'],$_REQUEST['data'],$_REQUEST['script']);
		send_ajax_response( 'draft', $res );

	} else if (isset($_REQUEST['autoSaveId'])) {	// wiki page previews
		
		$_REQUEST['autoSaveId'] = urldecode($_REQUEST['autoSaveId']);
		$autoSaveIdParts = explode(':', $_REQUEST['autoSaveId']);	// user, section, object id
		
		if (count($autoSaveIdParts) === 3 && !empty($user) && $user === $autoSaveIdParts[0] && $autoSaveIdParts[1] === 'wiki_page') {
			
			$editlib; include_once 'lib/wiki/editlib.php';
			if (isset($_REQUEST['inPage'])) {
				if (!isset($_REQUEST['diff_style'])) {	// use previously set diff_style
					$_REQUEST['diff_style'] = isset($_COOKIE['preview_diff_style']) ? $_COOKIE['preview_diff_style'] : '';
				}
				$data = $editlib->partialParseWysiwygToWiki(get_autosave($_REQUEST['editor_id'], $_REQUEST['autoSaveId']));
				$smarty->assign( 'diff_style', $_REQUEST['diff_style'] );
				if (!empty($_REQUEST['diff_style'])) {
					global $tikilib;
					$info = $tikilib->get_page_info($autoSaveIdParts[2]);
					if (!empty($info)) {
						require_once('lib/diff/difflib.php');
						$data = diff2( $info['data'], html_entity_decode($data, ENT_COMPAT, 'UTF-8'), $_REQUEST["diff_style"]);
						$smarty->assign_by_ref('diffdata', $data);
						
						//$smarty->assign( 'diff_oldver', (int) $info['version'] );
						//$smarty->assign( 'diff_newver', (int) $info['version'] );
						$smarty->assign( 'translation_mode', 'y' );
						$data = $smarty->fetch('pagehistory.tpl');
					}
				} else {
					$data = $tikilib->parse_data_raw($data);
				}
				echo $data;
				
			} else {					// popup window
				$headerlib->add_js('
function get_new_preview() {
$("body").css("opacity", 0.6);
location.replace("' . $tikiroot . 'tiki-auto_save.php?editor_id=' . $_REQUEST['editor_id'] . '&autoSaveId=' . $_REQUEST['autoSaveId'] . '");
}
$(window).load(function(){
	if (typeof opener != "undefined") {
		opener.ajaxPreviewWindow = this;
	}
}).unload(function(){
	if (typeof opener.ajaxPreviewWindow != "undefined") {
		opener.ajaxPreviewWindow = null;
	}
});
');
				$smarty->assign('headtitle', tra('Preview'));
				$data = '<div id="c1c2"><div id="wrapper"><div id="col1"><div id="tiki-center" class="wikitext">';
				if (has_autosave($_REQUEST['editor_id'], $_REQUEST['autoSaveId'])) {
					$data .= $tikilib->parse_data_raw($editlib->partialParseWysiwygToWiki(get_autosave($_REQUEST['editor_id'], $_REQUEST['autoSaveId'])));
				} else {
					if ($autoSaveIdParts[1] == 'wiki_page') {
						global $wikilib; include_once('lib/wiki/wikilib.php');
						$canBeRefreshed = false;
						$data .= $wikilib->get_parse($autoSaveIdParts[2], $canBeRefreshed);
					}
				}
				$data .= '</div></div></div></div>';
				$smarty->assign_by_ref( 'mid_data', $data);
				$smarty->assign( 'mid', '');
				$smarty->display("tiki_full.tpl");
			}
		}	// end wiki preview
	}
}
