<?php
/**
 * $Id: /cvsroot/tikiwiki/tiki/tiki-admin_integrator.php,v 1.21 2007-10-12 07:55:24 nyloth Exp $
 *
 * Admin interface for repositories management
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
if (($tiki_p_admin_integrator != 'y') && ($tiki_p_admin != 'y')) {
	$smarty->assign('errortype', 401);
    $smarty->assign('msg',tra("You do not have permission to use this feature"));
    $smarty->display("error.tpl");
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
        $smarty->display("error.tpl");
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
        if ($repID != 0) {
					if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
						key_check($area);
						$integrator->remove_repository($repID);
					} else {
						key_get($area);
					}
				}
        break;
    case 'clear':
        if ($repID != 0) $integrator->clear_cache($repID);
        header('location: '.$_SERVER['SCRIPT_NAME'].'?action=edit&repID='.$repID);
        exit;
    default:
        $smarty->assign('msg', tra("Requested action is not supported on repository"));
        $smarty->display("error.tpl");
        die;
        break;
    }
}

// Fill list of repositories
$repositories = $integrator->list_repositories(false);
$smarty->assign_by_ref('repositories', $repositories);

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid','tiki-admin_integrator.tpl');
$smarty->display("tiki.tpl");

?>
