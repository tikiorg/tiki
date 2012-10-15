<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class WikiPlugin_forwardlinkbutton extends WikiPlugin_HtmlBase
{
	public $type = 'forwardlinkbutton';
	public $documentation = '';
	public $prefs = array('feature_wiki', 'wikiplugin_textlink', 'feature_forwardlinkprotocol');
	public $filter = 'rawhtml_unsafe';
	public $icon = 'img/icons/mime/html.png';
	public $tags = array( 'basic' );
	public $htmlTagType = 'span';
	public $htmlAttributes = array(
		"class" => "forwardLinkCreationButton"
	);

	function __construct()
	{
		$this->name = tra('A ForwardLink Button');
		$this->description = tra('A ForwardLink Button');
		$this->body = tra('NA');
		$this->params = array(
		);
	}

	function output(&$data, &$params, &$index, &$parser)
	{
		global $page;

		if (isset($page)) {
			return '<a href"#" onclick="return false;">' . tr('Create ForwardLink') . '</a>';
		}
	}
}
