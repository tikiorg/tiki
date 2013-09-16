<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

class WizardLib extends TikiLib
{
	public function onLogin($user, $homePageUrl, $force = false)
	{
		global $base_url, $userlib;
		
		$openWizard = false;
		
		if ($force) {
			// Force opening of the wizard
			// Needed for fresh-/re-installs, when admin is an anonymous user at check time
			$openWizard = true;
			
		} else {
			// Check the user status
			$isAdmin = $userlib->user_has_permission($user, 'tiki_p_admin');
		
			// Check if the wizard should be opened
			$activeLoginWizard = $this->get_preference('wizard_admin_hide_on_login') !== 'y';
			if ($isAdmin && $activeLoginWizard) {
				$openWizard = true;
			}
		}
		
		if ($openWizard) {	
			// User is an admin
			$this->startAdminWizard($homePageUrl,0);
		} else {
			// A regular user
			// New User wizard is not implemented
		}
	}
	
	public function startAdminWizard($homePageUrl, $stepNr=0)
	{
		// Start the admin wizard
		$url = $base_url.'tiki-wizard_admin.php?&stepNr=' . $stepNr . '&url=' . rawurlencode($homePageUrl);
		header('Location: '.$url);
		exit;
	}
	
	/*
	*	Wizard's page stepping logic
	*/
	public function showPages($pages)
	{
		global	$smarty;
		
		if (!isset($_REQUEST['url'])) {
			$smarty->assign('msg', tra("No return URL specified"));
			$smarty->display("error.tpl");
			die;
		}
		if (empty($pages)) {
			$smarty->assign('msg', tra("No wizard pages specified"));
			$smarty->display("error.tpl");
			die;
		}
		
		// Assign the return URL
		$homepageUrl = $_REQUEST['url'];
		$smarty->assign('homepageUrl', $homepageUrl);

		$isFirstStep = !isset($_REQUEST['wizard_step']);
		$isUserStep = isset($_REQUEST['stepNr']);	// User defined step nr
		if ($isUserStep) {
			$stepNr = intval($_REQUEST['stepNr']);
		} else {
			$stepNr = intval($_REQUEST['wizard_step']);
		}
		
		// Validate the specified stepNr
		if (($stepNr < 0) || ($stepNr >= count($pages))) {
			$smarty->assign('msg', tra("Invalid wizard stepNr specified"));
			$smarty->display("error.tpl");
			die;
		}
		
		if (!$isFirstStep || ($isUserStep && $stepNr > 0)) {
			$pages[$stepNr]->onContinue();
			if (count($pages) > $stepNr+1) {
				$stepNr += 1;
				if (count($pages) == $stepNr+1) {
					$smarty->assign('lastWizardPage', 'y');
				}
				$pages[$stepNr]->onSetupPage($homepageUrl);
			} else {
				// Return to homepage, when we get to the end
				header('Location: '.$homepageUrl);
				exit;
			}
		} else {
			$pages[0]->onSetupPage($homepageUrl);
		}

		$showOnLogin = $this->get_preference('wizard_admin_hide_on_login') !== 'y';
		$smarty->assign('showOnLogin', $showOnLogin);

		$smarty->assign('wizard_step', $stepNr);
	}
	
	public function setupEditor($editorType) // ($useWysiwyg, $editorType, $useInlineEditing)
	{
		$wysisygPrefs = array();
		switch ($editorType) {
			case 'html':
				$wysisygPrefs['wysiwyg_htmltowiki'] = 'n';
				break;
			
			case 'wiki':
			default:
				$wysisygPrefs['wysiwyg_htmltowiki'] = 'y';
				break;
		}
		$this->set_preference('wysiwyg_htmltowiki', $wysisygPrefs['wysiwyg_htmltowiki']);
	}
	
	public function showOnLogin($showOnLogin)
	{
		$hide = $showOnLogin === 'y' ? 'n' : 'y';
		$this->set_preference('wizard_admin_hide_on_login', $hide);
	}
}
