<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
require_once ('lib/integrator/integrator.php');
// If Integrator is ON, check permissions...
$access->check_feature('feature_integrator');
$access->check_permission(array('tiki_p_admin_integrator'));

// Setup local variables from request or set default values
$repID = isset($_REQUEST['repID']) ? $_REQUEST['repID'] : 0;
$name = isset($_REQUEST['name']) ? $_REQUEST['name'] : '';
$path = isset($_REQUEST['path']) ? $_REQUEST['path'] : '';
$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : '';
$cssfile = isset($_REQUEST['cssfile']) ? $_REQUEST['cssfile'] : '';
$expiration = isset($_REQUEST['expiration']) ? $_REQUEST['expiration'] : 0;
$description = isset($_REQUEST['description']) ? $_REQUEST['description'] : '';
$vis = isset($_REQUEST['vis']) ? ($_REQUEST['vis'] == 'on' ? 'y' : 'n') : 'n';
$cacheable = isset($_REQUEST['cacheable']) ? ($_REQUEST['cacheable'] == 'on' ? 'y' : 'n') : 'n';

// Create instance of integrator
$integrator = new TikiIntegrator($dbTiki);

// Check if 'submit' pressed ...
if (isset($_REQUEST['save'])) {
	// ... and all mandatory paramaters r OK
	if (strlen($name) > 0) $integrator->add_replace_repository($repID, $name, $path, $start, $cssfile, $vis, $cacheable, $expiration, $description);
	else {
		$smarty->assign('msg', tra("Repository name can't be an empty"));
		$smarty->display('error.tpl');
		die;
	}
}

// Whether some action requested?
if (isset($_REQUEST['action'])) {
	switch ($_REQUEST['action']) {
		case 'edit':
			if ($repID != 0) {
				$rep = $integrator->get_repository($repID);
				$smarty->assign('repID', $repID);
				$smarty->assign('name', $rep['name']);
				$smarty->assign('path', $rep['path']);
				$smarty->assign('start', $rep['start_page']);
				$smarty->assign('cssfile', $rep['css_file']);
				$smarty->assign('expiration', $rep['expiration']);
				$smarty->assign('vis', $rep['visibility']);
				$smarty->assign('cacheable', $rep['cacheable']);
				$smarty->assign('description', $rep['description']);
			}
			break;

		case 'rm':
			if ($repID != 0) {
				$access->check_authenticity();
				$integrator->remove_repository($repID);
			}
			break;

		case 'clear':
			if ($repID != 0) $integrator->clear_cache($repID);
			header('location: ' . $_SERVER['SCRIPT_NAME'] . '?action=edit&repID=' . $repID);
			exit;

		default:
			$smarty->assign('msg', tra('Requested action is not supported on repository'));
			$smarty->display('error.tpl');
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
$smarty->assign('mid', 'tiki-admin_integrator.tpl');
$smarty->display('tiki.tpl');
