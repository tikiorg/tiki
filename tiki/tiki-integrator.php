<?php
/**
 * $Header: /cvsroot/tikiwiki/tiki/tiki-integrator.php,v 1.9 2003-11-08 19:50:32 zaufi Exp $
 *
 * Integrated files viewer (wrapper)
 *
 */

require_once('tiki-setup.php');
require_once('lib/integrator/integrator.php');

// Check permissions
if ($tiki_p_view_integrator != 'y' || $tiki_p_admin_integrator != 'y')
{
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}

$repID = (isset($_REQUEST["repID"]) && strlen($_REQUEST["repID"]) > 0) ? $_REQUEST["repID"] : 0;

if (!isset($_REQUEST["repID"]) && ($repID <= 0))
{
    $smarty->assign('msg',tra("No repository given"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}
// Get repository configuration data
$rep = $integrator->get_repository($repID);

// Check if given file present at configured location
$file = $integrator->get_rep_file($rep, isset($_REQUEST["file"]) ? $_REQUEST["file"] : '');
if ((substr($file, 0, 7) != 'http://') 
 && (substr($file, 0, 8) != 'https://')
 && !file_exists($file))
{
    if ($tiki_p_admin == 'y')
      $smarty->assign('msg',tra("File not found ").$file);
    else
      $smarty->assign('msg',tra("File not found ").$_REQUEST["file"]);
    $smarty->display("styles/$style_base/error.tpl");
    die;
}

$data = $integrator->get_file($repID, $file, $rep["cacheable"], httpPrefix().$_SERVER["REQUEST_URI"]);
$smarty->assign_by_ref('data', $data);

// Display the template
$smarty->assign('mid','tiki-integrator.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>