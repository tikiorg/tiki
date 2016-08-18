<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/wizard/wizard.php');

/**
 * Set up the wysiwyg editor, including inline editing
 */
class AdminWizardWysiwyg extends Wizard 
{
    function pageTitle ()
    {
        return tra('Set up Wysiwyg editor');
    }
	function isEditable ()
	{
		return true;
	}
	function isVisible ()
	{
		global	$prefs;
		return $prefs['feature_wysiwyg'] === 'y';
	}

	function onSetupPage ($homepageUrl) 
	{
		global $prefs;
		$smarty = TikiLib::lib('smarty');

		// Run the parent first
		parent::onSetupPage($homepageUrl);

		if (!$this->isVisible()) {
			return false;
		}

		// Setup initial wizard screen
		$smarty->assign('useHighlighter', isset($prefs['feature_syntax_highlighter']) && $prefs['feature_syntax_highlighter'] === 'y' ? 'y' : 'n');
		$smarty->assign('useWysiwyg', isset($prefs['feature_wysiwyg']) && $prefs['feature_wysiwyg'] === 'y' ? 'y' : 'n');
		$smarty->assign('useWysiwygDefault', isset($prefs['wysiwyg_default']) && $prefs['wysiwyg_default'] === 'y' ? 'y' : 'n');
		$smarty->assign('useInlineEditing', isset($prefs['wysiwyg_inline_editing']) && $prefs['wysiwyg_inline_editing'] === 'y' ? 'y'  : 'n');
		$smarty->assign('editorType', isset($prefs['wysiwyg_htmltowiki']) && $prefs['wysiwyg_htmltowiki'] === 'y' ? 'wiki' : 'html');
		
		return true;		
	}

	function getTemplate()
	{
		$wizardTemplate = 'wizard/admin_wysiwyg.tpl';
		return $wizardTemplate;
	}

	function onContinue ($homepageUrl) 
	{
		$tikilib = TikiLib::lib('tiki');

		// Run the parent first
		parent::onContinue($homepageUrl);
		
		$editorType = $_REQUEST['editorType'];
		switch ($editorType) {
			case 'wiki':
				// Wysiwyg in wiki mode is always optional (or?).
				//	The setting is presented under HTML mode, and the user can change it there.
				//	Unaware that it affects the wiki mode also, where it is safe to switch between wysiwyg and text mode.
				$tikilib->set_preference('wysiwyg_optional', 'y');
				$tikilib->set_preference('wysiwyg_htmltowiki', 'y');	// Use wiki syntax
				break;
			
			case 'html':
				// Always use Wysiwyg mode as default
				//	The setting is presented under WIKI mode, and the user can change it there. 
				//	Unaware that it affects the HTML mode also, where Wysiwyg always should be the default.
				$tikilib->set_preference('wysiwyg_default', 'y');
				$tikilib->set_preference('wysiwyg_htmltowiki', 'n');	// No not use wiki syntax
				break;
		}
	}
}
