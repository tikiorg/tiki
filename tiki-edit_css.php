<?php 
// $Id: tiki-edit_css.php,v 1.1 2003-06-25 04:03:52 mose Exp $
include_once("tiki-setup.php");
include_once("lib/csslib.php");
// remove soon..
#$feature_edit_css = 'y';
#$tiki_p_create_css = 'y';
if(!isset($feature_editcss)) $feature_editcss = 'n';
if(!isset($tiki_p_create_css)) $tiki_p_create_css = 'n';

if($feature_editcss != 'y') {
  $smarty->assign('msg',tra("Feature disabled"));
	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if($tiki_p_create_css != 'y') {
  $smarty->assign('msg',tra("You dont have permission to use this feature"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}
if (!isset($_REQUEST["editstyle"])) $_REQUEST["editstyle"] = '';
if (!isset($_REQUEST["sub"])) $_REQUEST["sub"] = '';
if (!isset($_REQUEST["try"])) $_REQUEST["try"] = '';

$editstyle = $_REQUEST["editstyle"];
$styledir = "/home/mose/var/tikicvs/styles";

if (isset($_REQUEST["edit"]) and $_REQUEST["edit"]) {
	$action = 'edit';
	$data = implode("",file("$styledir/$editstyle.css"));
} elseif (isset($_REQUEST["save"]) and $_REQUEST["save"]) {
	$action = 'display';
	$data = '';
	$file = str_replace("-$user","",$editstyle);
	$fp = fopen("$styledir/{$file}-$user.css","w");
	$editstyle = "{$file}-$user";
	if(!$fp) {
		$smarty->assign('msg',tra("You dont have permission to write the style sheet"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
  }
  fwrite($fp,$_REQUEST["data"]);
  fclose($fp);
} else {
  $action = 'display';
	$data = '';
}	
$smarty->assign('action',$action);
$smarty->assign('data',$data);

$cssdata = $csslib->browse_css("$styledir/$editstyle.css");
if ((!$cssdata["error"]) and is_array($cssdata["content"])) {
	$parsedcss = $csslib->parse_css($cssdata["content"]);
} else {
	$parsedcss = $cssdata["error"];
}
$smarty->assign('css',$parsedcss);
$smarty->assign('editstyle',$editstyle);

if ($_REQUEST["try"]) {
	$style = "$editstyle.css";
	$smarty->assign('style',$style);
}

$list = $csslib->list_css($styledir);
$smarty->assign('list',$list);

$smarty->assign('mid','tiki-edit_css.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>
