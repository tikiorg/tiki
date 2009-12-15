<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_include_blogs.php,v 1.20.2.1 2007-10-20 05:21:41 pkdille Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

require_once 'lib/profilelib/profilelib.php';
require_once 'lib/profilelib/installlib.php';
require_once 'lib/profilelib/listlib.php';

$list = new Tiki_Profile_List;

$sources = $list->getSources();

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	check_ticket('admin-inc-profiles');

	if( isset($_POST['config']) ) { // {{{
		$tikilib->set_preference( 'profile_sources', $_POST['profile_sources'] );
		$tikilib->set_preference( 'profile_channels', $_POST['profile_channels'] );

		header( 'Location: tiki-admin.php?page=profiles' );
		exit;
	} // }}}

	if( isset($_POST['forget'], $_POST['pp'], $_POST['pd']) ) { // {{{

		$profile = Tiki_Profile::fromNames( $_POST['pd'], $_POST['pp'] );
		$profile->removeSymbols();

		$installer = new Tiki_Profile_Installer;
		$installer->install( $profile );
		
		if( $target = $profile->getInstructionPage() ) {
			global $wikilib; require_once 'lib/wiki/wikilib.php';

			$target = $wikilib->sefurl( $target );
		} else {
			$target = $_SERVER['REQUEST_URI'];
		}
		
		header( 'Location: ' . $target );
		exit;
	} // }}}

	if( isset($_POST['install'], $_POST['pd'], $_POST['pp']) ) { // {{{

		$data = array();
		foreach( $_POST as $key => $value )
			if( $key != 'url' && $key != 'install' )
				$data[str_replace('_', ' ', $key )] = $value;

		$installer = new Tiki_Profile_Installer;
		$installer->setUserData( $data );

		$profile = Tiki_Profile::fromNames( $_POST['pd'], $_POST['pp'] );
		$installer->install( $profile );
		if( $target = $profile->getInstructionPage() ) {
			global $wikilib; require_once 'lib/wiki/wikilib.php';

			$target = $wikilib->sefurl( $target );
		} else {
			$target = $_SERVER['REQUEST_URI'];
		}
		
		header( 'Location: ' . $target );
		exit;
	} // }}}

	if( isset( $_GET['refresh'] ) ) { // {{{
		$toRefresh = (int) $_GET['refresh'];
		if( isset( $sources[$toRefresh] ) )
		{
			echo json_encode( array(
				'status' => $list->refreshCache( $sources[$toRefresh]['url'] ) ? 'open' : 'closed',
				'lastupdate' => date('Y-m-d H:i:s' ),
			) );
		}
		else
			echo '{}';
		exit;
	} // }}}

	if( isset( $_GET['getinfo'], $_GET['pd'], $_GET['pp'] ) ) { // {{{

		$installer = new Tiki_Profile_Installer;

		$profile = Tiki_Profile::fromNames( $_GET['pd'], $_GET['pp'] );
		$error = '';

		try
		{
			if( ! $deps = $installer->getInstallOrder( $profile ) )
			{
				$deps = $profile->getRequiredProfiles(true);
				$deps[] = $profile;
				$sequencable = false;
			}
			else
				$sequencable = true;
		}
		catch( Exception $e )
		{
			$error = $e->getMessage();
			$sequencable = false;
		}

		$dependencies = array();
		$userInput = array();
		foreach( $deps as $d )
			if( ! $installer->isInstalled( $d ) ) {
				$dependencies[] = $d->pageUrl;
				$userInput = array_merge( $userInput, $d->getRequiredInput() );
			}

		$parsed = $tikilib->parse_data( $profile->pageContent );
		$installed = $installer->isInstalled( $profile );

		echo json_encode( array(
			'dependencies' => $dependencies,
			'userInput' => $userInput,
			'installable' => $sequencable,
			'error' => $error,
			'content' => $parsed,
			'already' => $installed,
			'url' => $profile->url,
		) );
		exit;
	} // }}}
}

if( isset( $_GET['list'] ) ) { // {{{
	$params = array_merge( array( 'repository' => '', 'category' => '', 'profile' => '' ), $_GET );
	$smarty->assign( 'category', $params['category'] );
	$smarty->assign( 'profile', $params['profile'] );
	$smarty->assign( 'repository', $params['repository'] );

	if ( $_GET['preloadlist'] && $params['repository'] ) 
		$list->refreshCache( $params['repository'] );
	$result = $list->getList( $params['repository'], $params['category'], $params['profile'] );

	$category_list = $list->getCategoryList( $params['repository'] );
	$smarty->assign( 'category_list', $category_list );	

	$smarty->assign( 'result', $result );
} // }}}

$threshhold = time() - 1800;
$oldSources = array();
foreach( $sources as $key => $source )
	if( $source['lastupdate'] < $threshhold )
		$oldSources[] = $key;

$smarty->assign( 'sources', $sources );
$smarty->assign( 'oldSources', $oldSources );

ask_ticket('admin-inc-profiles');

?>
