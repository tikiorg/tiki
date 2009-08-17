<?php
//
// $Id$
// \brief Show last comments on wiki pages
//

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}
if (!function_exists('module_last_comments')) {
	function module_last_comments($limit, $type='wiki page') {
		global $tikilib, $user;
		$bindvars = array($type);
		$where = '';
		switch ($type) {
		case 'article':
			$join = 'left join `tiki_articles` ta on (tc.`object` = ta.`articleId`)';
			$get = ', ta.`title` as name';
			global $tiki_p_admin_cms;
			if ($tiki_p_admin_cms != 'y') {
				$where = 'and `approved`!=?';
				$bindvars[] = 'n';
			}
			break;
		case 'wiki page':
			$join = '';
			$get = ', tc.`object` as name';
			global $tiki_p_admin_wiki;
			global $tiki_p_admin_cms;
			if ($tiki_p_admin_wiki != 'y') {
				$where = 'and `approved`!=?';
				$bindvars[] = 'n';
			}			
			break;			
		default:
			$join = '';
			$get = ', tc.`object` as name';
			global $tiki_p_admin;
			if ($tiki_p_admin != 'y') {
				$where = 'and `approved`!=?';
				$bindvars[] = 'n';
			}
			break;
		}
			
		$query = "select tc.* $get from `tiki_comments` as tc $join where `objectType`=? $where order by `commentDate` desc";
		$result = $tikilib->query($query, $bindvars, $limit, 0);
		$ret = array();

		while ($res = $result->fetchRow()) {
			switch ($type) {
				case 'wiki page':
					$perm = 'tiki_p_view';
					break;
				case 'article':
					$perm = 'tiki_p_read_article';
					break;
				default: 
					return null;
				}
			if ($tikilib->user_has_perm_on_object($user, $res['object'], $res['type'], $perm)) {
				$ret[] = $res;
			}
		}
		return $ret;
	}
}
global $smarty;
switch ($module_params['type']) {
case 'cms': case 'article': case 'articles':
	$module_params['type'] = 'article';
	break;
default:
	$module_params['type'] = 'wiki page';
	break;
}
$comments = module_last_comments($module_rows, $module_params['type']);
$smarty->assign_by_ref('comments', $comments);
$smarty->assign('nonums', isset($module_params['nonums']) ? $module_params['nonums'] : 'n');
$smarty->assign_by_ref('module_rows', $module_rows);


