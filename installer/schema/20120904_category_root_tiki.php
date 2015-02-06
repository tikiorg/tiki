<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/**
 * @param $installer
 */
function post_20120904_category_root_tiki($installer)
{
	// Finds the root category on all existing categories
	$categs = $installer->table('tiki_categories');

	$roots = array();

	$map = $categs->fetchMap('categId', 'parentId', array());

	$findRoot = function ($category, $cb) use($map) {
		if (! empty($map[$category])) {
			return $cb($map[$category], $cb);
		} else {
			return $category;
		}
	};

	foreach (array_keys($map) as $categId) {
		$root = $findRoot($categId, $findRoot);

		if ($root != $categId) {
			$categs->update(
				array(
					'rootId' => $root,
				), array(
					'categId' => $categId,
				)
			);
		}
	}
}

