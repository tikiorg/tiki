<?php
/**
 * $Header: /cvsroot/tikiwiki/tiki/tiki-list_integrator_repositories.php,v 1.8 2003-11-11 21:15:10 sylvieg Exp $
 *
 * Admin interface for repositories management
 *
 */

require_once('tiki-setup.php');
require_once('lib/integrator/integrator.php');

f ($feature_integrator != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_integrator");
	$smarty->display("styles/$style_base/error.tpl");
	die;
}
// Check permissions
if (($tiki_p_view_integrator != 'y') && ($tiki_p_admin_integrator != 'y') && ($tiki_p_admin != 'y'))
{
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}

// Fill list of repositories
$repositories = $integrator->list_repositories(true);
$smarty->assign_by_ref('repositories', $repositories);

// Display the template
$smarty->assign('mid','tiki-list_integrator_repositories.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>