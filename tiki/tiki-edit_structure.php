<?php
// Initialization
require_once('tiki-setup.php');
include_once('lib/structures/structlib.php');

if($tiki_p_edit_structures != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}


if(!isset($_REQUEST["structure"])) {
  $smarty->assign('msg',tra("No structure indicated"));
  $smarty->display("styles/$style_base/error.tpl");
  die;
}
if(!isset($_REQUEST["page"])) {
  $_REQUEST["page"]=$_REQUEST["structure"];
}
$smarty->assign('page',$_REQUEST["page"]);
$smarty->assign('structure',$_REQUEST["structure"]);
$pages=$structlib->get_structure_pages($_REQUEST["structure"]);
$smarty->assign('pages',$pages);

if(isset($_REQUEST["create"])) {
  if(!isset($_REQUEST["after"])) $_REQUEST["after"]='';	
  $structlib->s_create_page($_REQUEST["page"],$_REQUEST["after"],$_REQUEST["name"]);	
}

$smarty->assign('remove','n');
if(isset($_REQUEST["remove"])) {
  $smarty->assign('remove','y');
  $smarty->assign('removepage',$_REQUEST["remove"]);
}

if(isset($_REQUEST["rremove"])) {
  $structlib->s_remove_page($_REQUEST["rremove"],false);	
}
if(isset($_REQUEST["sremove"])) {
  $structlib->s_remove_page($_REQUEST["sremove"],true);	
}


$subpages = $structlib->get_pages($_REQUEST["page"]);
$max = $structlib->get_max_children($_REQUEST["page"]);
$smarty->assign('subpages',$subpages);
$smarty->assign('max',$max);




$html='';
$subtree = $structlib->get_subtree($_REQUEST["structure"],$_REQUEST["structure"],$html);
$smarty->assign('subtree',$subtree);
//print('<pre>'.htmlspecialchars($html).'</pre>');
$smarty->assign('html',$html);

// Display the template
$smarty->assign('mid','tiki-edit_structure.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>