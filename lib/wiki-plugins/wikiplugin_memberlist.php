<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_memberlist_info() {
	return array(
		'name' => tra('Member List'),
		'documentation' => 'PluginMemberList',
		'description' => tra('List and allow editing of group members'),
		'prefs' => array( 'wikiplugin_memberlist' ),
		'filter' => 'wikicontent',
		'icon' => 'pics/icons/group_gear.png',
		'params' => array(
			'groups' => array(
				'required' => true,
				'name' => tra('Groups'),
				'description' => tra('List of groups to handle through the interface. Colon separated.'),
				'separator' => ':',
				'filter' => 'groupname',
				'default' => '',
			),
		),
	);
}

function wikiplugin_memberlist( $data, $params ) {
	global $prefs;
	static $execution = 0;
	$key = 'memberlist-execution-' . ++ $execution;

	if( ! isset( $params['groups'] ) ) {
		return "^Missing group list^";
	}

	$groups = $params['groups'];

	Perms::bulk( array( 'type' => 'group' ), 'object', $groups );

	$validGroups = wikiplugin_memberlist_get_group_details( $groups );

	if( isset($_POST[$key]) ) {
		if( isset( $_POST['join'] ) ) {
			wikiplugin_memberlist_join( $validGroups, $_POST['join'] );
		}
		if( isset( $_POST['leave'] ) ) {
			wikiplugin_memberlist_leave( $validGroups, $_POST['leave'] );
		}
		if( isset( $_POST['remove'] ) ) {
			wikiplugin_memberlist_remove( $validGroups, $_POST['remove'] );
		}
		if( isset( $_POST['add'] ) ) {
			wikiplugin_memberlist_add( $validGroups, $_POST['add'] );
		}
		header( 'Location: ' . $_SERVER['REQUEST_URI'] );
		exit;
	}

	if( isset( $_REQUEST['transition'], $_REQUEST['member'] ) ) {
		if( $prefs['feature_group_transition'] == 'y' ) {
			require_once 'lib/transitionlib.php';
			$transitionlib = new TransitionLib( 'group' );
			$transitionlib->triggerTransition( $_REQUEST['transition'], $_REQUEST['member'] );

			$url = $_SERVER['REQUEST_URI'];
			$url = str_replace( 'transition=', 'x=', $url );
			$url = str_replace( 'member=', 'x=', $url );
			header( 'Location: ' . $url );
			exit;
		}
	}

	$canApply = false;
	foreach( $validGroups as $group ) {
		if( $group['can_add'] || $group['can_remove'] || $group['can_join'] || $group['can_leave'] ) {
			$canApply = true;
			break;
		}
	}

	global $smarty;
	$smarty->assign( 'execution_key', $key );
	$smarty->assign( 'can_apply', $canApply );
	$smarty->assign( 'memberlist_groups', $validGroups );
	return '~np~' . $smarty->fetch( 'wiki-plugins/wikiplugin_memberlist.tpl' ) . '~/np~';
}

function wikiplugin_memberlist_get_members( $groupName ) {
	global $userlib;

	$raw = $userlib->get_users( 0, -1, 'login_asc', '', '', false, $groupName );
	$users = array();

	foreach( $raw['data'] as $user ) {
		$users[] = $user['login'];
	}

	return $users;
}

function wikiplugin_memberlist_get_group_details( $groups ) {
	global $user, $prefs, $userlib;
	$validGroups = array();
	foreach( $groups as $groupName ) {
		if( ! $userlib->group_exists( $groupName ) ) {
			continue;
		}
		
		$perms = Perms::get( array( 'type' => 'group', 'object' => $groupName ) );

		if( $perms->group_view ) {
			$isMember = in_array( $groupName, $perms->getGroups() );

			$validGroups[$groupName] = array(
				'can_join' => $perms->group_join && ! $isMember && $user,
				'can_leave' => $perms->group_join && $isMember && $user,
				'can_add' => $perms->group_add_member,
				'can_remove' => $perms->group_remove_member,
				'is_member' => $isMember,
			);

			if( $perms->group_view_members ) {
				$validGroups[$groupName]['members'] = wikiplugin_memberlist_get_members( $groupName );

				if( $prefs['feature_group_transition'] == 'y' ) {
					require_once 'lib/transitionlib.php';
					$transitionlib = new TransitionLib( 'group' );
					$validGroups[$groupName]['transitions'] = array();
					foreach( $validGroups[$groupName]['members'] as $username ) {
						$validGroups[$groupName]['transitions'][$username] = $transitionlib->getAvailableTransitionsFromState( $groupName, $username );
					}
				}
			}
		}
	}

	return $validGroups;
}

function wikiplugin_memberlist_join( $groups, $joins ) {
	global $user, $userlib;
	foreach( $joins as $group ) {
		if( isset( $groups[$group] ) ) {
			if( $groups[$group]['can_join'] ) {
				$userlib->assign_user_to_group( $user, $group );
			}
		}
	}
}

function wikiplugin_memberlist_leave( $groups, $leaves ) {
	global $user, $userlib;
	foreach( $leaves as $group ) {
		if( isset( $groups[$group] ) ) {
			if( $groups[$group]['can_leave'] ) {
				$userlib->remove_user_from_group( $user, $group );
			}
		}
	}
}

function wikiplugin_memberlist_add( $groups, $adds ) {
	global $userlib;

	foreach( $adds as $group => $members ) {
		if( isset( $groups[$group] ) ) {
			if( $groups[$group]['can_add'] ) {
				$members = explode( ',', $members );
				$members = array_map( 'trim', $members );
				$members = array_filter( $members );

				foreach( $members as $name ) {
					if( $userlib->user_exists( $name ) ) {
						$userlib->assign_user_to_group( $name, $group );
					}
				}
			}
		}
	}
}

function wikiplugin_memberlist_remove( $groups, $removes ) {
	global $userlib;

	foreach( $removes as $group=> $members ) {
		if( isset( $groups[$group] ) ) {
			if( $groups[$group]['can_remove'] ) {
				foreach( $members as $name ) {
					$userlib->remove_user_from_group( $name, $group );
				}
			}
		}
	}
}
