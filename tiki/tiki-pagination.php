<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-pagination.php,v 1.7 2005-05-18 10:58:58 mose Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}


// !! you need to have $offset var setup with default value 0
// !! you need to setup $cant var to the total number of items in list
// !! you need to setup an array including all GET variable that have to be passed to link
// typical declaration is $urlquery = array('find'=>$find,'trackerId'=>$trackerId);
// the smarty var $urlquery has to be declared.
// the url is built using $smarty.server.PHP_SELF
// the template has to {include file="tiki-pagination.tpl"}
// see tiki-view_tracker.php and tpl for a working example

if (isset($cant) and $cant) {
	if (!isset($offset)) $offset = 0;
	if (!isset($maxRecords)) $maxRecords = 10;
	$smarty->assign_by_ref('offset', $offset);
	$smarty->assign_by_ref('maxRecords', $maxRecords);
	$cant_pages = ceil($cant / $maxRecords);
	$smarty->assign_by_ref('cant_pages', $cant_pages);
	$smarty->assign('actual_page', 1 + ($offset / $maxRecords));
	if ($cant > ($offset + $maxRecords)) {
		$smarty->assign('next_offset', $offset + $maxRecords);
	} else {
		$smarty->assign('next_offset', -1);
	}
	if ($offset > 0) {
		$smarty->assign('prev_offset', $offset - $maxRecords);
	} else {
		$smarty->assign('prev_offset', -1);
	}
}
?>
