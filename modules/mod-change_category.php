<?php
// $Header: /cvsroot/tikiwiki/tiki/modules/mod-change_category.php,v 1.6.2.10 2008-02-27 14:47:05 sylvieg Exp $

//this script may only be included - so its better to die if called directly.
// param: id, shy, notop, detail, categorize,multiple,group,path, add, del
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
global $prefs, $user;
global $logslib; require_once('lib/logs/logslib.php');
global $categlib; require_once('lib/categories/categlib.php');
  
// temporary limitation to wiki pages
// params : id (id of parent categ) and shy (show only if page is in categ)
if ($prefs['feature_categories'] == 'y' && (isset($_REQUEST['page']) || isset($_REQUEST['page_ref_if']))) {
	if (empty($_REQUEST['page'])) {
		global $structlib; include_once('lib/structures/structlib.php');
		$page_info = $structlib->s_get_page_info($_REQUEST['page_ref_id']);
		$_REQUEST['page'] = $page_info['page'];
	}
  if (!empty($module_params['id'])) {
    $id = $module_params['id'];
	$cat_parent = $categlib->get_category_name($id);
  } else {
	$id = 0;
	$cat_parent = '';
  }
  if (isset($module_params['shy'])) {
    $notshy = false;
  } else {
    $notshy = true;
  }

  $cat_type = 'wiki page';
  $cat_objid = $_REQUEST['page'];
  
  $categs = $categlib->list_categs($id);
	global $tiki_p_admin;

	if ($tiki_p_admin != 'y') {
		$ctg = array();
		foreach ($categs as $i=>$cat) {
			if (!$userlib->object_has_one_permission($cat['categId'], 'category') or $userlib->object_has_permission($user, $cat['categId'], 'category', 'tiki_p_view_categories')) {
				$ctg[] = $cat;
			}
		}
		$categs = $ctg;
	}

  if (!empty($module_params['group']) && $module_params['group'] == 'y') {
	  if (!$user) {
		  return;
	  }
	  $userGroups = $userlib->get_user_groups_inclusion($user);
	  foreach ($categs as $i=>$cat) {
		  if (isset($userGroups[$cat['name']])) {
			  continue;
		  }
		  $ok = false;
		  foreach ($cat['tepath'] as $c) {
			  if (isset($userGroups[$c])) {
				  $ok = true;
				  break;
			  }
		  }
		  if (!$ok) {
			  unset($categs[$i]);
		  }
	  }
  }

  $num = count($categs);
  if (!$num) {
	  return;
  }
  $modcatlist = array();
  $categsid = array();
  for ($i=0;$i<$num;$i++) {
    $categsid[] = $categs[$i]['categId'];
  }

  if (isset($_REQUEST['remove']) && in_array($_REQUEST['remove'], $categsid) && (!isset($module_params['del']) || $module_params['del'] != 'n')) {
	  $catObjectId = $categlib->is_categorized($cat_type, $_REQUEST['page']);
	  $query = "delete from `tiki_category_objects` where `catObjectId`=? and `categId`=?";
	  $result = $tikilib->query($query,array((int) $catObjectId, (int)$_REQUEST['remove']));
  }

  if (isset($_REQUEST["modcatid"]) and $_REQUEST["modcatid"] == $id) {
	$cs ="";
	if (!isset($module_params['detail']) || $module_params['detail'] != 'y') {
		$cats = $categlib->get_object_categories($cat_type, $cat_objid);
		$catObjectId = $categlib->is_categorized($cat_type, $_REQUEST['page']);
		foreach ($cats as $cs) {
			if (in_array($cs,$categsid)) {
				$query = "delete from `tiki_category_objects` where `catObjectId`=? and `categId`=?";
				$result = $tikilib->query($query,array((int) $catObjectId, (int) $cs));
			}
		}
	}
    if (isset($_REQUEST['modcatchange'])) {
		if (!is_array($_REQUEST['modcatchange'])) {
			$_REQUEST['modcatchange'] = array($_REQUEST['modcatchange']);
		}
		foreach ($_REQUEST['modcatchange'] as $cat) {
			$categlib->categorize_page($_REQUEST['page'], $cat);
			$logslib->add_log('step',"changed ".$_REQUEST['page']." from $cs to ".$_REQUEST['modcatchange']);
		}
    }
    else {
      $logslib->add_log('step',"changed ".$_REQUEST['page']." from $cs to top");
    }
    header('Location: '.$_SERVER['REQUEST_URI']);
    die;
  }

  $incategs = $categlib->get_object_categories($cat_type, $cat_objid);

  $remainCateg = false;
  $modcatlist = array();
  for ($i=0;$i<$num;$i++) {
    $cid = $categs[$i]['categId'];
    $modcatlist[$cid] = $categs[$i];
    if (in_array($cid,$incategs)) {
      $modcatlist[$cid]['incat'] = 'y';
      $notshy = true;
    } else {
      $modcatlist[$cid]['incat'] = 'n';
      $remainCateg = true;
    }
  }

  $smarty->assign_by_ref('remainCateg', $remainCateg);
  $smarty->assign('showmodule',$notshy);
  if (isset($changecateg)) /* big pacth... changecateg is not defined somewhere else */
    $smarty->assign('modname',$changecateg.$id);
  else
    $smarty->assign('modname',"change_category");
  $smarty->assign('modcattitle',sprintf(tra('Categorize %s in %s'),$_REQUEST['page'],$cat_parent));
  $smarty->assign('modcatlist',$modcatlist);
  $smarty->assign('modcatid',$id);
}
?>
