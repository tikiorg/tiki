<?php
/** $Header: /cvsroot/tikiwiki/tiki/modules/mod-breadcrumb.php,v 1.8 2007-03-20 14:04:32 sylvieg Exp $
 * \param maxlen = max number of displayed characters for the page name
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (!isset($_SESSION["breadCrumb"])) {
	$_SESSION["breadCrumb"] = array();
}
$bbreadCrumb = array_slice(array_reverse($_SESSION["breadCrumb"]), 0, $module_rows);
$smarty->assign('breadCrumb', $bbreadCrumb);
$smarty->assign('maxlen', isset($module_params["maxlen"]) ? $module_params["maxlen"] : 0);
?>
