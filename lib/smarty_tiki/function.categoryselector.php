<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

function smarty_function_categoryselector($params, $smarty)
{
	$categlib = TikiLib::lib('categ');
	$categories = $categlib->get_object_categories($params['type'], $params['object']);
	$intersect = array_intersect($categories, $params['categories']);

	$data = implode(
		'',
		array_map(
			function ($categId) {
				$objectlib = TikiLib::lib('object');
				return '<div>' . htmlspecialchars($objectlib->get_title('category', $categId)) . '</div>';
			},
			$intersect
		)
	);

	$url = array(
		'controller' => 'category',
		'action' => 'select',
		'type' => $params['type'],
		'object' => $params['object'],
		'subset' => implode(',', $params['categories']),
	);
	return new Tiki_Render_Editable(
		$data,
		array(
			'layout' => 'block',
			'object_store_url' => $url,
			'field_fetch_url' => $url,
		)
	);
}

