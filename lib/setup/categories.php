<?php

// $Id: freetags.php 12550 2008-04-15 17:21:56Z sylvieg $
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
	header('location: index.php');
	exit;
}
if ($prefs['feature_categories'] == 'y') {
	global $categlib; include_once('lib/categories/categlib.php');
	// pick up the objectType from cat_type is set or from section
    if (!empty($section) && !empty($sections) && !empty($sections[$section])) {
		$here = $sections[$section];
		if (isset($here['itemkey']) && isset($_REQUEST[$here['itemkey']]) && isset($here['itemObjectType'])) {
			if (strstr($here['itemObjectType'], '%') && isset($_REQUEST[$here['key']])) {
				$objectType = sprintf($here['itemObjectType'], $_REQUEST[$here['key']]);
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
			$objectCategoryIds = $categlib->get_object_categories($objectType, $_REQUEST[$here['key']]);
		}
	}
    $smarty->assign_by_ref('objectCategoryIds', $objectCategoryIds);
	// use in smarty {if isset($objectCategoryIds) and in_array(54, $objectCategoryIds)} My stuff ..{/if}
	echo $section.'-'.$objectType.'-'.$_REQUEST[$here['key']].'-'.$_REQUEST[$here['itemkey']];print_r($objectCategoryIds);
}