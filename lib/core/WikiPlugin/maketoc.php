<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class WikiPlugin_maketoc extends WikiPlugin_HtmlBase
{
	public $type = 'toc';
	public $documentation = 'PluginMaketoc';
	public $parserLevel = 1; //0 is standard, we put this after the others
	public $filter = 'rawhtml_unsafe';
	public $icon = 'img/icons/text_list_numbers.png';
	public $tags = array( 'basic' );

	function __construct()
	{
		if (empty(self::$name)) {
			self::$name = tr('Table of Contents (Page)');
			self::$description = tr('Add a table of contents to a page');
			self::$params = array(
				'type',
				'maxdepth',
				'title',
				'showhide',
				'nolinks',
				'nums',
				'levels'
			);
		}
		$this->parserLevel = 1;
	}

	function output(&$data, &$params, &$index, &$parser)
	{
		global $tikilib, $killtoc;

		if (isset($tikilib->is_slideshow) && $tikilib->is_slideshow == true) return '';
		if (isset($killtoc) && $killtoc == true) return '';

		$result = '<div id="toctitle"><h3>' .
			tr('Table of contents') .
		'</h3></div>' .
			$parser->header->toHtmlList('toc');

		return $result;
	}
}
