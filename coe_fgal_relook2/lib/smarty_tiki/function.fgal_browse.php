<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
 * smarty_function_fgal_browse: Display the content of a file gallery in browse mode (i.e. with thumbnails)
 *
 * params will be used as smarty params for browse_file_gallery.tpl, except special params starting with '_' :
 *  - _id: ID of the gallery
 *  - _offset
 *  - _maxRecords
 *  - _find
 */
function smarty_function_fgal_browse($params, &$smarty) {
	if ( ! is_array($params) || ! isset($params['_id']) ) return;
	global $tikilib, $userlib, $tiki_p_view_file_gallery, $prefs;

	if ( ! isset($params['nbCols']) ) $params['nbCols'] = 0;
	if ( ! isset($params['show_selectall']) ) $params['show_selectall'] = 'y';
	if ( ! isset($params['show_infos']) ) $params['show_infos'] = 'y';
	if ( ! isset($params['show_details']) ) $params['show_details'] = 'y';
	if ( ! isset($params['thumbnail_size']) ) $params['thumbnail_size'] = $prefs['fgal_thumb_max_size'];
	if ( ! isset($params['checkbox_label']) ) $params['checkbox_label'] = '';
	if ( ! isset($params['file_checkbox_name']) ) $params['file_checkbox_name'] = '';

	foreach ( $params as $k => $v ) {
		if ( $k[0] == '_' ) continue;
		$smarty->assign($k, $v);
	}

	if ( ! isset($params['_offset']) ) $params['_offset'] = 0;
	if ( ! isset($params['_maxRecords']) ) $params['_maxRecords'] = -1;
	if ( ! isset($params['_sort_mode']) ) $params['_sort_mode'] = '';
	if ( ! isset($params['_find']) ) $params['_find'] = '';

	if ( $params['_id'] > 0 && $tiki_p_view_file_gallery == 'y' ) {
		$filegallib = TikiLib::lib('filegal');

		if ( $gal_info = $filegallib->get_file_gallery($params['_id']) ) {
			$tikilib->get_perm_object($params['_id'], 'file gallery', $gal_info);
			if ( $userlib->object_has_one_permission($params['_id'], 'file gallery') ) {
				$smarty->assign('individual', 'y'); ///TO CHECK
			}
			///FIXME        $podCastGallery = $filegallib->isPodCastGallery($_REQUEST['galleryId'], $gal_info);
		}

		// Get listing display config
		include_once('fgal_listing_conf.php');

		$gal_info['show_action'] = 'n';
		$smarty->assign_by_ref('gal_info', $gal_info);

		// Get list of files in the gallery
		$files = $filegallib->get_files($params['_offset'], $params['_maxRecords'], $params['_sort_mode'], $params['_find'], $params['_id']);
		$smarty->assign_by_ref('files', $files['data']);
		$smarty->assign('cant', $files['cant']); ///FIXME
	}

	return '<div style="padding: 1px; overflow-y: hidden; overflow-x: auto;">'."\n".$smarty->fetch('browse_file_gallery.tpl')."\n</div>";
}
