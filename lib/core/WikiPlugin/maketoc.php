<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class WikiPlugin_maketoc extends WikiPlugin_HtmlBase
{
	var $type = 'toc';
	var $validate;
	var $filter = 'rawhtml_unsafe';
	var $icon = 'img/icons/text_list_numbers.png';
	var $tags = array( 'basic' );
	var $parserLevel = 1; //0 is standard, we put this after the others

	function __construct()
	{
		$this->name = tr('Table of contents for a page');
		$this->documentation = 'PluginMaketoc';
		$this->description = tr('Add a table of contents to a page');
		$this->params = array(
			'type',
			'maxdepth',
			'title',
			'showhide',
			'nolinks',
			'nums',
			'levels'
		);
	}

	function output($data, $params, $index, $parser)
	{
		$result = '<div id="toctitle"><h3>' .
			tr('Table of contents') .
		'</h3></div>' .
			$parser->header->toHtmlList('toc');

		return $result;
	}
}