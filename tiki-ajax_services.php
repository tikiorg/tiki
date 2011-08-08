<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// To contain data services for ajax calls
//
// If controller and action are specified in the request, the controller class matching the
// controller key in the $contollerMap registry will be instanciated. The method matching the
// action name will be called. The input to the method is a JitFilter. The output of the method
// will be serialized and sent to the browser.
//
// Otherwise, the procedural script remains

$controllerMap = array(
	'comment' => 'Services_Comment_Controller',
	'file' => 'Services_File_Controller',
	'auth_source' => 'Services_AuthSource_Controller',
	'tracker' => 'Services_Tracker_Controller',
	'tracker_sync' => 'Services_Tracker_SyncController',
	'favorite' => 'Services_Favorite_Controller',
	'translation' => 'Services_Language_TranslationController',
	'user' => 'Services_User_Controller',
	'category' => 'Services_Category_Controller',
);

$inputConfiguration = array(array(
	'staticKeyFilters' => array(
		'action' => 'word',
		'controller' => 'word',
	),
));

if (isset($_REQUEST['controller'], $_REQUEST['action'])) {
	$inputConfiguration[] = array('catchAllUnset' => null);
}

require_once ('tiki-setup.php');

if (isset($_REQUEST['controller'], $_REQUEST['action'])) {
	$controller = $_REQUEST['controller'];
	$action = $_REQUEST['action'];

	$broker = new Services_Broker($controllerMap);
	$broker->process($controller, $action, $jitRequest);
	exit;
}

if ($access->is_serializable_request() && isset($_REQUEST['listonly'])) {
	$access->check_feature( array( 'feature_ajax', 'feature_jquery_autocomplete' ) );

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
	} elseif ($_REQUEST['listonly'] == 'usersandcontacts') {
		$contactlib = TikiLib::lib('contact');
		$listcontact = $contactlib->list_contacts($user);
		$listusers = $userlib->get_users();
		
		$contacts = array();		
		$query = $_REQUEST['q'];
				
		foreach($listcontact as $key=>$contact) {
			if (isset($query) && (stripos($contact['firstName'], $query) !== false or stripos($contact['lastName'], $query) !== false or stripos($contact['email'], $query) !== false)) {
				if($contact['email']<>''){ $contacts[] = $contact['email']; }
			}
		}
		foreach($listusers['data'] as $key=>$contact) {
			if (isset($query) && (stripos($contact['firstName'], $query) !== false or stripos($contact['login'], $query) !== false or stripos($contact['lastName'], $query) !== false or stripos($contact['email'], $query) !== false)) {
				if($prefs['login_is_email'] == 'y'){
					$contacts[] = $contact['login'];
				} else {
					$contacts[] = $contact['email'];
				}
			}
		}
		$contacts = array_unique($contacts);
		sort($contacts);
		$access->output_serialized($contacts);
		
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
		$trackers = TikiLib::lib('trk')->list_trackers();
		$ret = array();
		foreach ($trackers['data'] as $tracker) {
			$ret[] = $tracker['name'];
		}
		$access->output_serialized($ret);
	}
}

// Handle Zotero Requests
if ($access->is_serializable_request() && isset($_REQUEST['zotero_tags'])) {
	$access->check_feature( array( 'zotero_enabled' ) );
	$zoterolib = TikiLib::lib('zotero');

	$references = $zoterolib->get_references($_REQUEST['zotero_tags']);
	
	if ($references === false) {
		$access->output_serialized(array(
			'type' => 'unauthorized',
			'results' => array(),
		));
	} else {
		$access->output_serialized(array(
			'type' => 'success',
			'results' => $references,
		));
	}
}

if (isset($_REQUEST['oauth_request'])) {
	$oauthlib = TikiLib::lib('oauth');

	$oauthlib->request_token($_REQUEST['oauth_request']);
	die('Provider not supported.');
}

if (isset($_REQUEST['oauth_callback'])) {
	$oauthlib = TikiLib::lib('oauth');

	$oauthlib->request_access($_REQUEST['oauth_callback']);
	$access->redirect('');
}

if (isset($_REQUEST['geocode']) && $access->is_serializable_request()) {
	$access->output_serialized(TikiLib::lib('geo')->geocode($_REQUEST['geocode']));
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
