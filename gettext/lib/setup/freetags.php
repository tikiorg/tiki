<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

if ( isset($section) and isset($sections[$section])) {
	include_once ('lib/freetag/freetaglib.php');
	$here = $sections[$section];
	if ( $tiki_p_freetags_tag == 'y' && isset($_POST['addtags']) && trim($_POST['addtags']) != '' ) {

		if ( ! isset($user) ) $userid = 0;
		else $userid = $userlib->get_user_id($user);

		if (empty($user) && $prefs['feature_antibot'] == 'y' && !$captchalib->validate()) {
			$smarty->assign('freetag_error', $captchalib->getErrors());
			$smarty->assign_by_ref('freetag_msg', $_POST['addtags']);
		} elseif ( $object = current_object() ) {
			$freetaglib->tag_object($userid, $object['object'], $object['type'], $_POST['addtags']);
		}
	}
	if (($tiki_p_admin == 'y' || $tiki_p_unassign_freetags == 'y') && isset($_REQUEST['delTag'])) {
		if ( $object = current_object() ) {
			$freetaglib->delete_object_tag($object['object'], $object['type'], $_REQUEST['delTag']);
		}
		$url = $tikilib->httpPrefix().preg_replace('/[?&]delTag='.preg_quote(urlencode($_REQUEST['delTag']), '/') . '/', '', $_SERVER['REQUEST_URI']);
		header("Location: $url");
		die;
	}
	if ( $object = current_object() ) {
		$tags = $freetaglib->get_tags_on_object($object['object'], $object['type']);
	} else {
		$tags = array();
	}
	$smarty->assign('freetags',$tags);
	$headerlib->add_cssfile('css/freetags.css');

	if( $tiki_p_freetags_tag == 'y' && $prefs['freetags_multilingual'] == 'y' ) {
		$ft_lang = null;
		$ft_multi = false;
		if (!empty($tags['data'])) {
			foreach( $tags['data'] as $row ) {
				$l = $row['lang'];
	
				if( ! $l )
					continue;
	
				if( ! $ft_lang )
					$ft_lang = $l;
				elseif( $ft_lang != $l ) {
					$ft_multi = true;
					break;
				}
			}
		}

		if( $ft_multi && $object = current_object() ) {
			$smarty->assign( 'freetags_mixed_lang', "tiki-freetag_translate.php?objType=" . urlencode($object['type']) . '&objId=' . urlencode($object['object']) );
		}
	}
}
