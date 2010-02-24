<?php

function smarty_function_object_link( $params, $smarty ) {

	if( ! isset( $params['type'], $params['id'] ) ) {
		return tra('No object information provided.');
	}

	$type = $params['type'];
	$object = $params['id'];

	switch( $type ) {
	case 'wiki page':
	case 'wikipage':
	case 'wiki':
		$function = 'smarty_function_object_link_wiki';
		break;
	case 'user':
		$function = 'smarty_function_object_link_user';
		break;
	case 'external':
		$function = 'smarty_function_object_link_external';
		break;
	default:
		return tr('No rules to display object %1 of type %0.', $type, $object );
	}

	return $function( $object );
}

function smarty_function_object_link_wiki( $page ) {
	require_once 'lib/smarty_tiki/modifier.sefurl.php';
	require_once 'lib/smarty_tiki/modifier.escape.php';

	$escapedPage = smarty_modifier_escape( $page );
	$escapedHref = smarty_modifier_escape( smarty_modifier_sefurl( $page, 'wiki' ) );

	return '<a href="' . $escapedHref . '">' . $escapedPage . '</a>';
}

function smarty_function_object_link_user( $user ) {
	require_once 'lib/smarty_tiki/modifier.userlink.php';

	return smarty_modifier_userlink( $user );
}

function smarty_function_object_link_external( $link ) {
	global $cachelib; require_once 'lib/cache/cachelib.php';
	global $tikilib;

	if( ! $data = $cachelib->getCached( $link, 'object_link_ext' ) ) {
		$body = $tikilib->httprequest( $link );
		if( preg_match( '|<title>(.+)</title>|', $body, $parts ) ) {
			$title = TikiFilter::get('text')->filter($parts[1]);
		} else {
			$title = $link;
		}

		require_once 'lib/smarty_tiki/modifier.escape.php';
		$escapedHref = smarty_modifier_escape( $link );
		$escapedTitle = smarty_modifier_escape( $title );
		$data = '<a href="' . $escapedHref . '">' . $escapedTitle . '</a>';

		$cachelib->cacheItem( $link, $data, 'object_link_ext' );
	}

	return $data;
}

