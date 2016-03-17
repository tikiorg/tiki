<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/wizard/wizard.php');

/**
 * Show the profiles choices
 */
class ProfilesWizardDemoCommonConfs extends Wizard
{
    function pageTitle ()
    {
        return tra('Demo of Commonly Used Configurations');
    }
	function isEditable ()
	{
		return false;
	}
	
	function onSetupPage ($homepageUrl) 
	{
		global$TWV;
		$smarty = TikiLib::lib('smarty');

		// Run the parent first
		parent::onSetupPage($homepageUrl);

		$smarty->assign('tikiMajorVersion', substr($TWV->version, 0, 2));
		
		return true;		
	}

	function getTemplate()
	{
		$wizardTemplate = 'wizard/profiles_demo_common_confs.tpl';
		return $wizardTemplate;
	}

	function onContinue ($homepageUrl) 
	{
		// Run the parent first
		parent::onContinue($homepageUrl);
	}
}
