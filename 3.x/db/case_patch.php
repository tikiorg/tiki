<?php

//this script may only be included - so its better to err & die if called directly.
//smarty is not there - we need setup
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

$list = $tikilib->list_pages(0,-1,'pageName_desc','');

foreach ($list['data'] as $l) {
	$page = $l['pageName'];
	if ($userlib->object_has_one_case_permission($page, 'wiki page')) {
		$value = $userlib->get_object_case_permissions($page, 'wiki page');
		foreach ($value as $val) {
			$userlib->remove_object_case_permission($val['groupName'],$page,'wiki page',$val['permName']);
			$userlib->assign_object_permission($val['groupName'],$page,'wiki page',$val['permName']);
		}
	}
}
?>
