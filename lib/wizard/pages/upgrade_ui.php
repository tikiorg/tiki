<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/wizard/wizard.php');

/**
 * The Wizard's language handler 
 */
class UpgradeWizardUI extends Wizard
{
    function pageTitle ()
    {
        return tra('User Interface');
    }

	function isEditable ()
	{
		return true;
	}
	
	function onSetupPage ($homepageUrl) 
	{
		global	$smarty, $prefs;

		// Run the parent first
		parent::onSetupPage($homepageUrl);

        // Assign the page template
        $wizardTemplate = 'wizard/upgrade_ui.tpl';
        $smarty->assign('wizardBody', $wizardTemplate);

		$showPage = true;
        // Show if any more specification is needed

        // ElFinder
        if ($prefs['fgal_elfinder_feature'] === 'y') {
            $smarty->assign('promptElFinder', 'y');

            // Determine the current filegal default view
            $defView = $prefs['fgal_default_view'];
            if (isset($defView)) {
                if ($defView == 'finder') {
                    $smarty->assign('useElFinderAsDefault', true);
                } else {
                    $smarty->assign('useElFinderAsDefault', false);
                }
            }
        }

        return $showPage;
	}

	function onContinue ($homepageUrl) 
	{
        global $tikilib;

		// Run the parent first
		parent::onContinue($homepageUrl);

        if (isset($_REQUEST['useElFinderAsDefault']) && $_REQUEST['useElFinderAsDefault'] === 'on') {
            // Set ElFinder view as the default File Gallery view
            $tikilib->set_preference('fgal_default_view', 'finder');
        } else {
            // Re-set back default File Gallery view to list
            $tikilib->set_preference('fgal_default_view', 'list');
        }
    }
}
