<?php

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
