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

require_once('lib/headerlib.php');

$accesslib = TikiLib::lib('access');
$accesslib->check_user($user);

// Create the template instances
$pages = array();

/////////////////////////////////////
// BEGIN User Wizard page section
/////////////////////////////////////

require_once('lib/wizard/pages/user_wizard.php'); 
$pages[] = new UserWizard();

require_once('lib/wizard/pages/user_preferences_info.php'); 
$pages[] = new UserWizardPreferencesInfo();

require_once('lib/wizard/pages/user_preferences_params.php'); 
$pages[] = new UserWizardPreferencesParams();

require_once('lib/wizard/pages/user_preferences_reports.php'); 
$pages[] = new UserWizardPreferencesReports();

require_once('lib/wizard/pages/user_preferences_notifications.php'); 
$pages[] = new UserWizardPreferencesNotifications();

/////////////////////////////////////
// END User Wizard page section
/////////////////////////////////////


// Step the wizard pages
$wizardlib = TikiLib::lib('wizard');
$wizardlib->showPages($pages);


// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('mid', 'tiki-wizard_user.tpl');
$smarty->display("tiki.tpl");
