<?php

// Initialization
require_once ('tiki-setup.php');
require_once ('lib/jukebox/jukeboxlib.php');

if ($feature_jukebox != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_jukebox");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_jukebox_genres != 'y') {
	$smarty->assign('msg', tra("Permission denied you can not view this section"));

	$smarty->display("error.tpl");
	die;
}

if(!isset($_REQUEST["genreId"])) {
	$_REQUEST["genreId"]=0;
}

if(isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

if (!isset($_REQUEST["offset"])) {
        $offset = 0;
} else {
        $offset = $_REQUEST["offset"];
}

$smarty->assign_by_ref('offset', $offset);

if (!isset($_REQUEST["sort_mode"])) {
        $sort_mode = "genreName_asc";
} else {
        $sort_mode = $_REQUEST["sort_mode"];
}

$smarty->assign_by_ref('sort_mode', $sort_mode);

$smarty->assign('find',$find);
$smarty->assign('genreId',$_REQUEST["genreId"]);

if (isset($_REQUEST["edit"])) {
	check_ticket ('jukebox-genres');

	if ($_REQUEST["genreId"] == 0) {
		$info = $jukeboxlib->replace_genres($_REQUEST["title"], $_REQUEST["description"], '');
	} else {
		$info = $jukeboxlib->replace_genres($_REQUEST["title"], $_REQUEST["description"], $_REQUEST["genreId"]);
	}
}

if(isset($_REQUEST["edit_mode"])&&$_REQUEST["edit_mode"]) {
	$smarty->assign('edit_mode','y');
	$smarty->assign('edited','y');
	if($_REQUEST["genreId"]>0) {
		$info = $jukeboxlib->get_genre($_REQUEST["genreId"]);

		$smarty->assign_by_ref('title',$info["genreName"]);
		$smarty->assign_by_ref('description',$info["genreDescription"]);
	}
}

if (isset($_REQUEST["remove"])) {
        check_ticket('jukebox-genres');
	$area='deletegenre';
	if ($feature_ticketlib2 != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
		key_check($area);
        	$jukeboxlib->remove_genre($_REQUEST["remove"]);
	} else {
		key_get($area);
	}
}

$listgenres = $jukeboxlib->list_genres($offset, $maxRecords, $sort_mode, $find);

$cant_pages = ceil($listgenres["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($listgenres["cant"] > ($offset + $maxRecords)) {
        $smarty->assign('next_offset', $offset + $maxRecords);
} else {
        $smarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($offset > 0) {
        $smarty->assign('prev_offset', $offset - $maxRecords);
} else {
        $smarty->assign('prev_offset', -1);
}

$smarty->assign_by_ref('listgenres', $listgenres["data"]);

ask_ticket('jukebox-genres');

// Display the template
$smarty->assign('mid', 'tiki-jukebox_genres.tpl');
$smarty->display("tiki.tpl");

?>
