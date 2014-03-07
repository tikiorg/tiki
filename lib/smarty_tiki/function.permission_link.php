<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_permission_link( $params, $smarty )
{
	if ( ! isset( $params['type'], $params['id'] )) {
		return tra('No object information provided.');
	}

	$params = new JitFilter($params);
	$type = $params->type->text();
	$id = $params->id->text();

	$objectlib = TikiLib::lib('object');
	$link = 'tiki-objectpermissions.php?' . http_build_query([
		'objectType' => $type,
		'objectId' => $id,
		'permType' => $params->permType->text() ?: $type,
		'objectName' => $params->title->text() ?: $objectlib->get_title($type, $id),
	], '', '&');

	$perms = Perms::get($type, $id);
	$source = $perms->getResolver()->from();

	return $smarty->fetch('permission_link.tpl', [
		'permission_link' => [
			'url' => $link,
			'active' => $source == 'object',
			'mode' => $params->mode->word() ?: 'glyph',
		],
	]);
}

