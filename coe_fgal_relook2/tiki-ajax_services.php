<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// To contain data services for ajax calls (autocomplete calls sa far)

require_once ('tiki-setup.php');

$access->check_feature( array( 'feature_ajax', 'feature_jquery_autocomplete' ) );

if ($access->is_serializable_request() && isset($_REQUEST['listonly'])) {
	$sep = '|';
	if( isset( $_REQUEST['separator'] ) ) {
		$sep = $_REQUEST['separator'];
	}
	$p = strrpos($_REQUEST['q'], $sep);
	if ($p !== false) {
		$_REQUEST['q'] = substr($_REQUEST['q'], $p + 1);
	}

	if ($_REQUEST['listonly'] == 'groups') {
		$listgroups = $userlib->get_groups(0, -1, 'groupName_asc', '', '', 'n');
		
		// TODO proper perms checking - this looks right but returns nothing for reg, and everything for admin
		// $listgroups['data'] = Perms::filter( array( 'type' => 'group' ), 'object', $listgroups['data'], array( 'object' => 'groupName' ), 'view_group' );
		
		$grs = array();
		foreach($listgroups['data'] as $gr) {
			if (isset($_REQUEST['q']) && stripos($gr['groupName'], $_REQUEST['q']) !== false) {
				$grs[] = $gr['groupName'];
			}
		}
		$access->output_serialized($grs);
	} elseif ($_REQUEST['listonly'] == 'users') {
		$listusers = $userlib->get_users_names();
		
		// TODO also - proper perms checking
		// tricker for users? Check the group they're in, then tiki_p_group_view_members
		
		$usrs = array();
		foreach($listusers as $usr) {
			if (isset($_REQUEST['q']) && stripos($usr, $_REQUEST['q']) !== false) {
				$usrs[] = $usr;
			}
		}
		$access->output_serialized($usrs);
	} elseif ($_REQUEST['listonly'] == 'userrealnames') {
		$groups = '';
		$listusers = $userlib->get_users_light(0, -1, 'login_asc', '', $groups);
		$done = array();
		$finalusers = array();
		foreach($listusers as $usrId => $usr) {
			if (isset($_REQUEST['q'])) {
				$longusr = $usr . ' (' . $usrId . ')';
				if (array_key_exists($usr, $done)) {
					// disambiguate duplicates
					if (stripos($longusr, $_REQUEST['q']) !== false) {
						$oldkey = array_search($usr, $finalusers);
						if ($oldkey !== false) {
							$finalusers[$oldkey] = $done[$usr];
						}
					}
					if (stripos($longusr, $_REQUEST['q']) !== false) {
						$finalusers[] = $longusr;
					}
				} else {
					if (stripos($longusr, $_REQUEST['q']) !== false) {
						$finalusers[] = $usr;
					}
				}
				$done[$usr] = $longusr;
			}
		}
		
		// TODO also - proper perms checking
		// tricker for users? Check the group they're in, then tiki_p_group_view_members
				
		$access->output_serialized($finalusers);
	} elseif( $_REQUEST['listonly'] == 'tags' ) {
		global $freetaglib; require_once 'lib/freetag/freetaglib.php';

		$tags = $freetaglib->get_tags_containing( $_REQUEST['q'] );
		$access->output_serialized( $tags );
	} elseif( $_REQUEST['listonly'] == 'icons' ) {

		$dir = 'pics/icons';
		$max = isset($_REQUEST['max']) ? $_REQUEST['max'] : 10;
		$icons = array();
		$style_dir = $tikilib->get_style_path($prefs['style'], $prefs['style_option']);
		if ($style_dir && is_dir($style_dir . $dir)) {
			read_icon_dir($style_dir . $dir, $icons, $max);
		}
		read_icon_dir($dir, $icons, $max);
		$access->output_serialized($icons);
	} elseif( $_REQUEST['listonly'] == 'shipping' && $prefs['shipping_service'] == 'y' ) {
		global $shippinglib; require_once 'lib/shipping/shippinglib.php';

		$access->output_serialized( $shippinglib->getRates( $_REQUEST['from'], $_REQUEST['to'], $_REQUEST['packages'] ) );
	} elseif(  $_REQUEST['listonly'] == 'trackername' ) {
		$trackers = $tikilib->list_trackers();
		$ret = array();
		foreach ($trackers['data'] as $tracker) {
			$ret[] = $tracker['name'];
		}
		$access->output_serialized($ret);
	}
}

function read_icon_dir($dir, &$icons, $max) {
	$fp = opendir($dir);
	while(false !== ($f = readdir($fp))) {
		preg_match('/^([^\.].*)\..*$/', $f, $m);
		if (count($m) > 0 && count($icons) < $max &&
				stripos($m[1], $_REQUEST['q']) !== false &&
				!in_array($dir . '/' . $f, $icons)) {
			
			$icons[] = $dir . '/' . $f;
		}
	}
}
