<?php
/** $Header: /cvsroot/tikiwiki/tiki/modules/mod-breadcrumb.php,v 1.3 2003-10-22 18:38:21 sylvieg Exp $
 * \param maxlen = max number of displayed characters for the page name
 */
if (!isset($_SESSION["breadCrumb"])) {
	$_SESSION["breadCrumb"] = array();
}

$bbreadCrumb = array_reverse($_SESSION["breadCrumb"]);
$smarty->assign('breadCrumb', $bbreadCrumb);
$smarty->assign('maxlen', isset($module_params["maxlen"]) ? $module_params["maxlen"] : 0);
?>