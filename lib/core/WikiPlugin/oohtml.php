<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class WikiPlugin_oohtml extends WikiPlugin_HtmlBase
{
	var $type = 'oohtml';
	var $validate = 'all';
	var $prefs = array('wikiplugin_html');
	var $filter = 'rawhtml_unsafe';
	var $icon = 'img/icons/mime/html.png';
	var $tags = array( 'basic' );

	function __construct()
	{
		$this->name = tra('Object oriented version of the html wiki plugin');
		$this->documentation = 'PluginHTML';
		$this->description = tra('Add HTML to a page');
		$this->body = tra('HTML code');
		$this->params = array(
			'wiki' => array(
				'required' => false,
				'name' => tra('Wiki Syntax'),
				'description' => tra('Parse wiki syntax within the HTML code.'),
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('No'), 'value' => 0),
					array('text' => tra('Yes'), 'value' => 1),
				),
				'filter' => 'int',
				'default' => '0',
			),
		);
	}

	function output(&$data, &$params, &$index, &$parser)
	{
		// parse using is_html if wiki param set, or just decode html entities
		if ( isset($params['wiki']) && $params['wiki'] === 1 ) {
			return  TikiLib::lib('tiki')->parse_data($data, array('is_html' => true));
		} else {
			return html_entity_decode($data, ENT_NOQUOTES, 'UTF-8');
		}
	}
}