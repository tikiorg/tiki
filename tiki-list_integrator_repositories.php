<?php
/**
 * $Header: /cvsroot/tikiwiki/tiki/tiki-list_integrator_repositories.php,v 1.1 2003-10-13 17:17:49 zaufi Exp $
 *
 * Admin interface for repositories management
 *
 */

require_once('tiki-setup.php');
require_once('lib/integrator/integrator.php');

// Fill list of repositories
$repositories = $integrator->list_repositories();
$smarty->assign_by_ref('repositories', $repositories);

// Display the template
$smarty->assign('mid','tiki-list_integrator_repositories.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>