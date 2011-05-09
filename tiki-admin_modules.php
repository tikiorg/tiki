<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'admin';
require_once ('tiki-setup.php');
include_once ('lib/menubuilder/menulib.php');
include_once ('lib/rss/rsslib.php');
include_once ('lib/polls/polllib.php');
include_once ('lib/banners/bannerlib.php');
include_once ('lib/dcs/dcslib.php');
include_once ('lib/modules/modlib.php');
include_once ('lib/structures/structlib.php');
if (!isset($dcslib)) {
    $dcslib = new DCSLib($dbTiki);
}
if (!isset($bannerlib)) {
    $bannerlib = new BannerLib($dbTiki);
}
if (!isset($rsslib)) {
    $rsslib = new RssLib($dbTiki);
}
if (!isset($polllib)) {
    $polllib = new PollLib($dbTiki);
}
if (!isset($structlib)) {
    $structlib = new StructLib($dbTiki);
}
$smarty->assign('wysiwyg', 'n');
if (isset($_REQUEST['wysiwyg']) && $_REQUEST['wysiwyg'] == 'y') {
    $smarty->assign('wysiwyg', 'y');
}
$access->check_permission(array('tiki_p_admin_modules'));
$auto_query_args = array('show_hidden_modules');

$access->check_feature( array('feature_jquery_ui') );

// Values for the user_module edit/create form
$smarty->assign('um_name', '');
$smarty->assign('um_title', '');
$smarty->assign('um_data', '');
$smarty->assign('um_parse', '');
$smarty->assign('assign_name', '');
//$smarty->assign('assign_title','');
$smarty->assign('assign_position', '');
$smarty->assign('assign_order', '');
$smarty->assign('assign_cache', 0);
$smarty->assign('assign_rows', 10);
$smarty->assign('assign_params', '');
if (isset($_REQUEST["clear_cache"])) {
    check_ticket('admin-modules');
    $modlib->clear_cache();
}
$module_groups = array();
$smarty->assign('assign_selected', '');
$smarty->assign('assign_type', '');
$smarty->assign('assign_title', '');
if (!empty($_REQUEST['edit_assign'])) {
    check_ticket('admin-modules');
    $info = $modlib->get_assigned_module($_REQUEST['edit_assign']);
    $grps = '';
    if (!empty($info['groups'])) {
        $module_groups = unserialize($info["groups"]);
        foreach($module_groups as $amodule) {
            $grps = $grps . ' $amodule ';
        }
    }
    $smarty->assign('module_groups', $grps);
    if (isset($info["ord"])) {
        $cosa = "" . $info["ord"];
    } else {
        $cosa = "";
    }
    $smarty->assign_by_ref('assign_name', $info["name"]);
    //$smarty->assign_by_ref('assign_title',$info["title"]);
    $smarty->assign_by_ref('assign_position', $info["position"]);
    $smarty->assign_by_ref('assign_cache', $info["cache_time"]);
    $smarty->assign_by_ref('assign_type', $info["type"]);
    $smarty->assign_by_ref('assign_order', $cosa);
    $smarty->assign_by_ref('info', $info);
    if (!$info['name']) {
        $smarty->assign('assign_selected', $_REQUEST['edit_assign']);
    }

	$modinfo = $modlib->get_module_info( $info['name'] );
	if ($modinfo["type"] != "function") {
		$smarty->assign_by_ref('assign_rows', $info["rows"]);
		$smarty->assign_by_ref('assign_params', $info["params"]); // For old-style (user) modules
	} else {
		if (empty($info['params'])) $info['params'] = array();
		$modlib->dispatchValues( $info['params'], $modinfo['params'] );
		if (isset($modinfo['params']['rows'])) {
			$modinfo['params']['rows']['value'] = $info["rows"];
		}
	}
	$smarty->assign('assign_info', $modinfo);
}
if (isset($_REQUEST['edit_assign']) || isset($_REQUEST['preview'])) {	// will be 0 for a new assignment
	$cookietab = 2;
}
if (!empty($_REQUEST['unassign'])) {
    check_ticket('admin-modules');
    $info = $modlib->get_assigned_module($_REQUEST['unassign']);
	$modlib->unassign_module($_REQUEST['unassign']);
	$logslib->add_log('adminmodules', 'unassigned module ' . $info['name']);
}
if (!empty($_REQUEST['modup'])) {
    check_ticket('admin-modules');
    $modlib->module_up($_REQUEST['modup']);
}
if (!empty($_REQUEST['moddown'])) {
    check_ticket('admin-modules');
    $modlib->module_down($_REQUEST['moddown']);
}
if (!empty($_REQUEST['modleft'])) {
    check_ticket('admin-modules');
    $modlib->module_left($_REQUEST['modleft']);
}
if (!empty($_REQUEST['modright'])) {
    check_ticket('admin-modules');
    $modlib->module_right($_REQUEST['modright']);
}
if (!empty($_REQUEST['module-order'])) {
    check_ticket('admin-modules');
    $module_order = json_decode($_REQUEST['module-order']);
    $modlib->reorder_modules($module_order);
}

/* Edit or delete a user module */
if (isset($_REQUEST["um_update"])) {
    if (empty($_REQUEST["um_name"])) {
        $smarty->assign('msg', tra("Cannot create or update module: You need to specify a name to the module"));
        $smarty->display("error.tpl");
        die;
    }
    if (empty($_REQUEST["um_data"])) {
        $smarty->assign('msg', tra("Cannot create or update module: You cannot leave the data field empty"));
        $smarty->display("error.tpl");
        die;
    }
    check_ticket('admin-modules');
    $_REQUEST["um_update"] = urldecode($_REQUEST["um_update"]);
    $smarty->assign_by_ref('um_name', $_REQUEST["um_name"]);
    $smarty->assign_by_ref('um_title', $_REQUEST["um_title"]);
    $smarty->assign_by_ref('um_data', $_REQUEST["um_data"]);
    $smarty->assign_by_ref('um_parse', $_REQUEST["um_parse"]);
    $modlib->replace_user_module($_REQUEST["um_name"], $_REQUEST["um_title"], $_REQUEST["um_data"], $_REQUEST["um_parse"]);
    $logslib->add_log('adminmodules', 'changed user module ' . $_REQUEST["um_name"]);
}
if (!isset($_REQUEST["groups"])) {
    $_REQUEST["groups"] = array();
}
if (isset($_REQUEST["assign"]) || isset($_REQUEST["preview"])) { // Verify that required parameters are present
	$missing_params = array();
	$modinfo = $modlib->get_module_info( $_REQUEST['assign_name'] );
	if ($_REQUEST['moduleId'] > 0) {
		foreach($modinfo["params"] as $pname => $param) {
			if ($param["required"] && empty($_REQUEST["assign_params"][$pname]))
				$missing_params[] = $param["name"];
		}
	}
	$smarty->assign('missing_params', $missing_params);
}
$smarty->assign('preview', 'n');
if (isset($_REQUEST["preview"])) {
    check_ticket('admin-modules');
    $smarty->assign('preview', 'y');
    $smarty->assign_by_ref('assign_name', $_REQUEST["assign_name"]);
    if (!is_array($_REQUEST["assign_params"])) {
        TikiLib::parse_str($_REQUEST["assign_params"], $module_params);
    } else {
        $module_params=$_REQUEST["assign_params"];
    }
    $smarty->assign_by_ref('module_params', $module_params);
	if (isset($module_params['title'])) {
		$smarty->assign('tpl_module_title', tra( $module_params['title'] ) );
	}

	if (isset($_REQUEST["assign_rows"])) {
		$module_rows = $_REQUEST["assign_rows"];
		$smarty->assign_by_ref('assign_rows', $_REQUEST["assign_rows"]);
	} elseif (isset($_REQUEST["assign_params"]["rows"]))
		$module_rows = $_REQUEST["assign_params"]["rows"];
	else
		$module_rows = 10;

    if ($modlib->is_user_module($_REQUEST["assign_name"])) {
        $info = $modlib->get_user_module($_REQUEST["assign_name"]);
        $smarty->assign_by_ref('user_title', $info["title"]);
        if ($info["parse"] == "y") {
            $parse_data = $tikilib->parse_data($info["data"]);
            $smarty->assign_by_ref('user_data', $parse_data);
        } else {
            $smarty->assign_by_ref('user_data', $info["data"]);
        }
        $data = $smarty->fetch('modules/user_module.tpl');
    } else {
        $phpfile = 'modules/mod-' . $_REQUEST["assign_name"] . '.php';
        $phpfuncfile = 'modules/mod-func-' . $_REQUEST["assign_name"] . '.php';
        $template = 'modules/mod-' . $_REQUEST["assign_name"] . '.tpl';
        if (file_exists($phpfile)) {
            include ($phpfile);
        } elseif (file_exists($phpfuncfile)) {
			if (isset($_REQUEST["assign_params"]["rows"]))
				$module_rows = $_REQUEST["assign_params"]["rows"];
			else
				$module_rows = 10;
            include_once ($phpfuncfile);
            $function = 'module_' . $_REQUEST["assign_name"];
            if( function_exists( $function ) ) {
                $function( array("name" => $_REQUEST["assign_name"], "position" => $_REQUEST["assign_position"], "ord" => $_REQUEST["assign_order"], "cache_time" => $_REQUEST["assign_cache"], "rows" => $module_rows), $_REQUEST["assign_params"] ); // Warning: First argument should have all tiki_modules table fields. This is just a best effort.
            }
        }

        if (file_exists('templates/' . $template)) {
            $data = $smarty->fetch($template);
        } else {
            $data = '';
        }
    }
    if (!empty($_REQUEST['moduleId'])) {
        $smarty->assign('moduleId', $_REQUEST['moduleId']);
    } else {
		$smarty->assign('moduleId', 0);
	}
    $smarty->assign_by_ref('assign_name', $_REQUEST["assign_name"]);
    $smarty->assign_by_ref('assign_params', $_REQUEST["assign_params"]);
    $smarty->assign_by_ref('assign_position', $_REQUEST["assign_position"]);
    $smarty->assign_by_ref('assign_order', $_REQUEST["assign_order"]);
    $smarty->assign_by_ref('assign_cache', $_REQUEST["assign_cache"]);
    $module_groups = $_REQUEST["groups"];
    $grps = '';
    foreach($module_groups as $amodule) {
        $grps = $grps . " $amodule ";
    }
    $smarty->assign('module_groups', $grps);
    $smarty->assign_by_ref('preview_data', $data);

	$modlib->dispatchValues( $_REQUEST['assign_params'], $modinfo['params'] );
	$smarty->assign( 'assign_info', $modinfo );
}
if (isset($_REQUEST["assign"])) {
    check_ticket('admin-modules');
    $_REQUEST["assign"] = urldecode($_REQUEST["assign"]);
    $smarty->assign_by_ref('assign_name', $_REQUEST["assign_name"]);
    $smarty->assign_by_ref('assign_position', $_REQUEST["assign_position"]);
    $smarty->assign_by_ref('assign_params', $_REQUEST["assign_params"]);
    $smarty->assign_by_ref('assign_order', $_REQUEST["assign_order"]);
    $smarty->assign_by_ref('assign_cache', $_REQUEST["assign_cache"]);

	if (isset($_REQUEST["assign_rows"])) {
		$module_rows = $_REQUEST["assign_rows"];
		$smarty->assign_by_ref('assign_rows', $_REQUEST["assign_rows"]);
	} elseif (isset($_REQUEST["assign_params"]["rows"])) {
		$module_rows = $_REQUEST["assign_params"]["rows"];
		unset($_REQUEST["assign_params"]["rows"]); // hack, since rows goes in its own DB field
	} else
		$module_rows = 10;
    $smarty->assign_by_ref('assign_type', $_REQUEST["assign_type"]);
    $module_groups = $_REQUEST["groups"];
    $grps = '';
    foreach($module_groups as $amodule) {
        $grps = $grps . " $amodule ";
    }
    $smarty->assign('module_groups', $grps);
	if (empty($missing_params)) {
		$modlib->assign_module(isset($_REQUEST['moduleId']) ? $_REQUEST['moduleId'] : 0, $_REQUEST["assign_name"], '', $_REQUEST["assign_position"], $_REQUEST["assign_order"], $_REQUEST["assign_cache"], $module_rows, serialize($module_groups) , $_REQUEST["assign_params"], $_REQUEST["assign_type"]);
		$logslib->add_log('adminmodules', 'assigned module ' . $_REQUEST["assign_name"]);
		$modlib->reorder_modules();
		header("location: tiki-admin_modules.php");
	} else {
		$modlib->dispatchValues( $_REQUEST['assign_params'], $modinfo['params'] );
		$smarty->assign( 'assign_info', $modinfo );
	}
}

if (isset($_REQUEST["um_remove"])) {
    $_REQUEST["um_remove"] = urldecode($_REQUEST["um_remove"]);
	$access->check_authenticity(tra('Are you sure you want to delete this User Module?') . '&nbsp;&nbsp;(&quot;' . $_REQUEST["um_remove"] . '&quot;)');
    $modlib->remove_user_module($_REQUEST["um_remove"]);
    $logslib->add_log('adminmodules', 'removed user module ' . $_REQUEST["um_remove"]);
	$cookietab = 1;
}
if (isset($_REQUEST["um_edit"])) {
    check_ticket('admin-modules');
    $_REQUEST["um_edit"] = urldecode($_REQUEST["um_edit"]);
    $um_info = $modlib->get_user_module($_REQUEST["um_edit"]);
    $smarty->assign_by_ref('um_name', $um_info["name"]);
    $smarty->assign_by_ref('um_title', $um_info["title"]);
    $smarty->assign_by_ref('um_data', $um_info["data"]);
    $smarty->assign_by_ref('um_parse', $um_info["parse"]);
}
$user_modules = $modlib->list_user_modules();
$smarty->assign_by_ref('user_modules', $user_modules["data"]);

$all_modules = $modlib->get_all_modules();
sort($all_modules);
$smarty->assign_by_ref('all_modules', $all_modules);
$all_modules_info = array_combine( 
	$all_modules, 
	array_map( array( $modlib, 'get_module_info' ), $all_modules ) 
);
foreach ($all_modules_info as &$mod) {
	$mod['enabled'] = true;
	foreach ($mod['prefs'] as $pf) {
		if ($prefs[$pf] !== 'y') {
			$mod['enabled'] = false;
		}
	}
}
uasort($all_modules_info, 'compare_names');
$smarty->assign_by_ref( 'all_modules_info', $all_modules_info);
if (!empty($_REQUEST['module_list_show_all'])) {
	$smarty->assign('module_list_show_all', true);
}

$orders = array();
for ($i = 1;$i < 50;$i++) {
    $orders[] = $i;
}
$smarty->assign_by_ref('orders', $orders);
$groups = $userlib->list_all_groups();
$allgroups = array();
$temp_max = count($groups);
for ($i = 0;$i < $temp_max;$i++) {
    if (in_array($groups[$i], $module_groups)) {
        $allgroups[$i]["groupName"] = $groups[$i];
        $allgroups[$i]["selected"] = 'y';
    } else {
        $allgroups[$i]["groupName"] = $groups[$i];
        $allgroups[$i]["selected"] = 'n';
    }
}

$smarty->assign("groups", $allgroups);

if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}
$maximum = 0;
$maxRecords = $prefs['maxRecords'];

$galleries = $tikilib->list_galleries($offset, $maxRecords, 'lastModif_desc', $user, '');
$smarty->assign('galleries', $galleries["data"]);
$maximum = max( $maximum, $galleries['cant'] );

$polls = $polllib->list_active_polls($offset, $maxRecords, 'publishDate_desc', '');
$smarty->assign('polls', $polls["data"]);
$maximum = max( $maximum, $polls['cant'] );

$contents = $dcslib->list_content($offset, $maxRecords, 'contentId_desc', '');
$smarty->assign('contents', $contents["data"]);
$maximum = max( $maximum, $contents['cant'] );

$rsss = $rsslib->list_rss_modules($offset, $maxRecords, 'name_desc', '');
$smarty->assign('rsss', $rsss["data"]);
$maximum = max( $maximum, $rsss['cant'] );

$menus = $menulib->list_menus($offset, $maxRecords, 'menuId_desc', '');
$smarty->assign('menus', $menus["data"]);
$maximum = max( $maximum, $menus['cant'] );

$banners = $bannerlib->list_zones();
$smarty->assign('banners', $banners["data"]);
$maximum = max( $maximum, $banners['cant'] );

$wikistructures = $structlib->list_structures('0', '100', 'pageName_asc', '');
$smarty->assign('wikistructures', $wikistructures["data"]);
$maximum = max( $maximum, $wikistructures['cant'] );

$smarty->assign( 'maxRecords', $maxRecords );
$smarty->assign( 'offset', $offset );
$smarty->assign( 'maximum', $maximum );

$assigned_modules = $modlib->get_assigned_modules();
$module_zones = array();
foreach( $modlib->module_zones as $initial => $zone) {
	$module_zones[$initial] = array(
		'id' => $zone,
		'name' => substr($zone, 0, strpos($zone, '_'))
	);
}
$smarty->assign_by_ref( 'assigned_modules', $assigned_modules );
$smarty->assign_by_ref( 'module_zones', $module_zones );

$prefs['module_zones_top'] = 'y';
$prefs['module_zones_topbar'] = 'y';
$prefs['module_zones_pagetop'] = 'y';
$prefs['feature_left_column'] = 'y';
$prefs['feature_right_column'] = 'y';
$prefs['module_zones_pagebottom'] = 'y';
$prefs['module_zones_bottom'] = 'y';

$headerlib->add_css('.module:hover {
	cursor: move;
	background-color: #ffa;
}');
$headerlib->add_cssfile('css/admin.css');
$headerlib->add_jsfile('lib/modules/tiki-admin_modules.js');
$headerlib->add_jsfile('lib/jquery/jquery.json-2.2.js');

$sameurl_elements = array(
    'offset',
    'sort_mode',
    'where',
    'find'
);
ask_ticket('admin-modules');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

if (!empty($_REQUEST['edit_module'])) {	// pick up ajax calls
	$smarty->display("admin_modules_form.tpl");
} else {
	$smarty->assign('mid', 'tiki-admin_modules.tpl');
	$smarty->display("tiki.tpl");
}
