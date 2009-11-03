<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: /cvsroot/tikiwiki/tiki/tiki-setup_base.php,v 1.142.2.8 2008-03-22 05:12:47 mose Exp $
//this script may only be included - so its better to die if called directly.

if (!$cachelib->isCached("allperms")) {
	$allperms = $userlib->get_permissions(0, -1, 'permName_desc', '', '');
	$cachelib->cacheItem("allperms", serialize($allperms));
} else {
	$allperms = unserialize($cachelib->getCached("allperms"));
}
$permissionList = array();
$adminPermissions = array();

foreach( $allperms['data'] as $row ) {
	$valid = false;

	if( ! $row['feature_check'] ) {
		$valid = true;
	} else {
		foreach( explode( ',', $row['feature_check'] ) as $feature ) {
			if( $prefs[$feature] == 'y' ) {
				$valid = true;
				break;
			}
		}
	}

	if( $valid ) {
		$permissionList[] = $row['permName'];

		if( $row['admin'] == 'y' ) {
			$adminPermissions[ $row['type'] ] = substr( $row['permName'], strlen( 'tiki_p_' ) );
		}
	}
}

// Create a map from the permission to the admin permission
$map = array();
foreach( $allperms['data'] as $row ) {
	$type = $row['type'];
	if( isset( $adminPermissions[$type] ) && $row['admin'] != 'y' ) {
		$permName = substr( $row['permName'], strlen( 'tiki_p_' ) );
		$map[ $permName ] = $adminPermissions[$type];
	}
}

$groupList = $tikilib->get_user_groups( $user );

require_once 'lib/core/lib/Perms.php';
require_once 'lib/core/lib/Perms/Check/Direct.php';
require_once 'lib/core/lib/Perms/Check/Indirect.php';
require_once 'lib/core/lib/Perms/Check/Alternate.php';
require_once 'lib/core/lib/Perms/ResolverFactory/GlobalFactory.php';
require_once 'lib/core/lib/Perms/ResolverFactory/CategoryFactory.php';
require_once 'lib/core/lib/Perms/ResolverFactory/ObjectFactory.php';

$sequence = array(
	$globalAdminCheck = new Perms_Check_Alternate( 'admin' ),
	new Perms_Check_Direct,
	new Perms_Check_Indirect( $map ),
);

if( $user ) {
	require_once 'lib/core/lib/Perms/Check/Creator.php';
	$sequence[] = new Perms_Check_Creator( $user );
}

$factories = array();
$factories[] = new Perms_ResolverFactory_ObjectFactory;
if( $prefs['feature_categories'] == 'y' ) {
	$factories[] = new Perms_ResolverFactory_CategoryFactory;
}
$factories[] = new Perms_ResolverFactory_GlobalFactory;

$perms = new Perms;
$perms->setGroups( $groupList );
$perms->setPrefix( 'tiki_p_' );
$perms->setCheckSequence( $sequence );
$perms->setResolverFactories( $factories );
Perms::set( $perms );

$globalperms = Perms::get();
$globalAdminCheck->setResolver( $globalperms->getResolver() );

function remove_tiki_p_prefix( $name ) {
	return substr( $name, 7 );
}

$shortPermList = array_map( 'remove_tiki_p_prefix', $permissionList );

$globalperms->globalize( $shortPermList, $smarty, false );
$smarty->assign( 'globalperms', $globalperms );

unset($allperms);
