<?php
/**
 * $Header: /cvsroot/tikiwiki/tiki/tiki-integrator.php,v 1.19 2007-10-12 07:55:28 nyloth Exp $
 *
 * Integrated files viewer (wrapper)
 *
 */

require_once('tiki-setup.php');
require_once('lib/integrator/integrator.php');

// If Integrator is ON, check permissions...
if ($prefs['feature_integrator'] != 'y')
{
    $smarty->assign('msg', tra("This feature is disabled").": feature_integrator");
    $smarty->display("error.tpl");
    die;
}
if (($tiki_p_view_integrator != 'y') && ($tiki_p_admin_integrator != 'y') && ($tiki_p_admin != 'y'))
{
    $smarty->assign('msg',tra("You do not have permission to use this feature"));
    $smarty->display("error.tpl");
    die;
}

$repID = (isset($_REQUEST["repID"]) && strlen($_REQUEST["repID"]) > 0) ? $_REQUEST["repID"] : 0;

if (!isset($_REQUEST["repID"]) && ($repID <= 0))
{
    $smarty->assign('msg',tra("No repository given"));
    $smarty->display("error.tpl");
    die;
}
// Create instance of integrator
$integrator = new TikiIntegrator($dbTiki);
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
    $smarty->display("error.tpl");
    die;
}
// Needs to clear cached version of this file...
if (isset($_REQUEST["clear_cache"]) && $rep["cacheable"])
    $integrator->clear_cached_file($repID, (isset($_REQUEST["file"]) ? $_REQUEST["file"] : ''));
//
$url2cache = $tikilib->httpPrefix().$_SERVER["SCRIPT_NAME"]."?repID=".$repID.(isset($_REQUEST["file"]) ? "&file=".$_REQUEST["file"] : '');
$data = $integrator->get_file($repID, $file, $rep["cacheable"], $url2cache);
$smarty->assign_by_ref('data', $data);
$smarty->assign('repID', $repID);
$smarty->assign('cached', $rep["cacheable"]);
if (isset($_REQUEST["file"])) $smarty->assign('file', $_REQUEST["file"]);

// Display the template
$smarty->assign('mid','tiki-integrator.tpl');
$smarty->display("tiki.tpl");

?>
