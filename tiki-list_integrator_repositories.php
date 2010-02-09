<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
//$Id: /cvsroot/tikiwiki/tiki/tiki-list_integrator_repositories.php,v 1.14 2007-10-12 07:55:28 nyloth Exp $
require_once ('tiki-setup.php');
require_once ('lib/integrator/integrator.php');
$access->check_feature('feature_integrator');
$access->check_permission(array('tiki_p_view_integrator','tiki_p_admin_integrator','tiki_p_admin'));
// Create instance of integrator
$integrator = new TikiIntegrator($dbTiki);
// Fill list of repositories
$repositories = $integrator->list_repositories(true);
$smarty->assign_by_ref('repositories', $repositories);
// Display the template
$smarty->assign('mid', 'tiki-list_integrator_repositories.tpl');
$smarty->display("tiki.tpl");
