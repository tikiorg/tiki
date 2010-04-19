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

$pos = -1;
$count = 0;
while( true )
{
	$posA = strpos( $current, $sa = "{{$type}(", $pos + 1 );
	$posB = $posB = strpos( $current, $sb = "{{$plugin}", $pos + 1 );

	if( $posA === false && $posB === false )
		break;

	// Make sure we didn't get a partial word with {plugin (ex: {pluginfoo)
	if( $posB !== false && ctype_alnum( $current{$posB + 1 + strlen($plugin)} ) ) {
		$pos = $posB;
		$posB = false;
	}
	
	$syntax = 'normal';
	if( $posA !== false && $posB !== false ) {
		// out of {PLUGIN( or {plugin, take the lowest one
		$pos = min( $posA, $posB );
	} elseif( $posA !== false ) {
		$pos = $posA;
	} elseif( $posB !== false ) {
		$pos = $posB;
		$syntax = 'short';
	} else {
		$pos++;
		continue;
	}

	++$count;

	if( $_POST['index'] == $count )
	{
		$hasBody = false;

		if( $syntax == 'normal' ) {
			$endparamA = strpos( $current, '/}', $pos );
			$endparamB = strpos( $current, ')}', $pos );
			if( false === $endparamA && false === $endparamB )
				die( 'Failed to find end of plugin code.' );
			if( ( false !== $endparamA 
				&& ( false !== $endparamB && $endparamA < $endparamB ) )
				|| $endparamB === false )
			{
				$endparam = $endparamA + 2;
			}
			else
			{
				$endparam = $endparamB + 2;
				$hasBody = true;
			}
		} else {
			if( false !== $endparam = strpos( $current, '}', $pos ) )
				$endparam = $endparam + 1;
		}

		if( $hasBody )
		{
			$body = $endparam;
			$endbody = strpos( $current, "{{$type}}", $endparam );
			if( false === $endbody )
				die( 'Failed to find end of plugin body.' );

			$before = substr( $current, 0, $body );
			$after = substr( $current, $endbody + strlen("{{$type}}") );
		}
		else
		{
			$before = substr( $current, 0, $endparam );
			$after = substr( $current, $endparam );
		}

		$hasBody = !empty($content) && !ctype_space( $content );

		// If parameters are provided, rebuild the parameter line
		if( isset( $_POST['params'] ) && is_array( $_POST['params'] ) )
		{
		  // $values was relaxed to accept any argument rather than those defined up front 
		  // in the plugin's parameter list. This facilitates the use of modules as plugins.
		        $values = $_POST['params'];

			$parts = array();
			foreach( $values as $key => $value )
				if( ! empty( $value ) )
					$parts[] = "$key=\"" . str_replace( '"', "\\\"", $value ) . '"';

			$params = implode( ' ', $parts );

			if( $hasBody )
				$before = substr( $before, 0, $pos )
					. "{{$type}($params)}";
			else
				$before = substr( $before, 0, $pos )
					. "{{$plugin} $params}";
		}
		elseif( $syntax == 'short' && $hasBody )
		{
			// Need to convert the begining of the plugin to the long syntax
			$before = substr( $before, 0, $pos )
				. "{{$type}(" . substr( $before, $pos + strlen($plugin) + 2, -1 ) . ")}";
		}

		// Replace the content
		if( $hasBody )
			$content = $before . $content . "{{$type}}" . $after;
		else
			$content = $before . $content . $after;

		$tikilib->update_page( $page, $content, $_POST['message'], $user, $tikilib->get_ip_address() );
	}
}

header( "Location: {$_SERVER['HTTP_REFERER']}" );
exit;
