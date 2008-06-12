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

if( ! function_exists( 'json_encode' ) )
{
	require_once 'lib/pear/Services/JSON.php';

	function json_encode( $nodes )
	{
		$json = new Services_JSON();
		return $json->encode($nodes);
	}
}

require_once 'lib/profilelib/listlib.php';

$list = new Tiki_Profile_List;

$sources = $list->getSources();

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) { // {{{
	check_ticket('admin-inc-profiles');

	if( isset($_POST['config']) ) {
		$tikilib->set_preference( 'profile_sources', $_POST['profile_sources'] );
		
		header( 'Location: tiki-admin.php?page=profiles' );
		exit;
	}

	if( isset( $_GET['refresh'] ) ) {
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
	}
} // }}}

if( isset( $_GET['list'] ) ) { // {{{
	$params = array_merge( array( 'repository' => '', 'category' => '', 'profile' => '' ), $_GET );
	$smarty->assign( 'category', $params['category'] );
	$smarty->assign( 'profile', $params['profile'] );
	$smarty->assign( 'repository', $params['repository'] );

	$result = $list->getList( $params['repository'], $params['category'], $params['profile'] );
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
