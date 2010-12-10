<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require 'tiki-setup.php';

if( ! isset( $_POST['page'], $_POST['content'], $_POST['index'], $_POST['type'], $_SERVER['HTTP_REFERER'] ) )
	die( 'Missing parameters' );

$page = $_POST['page'];

$plugin = strtolower( basename( $_POST['type'] ) );
$type = strtoupper( $plugin );

if( ! $meta = $tikilib->plugin_info( $plugin ) )
	exit;

if( ! isset( $_POST['message'] ) )
	$_POST['message'] = (isset($meta['name']) ? tra($meta['name']) : $plugin) . ' ' . tra('Plugin modified by editor.');

$info = $tikilib->get_page_info($page);
$tikilib->get_perm_object($page, 'wiki page', $info, true);
if ($tiki_p_edit != 'y') {
	header( "Location: {$_SERVER['HTTP_REFERER']}" );
	exit;
}
$content = $_POST['content'];
$current = $info['data'];

$matches = WikiParser_PluginMatcher::match($current);
$count = 0;
foreach( $matches as $match )
{
	if( $match->getName() !== $plugin ) {
		continue;
	}

	++$count;

	if( $_POST['index'] == $count ) {
		$hasBody = !empty($content) && !ctype_space( $content );
		$params = $match->getArguments();

		// If parameters are provided, rebuild the parameter line
		if( isset( $_POST['params'] ) && is_array( $_POST['params'] ) )
		{
			// $values was relaxed to accept any argument rather than those defined up front 
			// in the plugin's parameter list. This facilitates the use of modules as plugins.
			$values = $_POST['params'];

			$parts = array();
			foreach( $values as $key => $value ) {
				if( ! empty( $value ) )
					$parts[] = "$key=\"" . str_replace( '"', "\\\"", $value ) . '"';
			}

			$params = implode( ' ', $parts );
		}

		// Replace the content
		if( $hasBody ) {
			$content = "{{$type}($params)}$content{{$type}}";
		} else {
			$content = "{{$plugin} $params}";
		}

		$match->replaceWith($content);

		$tikilib->update_page( $page, $matches->getText(), $_POST['message'], $user, $tikilib->get_ip_address() );
		break;
	}
}

header( "Location: {$_SERVER['HTTP_REFERER']}" );
exit;
