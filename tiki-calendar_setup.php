<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
  header('location: index.php');
  exit;
}

$trunc = "20"; // put in a pref, number of chars displayed in cal cells
$dc = $tikilib->get_date_converter($user);

if (isset($_REQUEST["todate"]) && $_REQUEST['todate']) {
	$_SESSION['CalendarFocusDate'] = $_REQUEST['todate'];
} elseif (isset($_SESSION['CalendarFocusDate']) && $_SESSION['CalendarFocusDate']) {
	$_REQUEST["todate"] = $_SESSION['CalendarFocusDate'];
} else {
	$focusdate = $dc->getDisplayDateFromServerDate(mktime(date('G'),date('i'),date('s'), date('m'), date('d'), date('Y'))); /* user date */
	$_SESSION['CalendarFocusDate'] = $focusdate;
	$_REQUEST["todate"] = $_SESSION['CalendarFocusDate'];
}

$focusdate = $_REQUEST['todate'];
list($focus_day, $focus_month, $focus_year) = array(
	date("d", $focusdate),
	date("m", $focusdate),
	date("Y", $focusdate)
);
$focuscell = mktime(0,0,0,$focus_month,$focus_day,$focus_year);
$smarty->assign('focusdate', $focusdate);
$smarty->assign('focuscell', $focuscell);

if (!isset($_SESSION['CalendarViewMode']) or !$_SESSION['CalendarViewMode']) {
	$_SESSION['CalendarViewMode'] = $calendar_view_mode;
}

if (isset($_REQUEST["viewmode"]) and $_REQUEST["viewmode"]) {
	$_SESSION['CalendarViewMode'] = $_REQUEST["viewmode"];
}

if (!isset($_SESSION['CalendarViewMode']) or !$_SESSION['CalendarViewMode']) {
	$_SESSION['CalendarViewMode'] = 'month';
}

$smarty->assign_by_ref('viewmode', $_SESSION['CalendarViewMode']);

if (isset($_REQUEST["viewlist"])) {
	$viewlist = $_REQUEST["viewlist"];
	$_SESSION['CalendarViewList'] = $viewlist;
} else {
	$viewlist = "";
}
$smarty->assign_by_ref('viewlist', $_SESSION['CalendarViewList']);

?>
