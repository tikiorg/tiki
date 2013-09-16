<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/wizard/wizard.php');

/**
 * Set up the wysiwyg editor, including inline editing
 */
class AdminWizardEditor extends Wizard 
{
	function onSetupPage ($homepageUrl) 
	{
		global	$smarty, $prefs;

		// Run the parent first
		parent::onSetupPage($homepageUrl);
		
		// Setup initial wizard screen
		$smarty->assign('useHighlighter', isset($prefs['feature_syntax_highlighter']) && $prefs['feature_syntax_highlighter'] === 'y' ? 'y' : 'n');
		$smarty->assign('highlighterTheme', isset($prefs['feature_syntax_highlighter_theme']) && $prefs['feature_syntax_highlighter_theme'] === 'default' ? 'default' : 'off');
		$smarty->assign('useWysiwyg', isset($prefs['feature_wysiwyg']) && $prefs['feature_wysiwyg'] === 'y' ? 'y' : 'n');
		$smarty->assign('useWysiwygDefault', isset($prefs['wysiwyg_default']) && $prefs['wysiwyg_default'] === 'y' ? 'y' : 'n');
		$smarty->assign('useInlineEditing', isset($prefs['wysiwyg_inline_editing']) && $prefs['wysiwyg_inline_editing'] === 'y' ? 'y'  : 'n');
		$smarty->assign('editorType', isset($prefs['wysiwyg_htmltowiki']) && $prefs['wysiwyg_htmltowiki'] === 'y' ? 'wiki' : 'html');

		// Assign the page temaplte
		$wizardTemplate = 'wizard/admin_editor.tpl';
		$smarty->assign('wizardBody', $wizardTemplate);
	}

	function onContinue () 
	{
		global $wizardlib;

		// Run the parent first
		parent::onContinue();
		
		// Commit new preferences
		if ( isset( $_REQUEST['lm_preference'] ) ) {
			$prefslib = TikiLib::lib('prefs');
			$changes = $prefslib->applyChanges((array) $_REQUEST['lm_preference'], $_REQUEST);
		}
			
		$editorType = $_REQUEST['editorType'];

		$wizardlib->setupEditor($editorType);	
	}
}
