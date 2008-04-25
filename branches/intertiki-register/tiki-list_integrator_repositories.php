<?php
/**
 * $Id: /cvsroot/tikiwiki/tiki/tiki-list_integrator_repositories.php,v 1.14 2007-10-12 07:55:28 nyloth Exp $
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
if (($tiki_p_view_integrator != 'y') && ($tiki_p_admin_integrator != 'y') && ($tiki_p_admin != 'y'))
{
    $smarty->assign('msg',tra("You do not have permission to use this feature"));
    $smarty->display("error.tpl");
    die;
}

// Create instance of integrator
$integrator = new TikiIntegrator($dbTiki);

// Fill list of repositories
$repositories = $integrator->list_repositories(true);
$smarty->assign_by_ref('repositories', $repositories);

// Display the template
$smarty->assign('mid','tiki-list_integrator_repositories.tpl');
$smarty->display("tiki.tpl");

?>