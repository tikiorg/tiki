<?php

if (!isset($_SESSION["breadCrumb"])) {
	$_SESSION["breadCrumb"] = array();
}

$bbreadCrumb = array_reverse($_SESSION["breadCrumb"]);
$smarty->assign('breadCrumb', $bbreadCrumb);

?>