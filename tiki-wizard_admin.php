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
$headerlib->add_cssfile('css/admin.css');

// Hide the display of the preference dependencies in the wizard
$headerlib->add_css('.pref_dependency{display:none !important;}');

$accesslib = TikiLib::lib('access');
$accesslib->check_permission('tiki_p_admin');

// Create the template instances
$pages = array();

/////////////////////////////////////
// BEGIN Wizard page section
/////////////////////////////////////

require_once('lib/wizard/pages/admin_wizard.php'); 
$pages[0] = new AdminWizard();

require_once('lib/wizard/pages/admin_date_time.php'); 
$pages[1] = new AdminWizardDateTime();

require_once('lib/wizard/pages/admin_language.php'); 
$pages[2] = new AdminWizardLanguage();

require_once('lib/wizard/pages/admin_editor.php'); 
$pages[3] = new AdminWizardEditor();

require_once('lib/wizard/pages/admin_wiki.php'); 
$pages[4] = new AdminWizardWiki();

require_once('lib/wizard/pages/admin_auto_toc.php'); 
$pages[5] = new AdminWizardAutoTOC();

require_once('lib/wizard/pages/admin_namespace.php'); 
$pages[6] = new AdminWizardNamespace();

require_once('lib/wizard/pages/admin_jcapture.php'); 
$pages[7] = new AdminWizardJCapture();

require_once('lib/wizard/pages/admin_files.php'); 
$pages[8] = new AdminWizardFiles();

require_once('lib/wizard/pages/admin_profiles.php'); 
$pages[9] = new AdminWizardProfiles();


/////////////////////////////////////
// END Wizard page section
/////////////////////////////////////


// Step the wizard pages
require_once('lib/wizard/wizardlib.php');
$wizardlib = new WizardLib();
$wizardlib->showPages($pages);


// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('mid', 'tiki-wizard_admin.tpl');
$smarty->display("tiki.tpl");
