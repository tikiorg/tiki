<?php
//
// $Header: /cvsroot/tikiwiki/tiki/tiki-module_controls.php,v 1.2 2003-08-01 10:30:45 redflo Exp $
//

// Initialization
require_once('tiki-setup.php');
include_once('lib/usermodules/usermoduleslib.php');

global $smarty;


$check_req = (isset($_REQUEST["unassign"])
           || isset($_REQUEST["up"])
           || isset($_REQUEST["down"])
           || isset($_REQUEST["left"])
           || isset($_REQUEST["right"]));

if ($tiki_p_configure_modules != 'y' && $check_req)
{
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}

if ($user_assigned_modules != 'y' && $check_req)
{
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if (!$user && $check_req)
{
    $smarty->assign('msg',tra("You must log in to use this feature"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}

if ($check_req)
{
  if (isset($_REQUEST["up"]))
    $usermoduleslib->up_user_module($_REQUEST["up"],$user);
  elseif (isset($_REQUEST["down"]))
    $usermoduleslib->down_user_module($_REQUEST["down"],$user);
  elseif (isset($_REQUEST["left"]))
    $usermoduleslib->set_column_user_module($_REQUEST["left"],$user,'l');
  elseif (isset($_REQUEST["right"]))
    $usermoduleslib->set_column_user_module($_REQUEST["right"],$user,'r');
  else
    $usermoduleslib->unassign_user_module($_REQUEST["unassign"],$user);
}

// TODO: Need to fix this stupid way... Must replace only my own args... (or not?)
$pos = strpos($_SERVER["REQUEST_URI"], "?");
if ($pos)
  $url = substr($_SERVER["REQUEST_URI"], 0, $pos);
else
  $url = $_SERVER["REQUEST_URI"];
$smarty->assign('current_location', $url);


?>