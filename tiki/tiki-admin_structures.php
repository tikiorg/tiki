<?php
// Initialization
require_once('tiki-setup.php');
include_once('lib/structures/structlib.php');
include_once("lib/ziplib.php");

if($tiki_p_edit_structures != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}

if(isset($_REQUEST['rremove'])) {
  $structlib->s_remove_page($_REQUEST["rremove"],false);	
}
if(isset($_REQUEST['rremovex'])) {
  $structlib->s_remove_page($_REQUEST["rremovex"],false);	
}

if(isset($_REQUEST['export'])) {
  $structlib->s_export_structure($_REQUEST['export']);
}

if(isset($_REQUEST['export_tree'])) {
  header("content-type: text/plain");
  $structlib->s_export_structure_tree($_REQUEST['export_tree']);
  die;
}


$smarty->assign('askremove','n');
if(isset($_REQUEST['remove'])) {
	$smarty->assign('askremove','y');
	$smarty->assign('remove',$_REQUEST['remove']);
}

if(isset($_REQUEST["create"])) {
  $structlib->s_create_page('','',$_REQUEST["name"]);
}

if(isset($_REQUEST["create_from_tree"])) {
	$tree_lines = explode("\n",$_REQUEST["tree"]);
	$parents = Array('');
	$previous = Array('');
	foreach($tree_lines as $line) {
		// count the tabs
		$tabs = 0;
		while(substr($line,0,1)==" ") {
			$tabs++;
			$line=substr($line,1);
		}
		$parents[$tabs+1]=$line;
		$parent = $parents[$tabs];
		if(isset($previous[$tabs])) {
			$prev=$previous[$tabs];
		} else {
			$prev = '';
		}
		$structlib->s_create_page($parent,$prev,$line);
		$previous[$tabs]=$line;
	}
}

if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'page_asc'; 
} else {
  $sort_mode = $_REQUEST["sort_mode"];
} 

if(!isset($_REQUEST["offset"])) {
  $offset = 0;
} else {
  $offset = $_REQUEST["offset"]; 
}
$smarty->assign_by_ref('offset',$offset);

if(isset($_REQUEST["find"])) {
  $find = $_REQUEST["find"];  
} else {
  $find = ''; 
}
$smarty->assign('find',$find);

$smarty->assign_by_ref('sort_mode',$sort_mode);
$channels = $structlib->list_structures($offset,$maxRecords,$sort_mode,$find);

$cant_pages = ceil($channels["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages',$cant_pages);
$smarty->assign('actual_page',1+($offset/$maxRecords));
if($channels["cant"] > ($offset+$maxRecords)) {
  $smarty->assign('next_offset',$offset + $maxRecords);
} else {
  $smarty->assign('next_offset',-1); 
}
// If offset is > 0 then prev_offset
if($offset>0) {
  $smarty->assign('prev_offset',$offset - $maxRecords);  
} else {
  $smarty->assign('prev_offset',-1); 
}

$smarty->assign_by_ref('channels',$channels["data"]);



// Display the template
$smarty->assign('mid','tiki-admin_structures.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>