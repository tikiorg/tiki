<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$// (c) Copyright 2002-2009 by authors of the Tiki Wiki CMS Groupware Project

//this script may only be included - so its better to die if called directly.
if (! $allperms = $cachelib->getSerialized("allperms")) {
	$allperms = $userlib->get_permissions(0, -1, 'permName_desc', '', '');
	$cachelib->cacheItem("allperms", serialize($allperms));
}
$permissionList = array();
$adminPermissions = array();

foreach( $allperms['data'] as $row ) {
	$valid = false;

	if( ! $row['feature_check'] ) {
		$valid = true;
	} else {
		foreach( explode( ',', $row['feature_check'] ) as $feature ) {
			if( isset($prefs[$feature]) && $prefs[$feature] == 'y' ) {
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
$is_token_access = false;
if( $prefs['auth_token_access'] == 'y' && isset($_REQUEST['TOKEN']) ) {
	require_once 'lib/auth/tokens.php';
	$token = $_REQUEST['TOKEN'];
		
	unset( $_GET['TOKEN'] );
	unset( $_POST['TOKEN'] );
	unset( $_REQUEST['TOKEN'] );
	$tokenParams = $_GET;

 	/**
 	 * Shared 'Upload File' case
 	 */
	if ( isset($isUpload) && $isUpload && ! empty($_POST['galleryId']) && empty($_GET['galleryId']) ) {
		foreach ( (array) $_POST['galleryId'] as $v ) {
			if ( ! empty( $tokenParams['galleryId'] ) ) {
				if ( $tokenParams['galleryId'] == $v ) {
					continue;
				} else {
					unset( $tokenParams['galleryId'] );
					break;
				}
			}
			$tokenParams['galleryId'] = $v;
		}
	}

	$tokenlib = AuthTokens::build( $prefs );
	if( $groups = $tokenlib->getGroups( $token, $_SERVER['PHP_SELF'], $tokenParams ) ) {
	 	$groupList = $groups;
	 	$detailtoken = $tokenlib->getToken($token);
	 	$is_token_access = true;	
	 	
	 	/**
	 	 * Shared 'File download' case
	 	 */
	 	if(isset($_GET['fileId']) && $detailtoken['parameters'] == '{"fileId":"'.$_GET['fileId'].'"}'){
	 		$_SESSION['allowed'][$_GET['fileId']] = true;
	 	}
	 		 	
		// If notification then alert
		if ($prefs['share_token_notification'] == 'y') {
			$nots = $tikilib->get_event_watches('auth_token_called', $detailtoken['tokenId']);
			$smarty->assign('prefix_url', $base_host);

			// Select in db the tokenId
			$notificationPage = '';
			$smarty->assign_by_ref('page_token', $notificationPage);			
			
			if(is_array($nots)){
				include_once ('lib/webmail/tikimaillib.php');
				$mail = new TikiMail();
				
				$mail->setFrom( $prefs['sender_email'] );
				$mail->setHeader( 'Return-Path', '<' . $prefs['sender_email'] . '>' );
				$mail->setHeader( 'Reply-To', '<' . $prefs['sender_email'] . '>' );
				$mail->setSubject( $detailtoken['email'] . ' ' . tra(' has accessed your temporary shared content') );
				
				foreach($nots as $i=>$not) {
					$notificationPage = $not['url'];

				 	// Delete token from url
					$notificationPage = preg_replace('/[\?&]TOKEN='.$detailtoken['token'].'/','',$notificationPage);
					
					// If file Gallery
					$smarty->assign('filegallery', 'n');
					if(preg_match("/\btiki-download_file.php\b/i",$notificationPage)){
						include_once 'lib/filegals/filegallib.php';
						$smarty->assign('filegallery', 'y');
						$aParams = (array) json_decode($detailtoken['parameters']);
						$smarty->assign('fileId', $aParams['fileId']);
						
						$aFileInfos = $filegallib->get_file_info($aParams['fileId']);
						$smarty->assign('filegalleryId', $aFileInfos['galleryId']);
						$smarty->assign('filename', $aFileInfos['name']);
					}
					
					$smarty->assign('email_token',$detailtoken['email']);
					$txt = $smarty->fetch('mail/user_watch_token.tpl');
					$mail->setHTML($txt);
					$mailsent = $mail->send(array($not['email']));
				}
			}
			
		}
		
		// Log each token access
		$logslib->add_log('token', $detailtoken['email'].' '.tra('has accessed the following shared content:') . ' ' . $notificationPage);
	} else {
		// Error Token expired
		$token_error = tra('Your access to this page has expired');
	}
}

$sequence = array(
	$globalAdminCheck = new Perms_Check_Alternate( 'admin' ),
	new Perms_Check_Direct,
	new Perms_Check_Indirect( $map ),
);

if( $user ) {
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
unset($tokenParams);
