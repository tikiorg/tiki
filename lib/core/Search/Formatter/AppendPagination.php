<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Formatter_AppendPagination implements Search_Formatter_Plugin_Interface
{
	private $parent;
	private $arguments;

	function __construct(Search_Formatter_Plugin_Interface $parent, array $arguments = array())
	{ 
		$this->parent = $parent;
		$this->arguments = $arguments;
	}

	function getFields()
	{
		return $this->parent->getFields();
	}

	function getFormat()
	{
		return $this->parent->getFormat();
	}

	function prepareEntry($entry)
	{
		return $this->parent->prepareEntry($entry);
	}

	function renderEntries(Search_ResultSet $entries)
	{
		if ($entries->getTsOn()) {
			return $this->parent->renderEntries($entries);
		}
		$smarty = TikiLib::lib('smarty');
		$smarty->loadPlugin('smarty_block_pagination_links');
		$arguments = $this->arguments;
		$arguments['resultset'] = $entries;
		$tmp = false;
		$pagination = smarty_block_pagination_links($arguments, '', $smarty, $tmp);

		if ($this->getFormat() == Search_Formatter_Plugin_Interface::FORMAT_WIKI) {
			$pagination = "~np~$pagination~/np~";
		}
		return $this->parent->renderEntries($entries) . $pagination;
	}
}

