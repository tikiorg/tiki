<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_drawings.php,v 1.9 2004-03-31 07:38:41 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/drawings/drawlib.php');

if ($feature_drawings != 'y') {
	$smarty->assign('msg', tra("Feature disabled"));

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_admin_drawings != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST["remove"])) {
	$area = 'deldrawing';
	if (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"])) {
		key_check($area);
		$drawlib->remove_drawing($_REQUEST["remove"]);
	} else {
		key_get($area);
	}
}

if (isset($_REQUEST["removeall"])) {
	$area = 'deldrawingall';
	if (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"])) {
		key_check($area);
		$drawlib->remove_all_drawings($_REQUEST["removeall"]);
	} else {
		key_get($area);
	}
}

if (isset($_REQUEST['del'])) {
	$area = 'deldraw';
	if (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"])) {
		key_check($area);
		foreach (array_keys($_REQUEST['draw'])as $id) {
			$drawlib->remove_drawing($id);
		}
	} else {
		key_get($area);
	}
}

$pars = parse_url($_SERVER["REQUEST_URI"]);
$pars_parts = split('/', $pars["path"]);
$pars = array();

for ($i = 0; $i < count($pars_parts) - 1; $i++) {
	$pars[] = $pars_parts[$i];
}

$pars = join('/', $pars);
$smarty->assign('path', $pars);

$smarty->assign('preview', 'n');

if (isset($_REQUEST['previewfile'])) {
	$draw_info = $drawlib->get_drawing($_REQUEST['previewfile']);

	$smarty->assign('draw_info', $draw_info);
	$smarty->assign('preview', 'y');
}

// Manage offset here
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'name_asc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}

$smarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$smarty->assign('find', $find);
$smarty->assign_by_ref('sort_mode', $sort_mode);

if (isset($_REQUEST['ver']) && $_REQUEST['ver']) {
	$items = $drawlib->list_drawing_history($_REQUEST['ver'], $offset, $maxRecords, $sort_mode, $find);
} else {
	$items = $drawlib->list_drawings($offset, $maxRecords, $sort_mode, $find);
}

$smarty->assign('cant', $items['cant']);
$cant_pages = ceil($items["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($items["cant"] > ($offset + $maxRecords)) {
	$smarty->assign('next_offset', $offset + $maxRecords);
} else {
	$smarty->assign('next_offset', -1);
}

if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$smarty->assign('prev_offset', -1);
}

$smarty->assign_by_ref('items', $items["data"]);

ask_ticket('admin-drawings');

$smarty->assign('mid', 'tiki-admin_drawings.tpl');
$smarty->display("tiki.tpl");

?>
