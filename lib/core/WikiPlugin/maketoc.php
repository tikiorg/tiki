<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class WikiPlugin_maketoc extends WikiPlugin_HtmlBase
{
	var $type = 'toc';
	var $validate = 'all';
	var $prefs = array('wikiplugin_maketoc');
	var $filter = 'rawhtml_unsafe';
	var $icon = 'img/icons/text_list_numbers.png';
	var $tags = array( 'basic' );
	var $parserLevel = 1; //0 is standard, we put this after the others

	function __constuct()
	{
		$this->name = tra('Table of contents for a page');
		$this->documentation = 'PluginMaketoc';
		$this->description = tra('Add a table of contents to a page');
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
		if (!empty($parser->headerStack)) {

			$result = '<div id="toctitle"><h3>' .
				tr('Table of contents') .
			'</h3></div>' .
			$this->toList($parser->headerStack, true);

			return $result;
		}
	}

	private function toList($array, $isBase = false) {
		$result = '';

		foreach($array as &$header){
			if(empty($header['content']) == false){
				$result .= '<li><a class="link" href="#' . $header['id'] . '">' . $header['content'] . '</a></li>';

				if(empty($header['children']) == false) {
					$result .= $this->toList($header['children']);
				}

				$result .= '</li>';

			} elseif (empty($header['children']) == false) {

				$result .= $this->toList($header['children']);
			}
		}

		return '<ul' . ($isBase == true ? ' class = "toc" ' : '') . '>' . $result . '</ul>';
	}
}