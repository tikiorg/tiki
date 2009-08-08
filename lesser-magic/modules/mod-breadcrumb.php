<?php
/** $Id$
 * \param maxlen = max number of displayed characters for the page name
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

global $smarty, $prefs;
if (!isset($_SESSION["breadCrumb"])) {
	$_SESSION["breadCrumb"] = array();
}

if(!isset($prefs['category_jail']) || empty($prefs['category_jail'])) {
	$fullBreadCrumb=$_SESSION["breadCrumb"];
} else {
	global $categlib; include_once ('lib/categories/categlib.php');
	global $objectlib; include_once ('lib/objectlib.php');//
	$objectIds=$objectlib->get_object_ids("wiki page", $_SESSION["breadCrumb"]);

	$breadIds=array();
	foreach($_SESSION["breadCrumb"] as $step) {
		if (isset($objectIds[$step])) $breadIds[$objectIds[$step]]=$step;
	}
	
	$relevantIds=$categlib->filter_objects_categories(array_keys($breadIds),$categlib->get_jail());
	$fullBreadCrumb=array();
	foreach ($breadIds as $breadId => $breadName) {
		if (in_array($breadId, $relevantIds)) $fullBreadCrumb[$breadId]=$breadName;
	}
}
$bbreadCrumb = array_slice(array_reverse($fullBreadCrumb), 0, $module_rows);
$smarty->assign('breadCrumb', $bbreadCrumb);
$smarty->assign('maxlen', isset($module_params["maxlen"]) ? $module_params["maxlen"] : 0);

