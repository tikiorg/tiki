<?php

// {MODULE(module=>last_actions,maxlen=>60,nonums=>y,showuser=>y,showdate=>y)}{MODULE}

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
global $tiki_p_admin, $user;

if ($user) {
	// admin gets ALL actions, user only his own.
	$bindvars=array();
	if ($tiki_p_admin != 'y') {
		$uid = " where (`user` = ?)";
		$bindvars[]=$user;
	} else {
		$uid = "";
	}
	
	$offset=0;
	
	// retrieve latest actions from logging table
	$query = "select * from `tiki_actionlog` $uid order by ".$tikilib->convert_sortmode("lastModif_desc");
	$query_cant = "select count(*) from `tiki_actionlog` $uid";
	$result = $tikilib->query($query,$bindvars,$module_rows,$offset);
	$cant = $tikilib->getOne($query_cant,$bindvars);
	$ret = array();

	$showuser = isset($module_params["showuser"]) ? $module_params["showuser"] : 'n';
	$showdate = isset($module_params["showdate"]) ? $module_params["showdate"] : 'n';
	
	while ($res = $result->fetchRow()) {
		$res["action"] = $res["action"]." ".$res['objectType'].':'.$res["object"];
		if ($showdate=='y') { $res["action"] = $res["action"]." ".tra("at")." ".htmlspecialchars($tikilib->iso_8601($res["lastModif"])); }
		if ($showuser=='y') { $res["action"] = $res["user"].": ".$res["action"]; }
		$ret[] = $res;
	}
	$smarty->assign('modLastActions', $ret);
	$smarty->assign('modLastActionNo', $cant);
	$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');
	$smarty->assign('maxlen', isset($module_params["maxlen"]) ? $module_params["maxlen"] : '20');
}
?>
