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
// BEGIN Wizard page section
/////////////////////////////////////

require_once('lib/wizard/pages/user_dummy1.php'); 
$pages[] = new UserWizardDummy1();

require_once('lib/wizard/pages/user_dummy2.php'); 
$pages[] = new UserWizardDummy2();

require_once('lib/wizard/pages/user_dummy3.php'); 
$pages[] = new UserWizardDummy3();

/////////////////////////////////////
// END Wizard page section
/////////////////////////////////////


// Step the wizard pages
$wizardlib = TikiLib::lib('wizard');
$wizardlib->showPages($pages);


// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('mid', 'tiki-wizard_user.tpl');
$smarty->display("tiki.tpl");
