<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Formatter_Plugin_SmartyTemplate implements Search_Formatter_Plugin_Interface
{
	private $templateFile;
	private $changeDelimiters;
	private $data = array();
	private $fields = array();
	private $assigns = array();

	function __construct($templateFile, $changeDelimiters = false)
	{
		$this->templateFile = $templateFile;
		$this->changeDelimiters = (bool) $changeDelimiters;
	}

	function setData(array $data)
	{
		$this->data = $data;
	}

	/**
	 * Set a list of global var names to assign to smarty so they can be used in custom templates
	 *
	 * @param array $assigns
	 */
	function setAssigns(array $assigns)
	{
		$assigns2 = array();
		foreach ($assigns as $value) {
			if (!isset($GLOBALS[trim($value)])) {
				trigger_error('Global not found for assign var ' . $value);
			} else {
				$assigns2[] = trim($value);
			}
		}
		$this->assigns = $assigns2;
	}

	function getFields()
	{
		return $this->fields;
	}

	function setFields(array $fields)
	{
		$this->fields = $fields;
	}

	function getFormat()
	{
		return self::FORMAT_HTML;
	}

	function prepareEntry($entry)
	{
		return $entry->getPlainValues();
	}

	function renderEntries(Search_ResultSet $entries)
	{
		global $tikipath;
		$smarty = new Smarty;
		$smarty->setCompileDir($tikipath . 'templates_c');
		$smarty->setTemplateDir(null);
		$smarty->setTemplateDir(dirname($this->templateFile));
		$smarty->setPluginsDir(
			array(
				$tikipath . TIKI_SMARTY_DIR,	// the directory order must be like this to overload a plugin
				SMARTY_DIR . 'plugins',
			)
		);

		$secpol = new Tiki_Security_Policy($smarty);
		$secpol->secure_dir[] = dirname($this->templateFile);
		$smarty->enableSecurity($secpol);

		if ( $this->changeDelimiters ) {
			$smarty->left_delimiter = '{{';
			$smarty->right_delimiter = '}}';
		}

		foreach ($this->data as $key => $value) {
			$smarty->assign($key, $value);
		}

		foreach ($this->assigns as $value) {
			$smarty->assign($value, $GLOBALS[$value]);
		}

		$smarty->assign('results', $entries);
		$smarty->assign('count', count($entries));
		$smarty->assign('offset', $entries->getOffset());
		$smarty->assign('offsetplusone', $entries->getOffset() + 1);
		$smarty->assign('offsetplusmaxRecords', $entries->getOffset() + $entries->getMaxRecords());
		$smarty->assign('maxRecords', $entries->getMaxRecords());

		return $smarty->fetch($this->templateFile);
	}
}

