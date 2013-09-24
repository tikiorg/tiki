<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/wizard/wizard.php');
require_once('lib/notifications/notificationlib.php');
include_once ('lib/userprefs/userprefslib.php');

/**
 * Set up the wysiwyg editor, including inline editing
 */
class UserWizardPreferencesReports extends Wizard 
{
	function isEditable ()
	{
		return true;
	}

	function onSetupPage ($homepageUrl) 
	{
		global	$user, $smarty;

		// Run the parent first
		parent::onSetupPage($homepageUrl);
		
		// Show if option is selected
		if ($prefs['feature_user_watches'] === 'y' && $prefs['feature_daily_report_watches'] === 'y') {
			$showPage = true;
		}

		// Setup initial wizard screen
		$reportsUsers = Reports_Factory::build('Reports_Users');
		$reportsUsersUser = $reportsUsers->get($user);
		$smarty->assign_by_ref('report_preferences', $reportsUsersUser);

		// Assign the page template
		$wizardTemplate = 'wizard/user_preferences_reports.tpl';
		$smarty->assign('wizardBody', $wizardTemplate);
		
		return true;		
	}

	function onContinue ($homepageUrl) 
	{
		global $tikilib, $user;

		$reportsManager = Reports_Factory::build('Reports_Manager');

		$interval = filter_input(INPUT_POST, 'interval', FILTER_SANITIZE_STRING);
		$view = filter_input(INPUT_POST, 'view', FILTER_SANITIZE_STRING);
		$type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
		$always_email = filter_input(INPUT_POST, 'always_email', FILTER_SANITIZE_NUMBER_INT);
		if ($always_email != 1)
			$always_email = 0;
		
		$reportsManager->save($user, $interval, $view, $type, $always_email);

		// Run the parent first
		parent::onContinue($homepageUrl);
	}
}
