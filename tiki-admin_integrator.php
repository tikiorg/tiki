<?php
/**
 * $Header: /cvsroot/tikiwiki/tiki/tiki-admin_integrator.php,v 1.13 2003-11-12 01:00:56 zaufi Exp $
 *
 * Admin interface for repositories management
 *
 */

require_once('tiki-setup.php');
require_once('lib/integrator/integrator.php');

// If Integrator is ON, check permissions...
if ($feature_integrator != 'y')
{
	$smarty->assign('msg', tra("This feature is disabled").": feature_integrator");
	$smarty->display("styles/$style_base/error.tpl");
	die;
}
if (($tiki_p_admin_integrator != 'y') && ($tiki_p_admin != 'y'))
{
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}

// Setup local variables from request or set default values
$repID       = isset($_REQUEST["repID"])       ? $_REQUEST["repID"]       :  0;
$name        = isset($_REQUEST["name"])        ? $_REQUEST["name"]        : '';
$path        = isset($_REQUEST["path"])        ? $_REQUEST["path"]        : '';
$start       = isset($_REQUEST["start"])       ? $_REQUEST["start"]       : '';
$cssfile     = isset($_REQUEST["cssfile"])     ? $_REQUEST["cssfile"]     : '';
$expiration  = isset($_REQUEST["expiration"])  ? $_REQUEST["expiration"]  :  0;
$description = isset($_REQUEST["description"]) ? $_REQUEST["description"] : '';
$vis         = isset($_REQUEST["vis"])         ? ($_REQUEST["vis"]       == 'on' ? 'y' : 'n')  : 'n';
$cacheable   = isset($_REQUEST["cacheable"])   ? ($_REQUEST["cacheable"] == 'on' ? 'y' : 'n')  : 'n';

// Create instance of integrator
$integrator = new TikiIntegrator($dbTiki);

// Check if 'submit' pressed ...
if (isset($_REQUEST["save"]))
{
    // ... and all mandatory paramaters r OK
    if (strlen($name)  > 0)
        $integrator->add_replace_repository($repID, $name, $path, $start, $cssfile,
                                            $vis, $cacheable, $expiration, $description);
    else
    {
        $smarty->assign('msg',tra("Repository name can't be an empty"));
        $smarty->display("styles/$style_base/error.tpl");
        die;
    }
}
// Whether some action requested?
if (isset($_REQUEST["action"]))
{
    switch ($_REQUEST["action"])
    {
    case 'edit':
        if ($repID != 0)
        {
            $rep = $integrator->get_repository($repID);
            $smarty->assign('repID', $repID);
            $smarty->assign('name', $rep["name"]);
            $smarty->assign('path', $rep["path"]);
            $smarty->assign('start', $rep["start_page"]);
            $smarty->assign('cssfile', $rep["css_file"]);
            $smarty->assign('expiration', $rep["expiration"]);
            $smarty->assign('vis', $rep["visibility"]);
            $smarty->assign('cacheable', $rep["cacheable"]);
            $smarty->assign('description', $rep["description"]);
        }
        break;
    case 'rm':
        if ($repID != 0) $integrator->remove_repository($repID);
        break;
    case 'clear':
        if ($repID != 0) $integrator->clear_cache($repID);
        header('location: '.$_SERVER['SCRIPT_NAME'].'?action=edit&repID='.$repID);
        exit;
    default:
        $smarty->assign('msg', tra("Requested action is not supported on repository"));
        $smarty->display("styles/$style_base/error.tpl");
        die;
        break;
    }
}

// Fill list of repositories
$repositories = $integrator->list_repositories(false);
$smarty->assign_by_ref('repositories', $repositories);

// Display the template
$smarty->assign('mid','tiki-admin_integrator.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>