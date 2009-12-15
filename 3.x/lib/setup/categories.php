<?php

// $Id: freetags.php 12550 2008-04-15 17:21:56Z sylvieg $
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

if ($prefs['feature_categories'] == 'y' && $prefs['categories_used_in_tpl'] == 'y') {
	global $categlib; include_once('lib/categories/categlib.php');
	// pick up the objectType from cat_type is set or from section
    if (!empty($section) && !empty($sections) && !empty($sections[$section])) {
		$here = $sections[$section];
		if (isset($_REQUEST[$here['key']])) {
			if (is_array($_REQUEST[$here['key']])) { // tiki-upload_file uses galleryId[]
				$key = $_REQUEST[$here['key']][0];
			} else {
				$key = $_REQUEST[$here['key']];
			}
		}
		if (isset($here['itemkey']) && isset($_REQUEST[$here['itemkey']]) && isset($here['itemObjectType'])) {
			if (strstr($here['itemObjectType'], '%') && isset($_REQUEST[$here['key']])) {
				$objectType = sprintf($here['itemObjectType'], $key);
			} else {
				$objectType = $here['itemObjectType'];
			}
		} elseif (isset($here['key']) && isset($_REQUEST[$here['key']]) && isset($here['objectType'])) {
			$objectType = $here['objectType'];
		}
	}
		$objectCategoryIds = array();
	if (!empty($objectType)) {
		if (isset($here['itemkey']) && isset($_REQUEST[$here['itemkey']]) && isset($here['itemObjectType'])) {
			$objectCategoryIds = $categlib->get_object_categories($objectType, $_REQUEST[$here['itemkey']]);
		} elseif (isset($here['key']) && isset($_REQUEST[$here['key']])) {
			$objectCategoryIds = $categlib->get_object_categories($objectType, $key);
		}
	}
    $smarty->assign_by_ref('objectCategoryIds', $objectCategoryIds);
	// use in smarty {if isset($objectCategoryIds) and in_array(54, $objectCategoryIds)} My stuff ..{/if}
}
