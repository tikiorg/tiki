<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require 'tiki-setup.php';

require_once('lib/wizard/wizardlib.php');
$wizardlib = new WizardLib();

if (!isset($_REQUEST['url'])) {
	$smarty->assign('msg', tra("No return URL specified"));
	$smarty->display("error.tpl");
	die;
}

// Create the template instances
$pages = array();

/////////////////////////////////////
// BEGIN Wizard page section
/////////////////////////////////////

require_once('lib/wizard/pages/admin_wizard.php'); 
$pages[0] = new AdminWizard();

require_once('lib/wizard/pages/admin_wiki_env.php'); 
$pages[1] = new AdminWizardWikiEnv();

/////////////////////////////////////
// END Wizard page section
/////////////////////////////////////



// Assign the return URL
$homepageUrl = $_REQUEST['url'];
$smarty->assign('homepageUrl', $homepageUrl);

$stepNr = intval($_REQUEST['wizard_step']);
if (isset($_REQUEST['wizard_step'])) {

	$pages[$stepNr]->onContinue();
	if (count($pages) > $stepNr+1) {
		$stepNr += 1;
		$pages[$stepNr]->onSetupPage($homepageUrl);
	} else {
		// Return to homepage, when we get to the end
		header('Location: '.$homepageUrl);
		exit;
	}
} else {
	$pages[0]->onSetupPage($homepageUrl);
}

$showOnLogin = $tikilib->get_preference('wizard_admin_hide_on_login') !== 'y';
$smarty->assign('showOnLogin', $showOnLogin);

$smarty->assign('wizard_step', $stepNr);

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('mid', 'tiki-wizard_admin.tpl');
$smarty->display("tiki.tpl");
