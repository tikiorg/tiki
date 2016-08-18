<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/*
 * smarty_function_reindex_file_pixel: Display a 1x1 transparent gif image that will start a background reindexation process of a file
 *
 * params:
 *  - id: id of the file to reindex
 */
function smarty_function_reindex_file_pixel($params, $smarty)
{
	if ( ! is_array($params) || ! isset($params['id']) || ( $id = (int) $params['id'] ) <= 0 ) {
		return '';
	}

	global $tikiroot;
	return '<img src="' . $tikiroot . 'reindex_file.php?id=' . $id . '" width="1" height="1" border="0" alt="" />';
}
