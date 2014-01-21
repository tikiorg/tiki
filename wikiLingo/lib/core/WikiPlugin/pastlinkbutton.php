<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class WikiPlugin_pastlinkbutton extends WikiPlugin_HtmlBase
{
	public $type = 'pastlinkbutton';
	public $documentation = '';
	public $prefs = array('feature_wiki', 'wikiplugin_pastlink', 'feature_futurelinkprotocol');
	public $filter = 'rawhtml_unsafe';
	public $icon = 'img/icons/mime/html.png';
	public $tags = array( 'basic' );
	public $htmlTagType = 'span';
	public $htmlAttributes = array(
		"class" => "pastLinkCreationButton"
	);

	function __construct()
	{
		$this->name = tra('A PastLink Button');
		$this->description = tra('A PastLink Button');
		$this->body = tra('NA');
		$this->params = array();
	}

	function output(&$data, &$params, &$index, &$parser)
	{
		global $page;

		if (isset($page)) {
			return '<a href"#" onclick="return false;">' . tr('Create PastLink') . '</a>';
		}
	}
}
