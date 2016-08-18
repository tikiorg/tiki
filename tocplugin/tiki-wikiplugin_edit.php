<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require 'tiki-setup.php';

if ( ! isset( $_POST['page'], $_POST['content'], $_POST['index'], $_POST['type'], $_SERVER['HTTP_REFERER'] ) )
	die( 'Missing parameters' );

$page = $_POST['page'];

$plugin = strtolower(basename($_POST['type']));
$type = TikiLib::strtoupper($plugin);

if (empty($parserlib)) {
	$parserlib = TikiLib::lib('parser');
}

if ( ! $meta = $parserlib->plugin_info($plugin) )
	exit;

if ( ! isset( $_POST['message'] ) )
	$_POST['message'] = (isset($meta['name']) ? tra($meta['name']) : $plugin) . ' ' . tra('Plugin modified by editor.');

$info = $tikilib->get_page_info($page);
$tikilib->get_perm_object($page, 'wiki page', $info, true);
if ($tiki_p_edit != 'y') {
	header("Location: {$_SERVER['HTTP_REFERER']}");
	exit;
}
$content = $_POST['content'];
$current = $info['data'];

$matches = WikiParser_PluginMatcher::match($current);
$count = 0;
foreach ( $matches as $match ) {
	if ( $match->getName() !== $plugin ) {
		continue;
	}

	++$count;

	if ( $_POST['index'] == $count ) {
		//by using content of "~same~", it will not replace the body that is there
		$content = ($content == "~same~" ? $match->getBody() : $content);
		$params = $match->getArguments();

		// If parameters are provided, rebuild the parameter line
		if ( isset( $_POST['params'] ) && is_array($_POST['params']) ) {
			// $values was relaxed to accept any argument rather than those defined up front 
			// in the plugin's parameter list. This facilitates the use of modules as plugins.
			$params = $_POST['params'];
		}

		$match->replaceWithPlugin($plugin, $params, $content);

		$parsed = $matches->getText();

		$tikilib->update_page($page, $parsed, $_POST['message'], $user, $tikilib->get_ip_address());
		break;
	}
}

header("Location: {$_SERVER['HTTP_REFERER']}");
exit;
