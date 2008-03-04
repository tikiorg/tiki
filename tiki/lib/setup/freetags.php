<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/setup/freetags.php,v 1.2.2.4 2008-03-04 18:45:54 sylvieg Exp $
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
  header('location: index.php');
  exit;
}

if ( isset($section) and isset($sections[$section])) {
	include_once ('lib/freetag/freetaglib.php');
	$here = $sections[$section];
	if ( $tiki_p_freetags_tag == 'y' && isset($_POST['addtags']) && trim($_POST['addtags']) != '' ) {
		if ( ! isset($user) ) $userid = 0;
		else $userid = $userlib->get_user_id($user);

		if (isset($here['itemkey']) and isset($_REQUEST[$here['itemkey']])) {
			$freetaglib->tag_object($userid, $_REQUEST[$here['itemkey']], "$section ".$_REQUEST[$here['key']], $_POST['addtags']);
		} elseif (isset($here['key']) and isset($_REQUEST[$here['key']])) {
			$freetaglib->tag_object($userid, $_REQUEST[$here['key']], $section, $_POST['addtags']);
		}
	}
	if (($tiki_p_admin == 'y' || $tiki_p_unassign_freetags == 'y') && isset($_REQUEST['delTag'])) {
		if (isset($here['itemkey']) and isset($_REQUEST[$here['itemkey']])) {
			$freetaglib->delete_object_tag($_REQUEST[$here['itemkey']], $section, $_REQUEST['delTag']);
		} elseif (isset($here['key']) and isset($_REQUEST[$here['key']])) {
			$freetaglib->delete_object_tag($_REQUEST[$here['key']], $section, $_REQUEST['delTag']);
		}
		$url = $tikilib->httpPrefix().str_replace('&delTag='.urlencode($_REQUEST['delTag']), '', $_SERVER['REQUEST_URI']);
		header("Location: $url");
		die;
	}
	if (isset($here['itemkey']) and isset($_REQUEST[$here['itemkey']])) {
		$tags = $freetaglib->get_tags_on_object($_REQUEST[$here['itemkey']], "$section ".$_REQUEST[$here['key']]);
	} elseif (isset($here['key']) and isset($_REQUEST[$here['key']])) {
		$tags = $freetaglib->get_tags_on_object($_REQUEST[$here['key']], $section);
	} else {
		$tags = array();
	}
	$smarty->assign('freetags',$tags);
	$headerlib->add_cssfile('css/freetags.css');

	if( $tiki_p_freetags_tag == 'y' && $prefs['freetags_multilingual'] == 'y' ) {
		$ft_lang = null;
		$ft_multi = false;
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

		if( $ft_multi )
			$smarty->assign( 'freetags_mixed_lang', "tiki-freetag_translate.php?objId={$_REQUEST[$here['key']]}" );
	}
}
