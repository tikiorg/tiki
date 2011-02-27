<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

function module_wiki_last_comments_info() {
	return array(
		'name' => tra('Newest Comments'),
		'description' => tra('Lists the specified number of comments starting from the most recently posted.'),
		'prefs' => array( ),
		'params' => array(
			'moretooltips' => array(
				'name' => tra('More in tooltips'),
				'description' => tra('If set to "y", the name of the object on which a comment is made is not displayed in the module box, but instead moved in the item\'s tooltip.') . " " . tr('Default: "n".')
			),
			'type' => array(
				'name' => tra('Object type'),
				'description' => tra('Type of the objects from which comments will be listed. Possible values:') . '  wiki page, article. ' . tra('Default value:') . ' wiki page',
				'filter' => 'word'
			)
		),
		'common_params' => array('rows', 'nonums')
	);
}

function module_wiki_last_comments( $mod_reference, $module_params ) {
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
				if ($tiki_p_admin_wiki != 'y') {
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
	global $smarty, $prefs;
	if (!isset($module_params['type'])) $module_params['type'] = "wiki page";
	switch ($module_params['type']) {
	case 'cms': case 'article': case 'articles':
		if (!$prefs["feature_articles"]) return;
		$module_params['type'] = 'article';
		$smarty->assign('tpl_module_title', tra("Last article comments"));
		break;
	default:
		if (!$prefs["feature_wiki"]) return;
		$module_params['type'] = 'wiki page';
		$smarty->assign('tpl_module_title', tra("Last wiki comments"));
		break;
	}
	$comments = module_last_comments($mod_reference["rows"], $module_params['type']);
	$smarty->assign_by_ref('comments', $comments);
	$smarty->assign('moretooltips', isset($module_params['moretooltips']) ? $module_params['moretooltips'] : 'n');
	$smarty->assign('type', $module_params['type']);
}
