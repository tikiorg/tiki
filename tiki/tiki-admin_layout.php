<?php
// Initialization
require_once('tiki-setup.php');

if($layout_section != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}


if($tiki_p_admin != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display('error.tpl');
    die;
}

$sections=Array('wiki','galleries','file_galleries','cms','blogs','forums','chat','categories','games','faqs','html_pages','quizzes');

$sections_smt	= Array();
for($i=0;$i<count($sections);$i++) {
  $aux["name"]=$sections[$i];
  $name=$sections[$i];
  $aux["left_column"]=$tikilib->get_preference("${name}_left_column",'y');
  $aux["right_column"]=$tikilib->get_preference("${name}_right_column",'y');
  $aux["top_bar"]=$tikilib->get_preference("${name}_top_bar",'y');
  $aux["bot_bar"]=$tikilib->get_preference("${name}_bot_bar",'y');
  $sections_smt[]=$aux;
}
$smarty->assign('sections',$sections_smt);


foreach($sections as $section) {

if(isset($_REQUEST["${section}_layout"])) {

 if(isset($_REQUEST["${section}_left_column"]) && $_REQUEST["${section}_left_column"]=="on") {
    $tikilib->set_preference("${section}_left_column",'y'); 
    $smarty->assign("${section}_left_column",'y');
  } else {
    $tikilib->set_preference("${section}_left_column",'n');
    $smarty->assign("${section}_left_column",'n');
  }
  if(isset($_REQUEST["${section}_right_column"]) && $_REQUEST["${section}_right_column"]=="on") {
    $tikilib->set_preference("${section}_right_column",'y'); 
    $smarty->assign("${section}_right_column",'y');
  } else {
    $tikilib->set_preference("${section}_right_column",'n');
    $smarty->assign("${section}_right_column",'n');
  }
  if(isset($_REQUEST["${section}_top_bar"]) && $_REQUEST["${section}_top_bar"]=="on") {
    $tikilib->set_preference("${section}_top_bar",'y'); 
    $smarty->assign("${section}_top_bar",'y');
  } else {
    $tikilib->set_preference("${section}_top_bar",'n');
    $smarty->assign("${section}_top_bar",'n');
  }
  if(isset($_REQUEST["${section}_bot_bar"]) && $_REQUEST["${section}_bot_bar"]=="on") {
    $tikilib->set_preference("${section}_bot_bar",'y'); 
    $smarty->assign("${section}_bot_bar",'y');
  } else {
    $tikilib->set_preference("${section}_bot_bar",'n');
    $smarty->assign("${section}_bot_bar",'n');
  }
}
}

$sections_smt	= Array();
for($i=0;$i<count($sections);$i++) {
  $aux["name"]=$sections[$i];
  $name=$sections[$i];
  $aux["left_column"]=$tikilib->get_preference("${name}_left_column",'y');
  $aux["right_column"]=$tikilib->get_preference("${name}_right_column",'y');
  $aux["top_bar"]=$tikilib->get_preference("${name}_top_bar",'y');
  $aux["bot_bar"]=$tikilib->get_preference("${name}_bot_bar",'y');
  $sections_smt[]=$aux;
}
$smarty->assign('sections',$sections_smt);


// Display the template
$smarty->assign('mid','tiki-admin_layout.tpl');
$smarty->display('tiki.tpl');

?>

