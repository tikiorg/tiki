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
 * smarty_function_fileinfo: Display one info/property of a file (from a file gallery)
 *
 * params:
 *  - _id: ID of the file
 *  - _field: Return the value of the specified field/property
 *  - _link: Return the result inside an A tag that links to the image. The value of _link will be used to match the 'name' attribute of images to use for shadowbox (if feature shadowbox is enabled)
 */
function smarty_function_fileinfo($params, $smarty)
{
	if ( ! is_array($params) || ! isset($params['_id']) || ! isset($params['_field']) ) {
		return;
	}
	$filegallib = TikiLib::lib('filegal');
	$params['_id'] = (int) $params['_id'];

	$infos = $filegallib->get_file($params['_id']);

	if ( isset($infos[$params['_field']]) && $infos[$params['_field']] != '' ) {
		$return = $infos[$params['_field']];
	} elseif ( $params['_field'] == 'name' ) {
		// Fallback to filename if there is no name for the specified file
		$return = $infos['filename'];
	} else {
		$return = '';
	}

	if ( isset($params['_link']) ) {
		global $prefs, $filegals_manager, $url_path;
		$key_type = substr($infos['filetype'], 0, 9);
		$tmp = '<a href="'.$url_path.'tiki-download_file.php?fileId='.$params['_id'].'&amp;display"';
		if ( $prefs['feature_shadowbox'] == 'y' ) {
			$tmp .= ' data-box="shadowbox['.htmlentities($params['_link']).'];type=' . ( in_array($key_type, array('image/png', 'image/jpe', 'image/gif')) ? 'img' : 'iframe' ) . '"';
		}
		$return = $tmp.'>'.$return.'</a>';
		unset($tmp);
	}

	return $return;
}
