<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/wizard/wizard.php');

/**
 * Show the profiles choices
 */
class ProfilesWizardDemoMoreAdvancedConfs extends Wizard
{
    function pageTitle ()
    {
        return tra('Demo of More Advanced Configurations');
    }
    function isEditable ()
    {
        return false;
    }

    function onSetupPage ($homepageUrl)
    {
        global	$smarty, $prefs, $TWV;

        // Run the parent first
        parent::onSetupPage($homepageUrl);

        $smarty->assign('tikiMajorVersion', substr($TWV->version, 0, 2));

        // Assign the page template
        $wizardTemplate = 'wizard/profiles_demo_more_advanced_confs.tpl';
        $smarty->assign('wizardBody', $wizardTemplate);

        return true;
    }

    function onContinue ($homepageUrl)
    {
        // Run the parent first
        parent::onContinue($homepageUrl);
    }
}
