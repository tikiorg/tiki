<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-pagination.php,v 1.2 2004-03-07 21:28:50 mose Exp $

// you need to setup $cant var to the total number of items in list
// you need to setup an array including all GET variable that have to be passed to link
// typical declaration is $urlquery = array('find'=>$find,'trackerId'=>$trackerId);
// the smarty var $urlquery has to be declared.
// the url is built using $smarty.server.PHP_SELF
// the template has to {include file="tiki-pagination.tpl"}
// see tiki-view_tracker.php and tpl for a working example

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

?>
