<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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

	function __construct($templateFile, $changeDelimiters = false)
	{
		$this->templateFile = $templateFile;
		$this->changeDelimiters = (bool) $changeDelimiters;
	}

	function setData(array $data)
	{
		$this->data = $data;
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
		$smarty = new Smarty_Tiki;

		if ( $this->changeDelimiters ) {
			$smarty->left_delimiter = '{{';
			$smarty->right_delimiter = '}}';
		}

		foreach ($this->data as $key => $value) {
			$smarty->assign($key, $value);
		}

		$smarty->assign('prefs', $GLOBALS['prefs']);
		$smarty->assign('user', $GLOBALS['user']);
		$smarty->assign('results', $entries);
		$smarty->assign(
			'facets',
			array_map(
				function ($facet) {
					return array(
						'name' => $facet->getName(),
						'label' => $facet->getLabel(),
						'options' => $facet->getOptions(),
						'operator' => $facet->getOperator(),
					);
				},
				$entries->getFacets()
			)
		);
		$smarty->assign('count', count($entries));
		$smarty->assign('offset', $entries->getOffset());
		$smarty->assign('offsetplusone', $entries->getOffset() + 1);
		$smarty->assign('offsetplusmaxRecords', $entries->getOffset() + $entries->getMaxRecords());
		$smarty->assign('maxRecords', $entries->getMaxRecords());
		$smarty->assign('id', $entries->getId());
		$smarty->assign('tsOn', $entries->getTsOn());
		$tsettings = $entries->getTsSettings();
		if (is_array($tsettings)) {
			$smarty->assign('tstotals', $tsettings['math']['totals']);
			$smarty->assign('tscols', $tsettings['columns']);
		}

		return $smarty->fetch($this->templateFile);
	}
}

