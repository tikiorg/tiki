<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class JisonParser_Wiki_PluginAjaxHandler extends JisonParser_Wiki_Handler
{
	static public $pluginCount = 0;
	static public $plugins = array();
	static public $pluginPreKeys = array();

	var $parsePlugins = false;
	var $parseNps = true;
	var $parseLists = false;

	var $inUse = false;

	function parse($data)
	{

		if ($this->inUse) {
			$parser = new self();
			return $parser->parse($data);
		}

		$this->inUse = true;
		$result = parent::parse($data);
		$this->inUse = false;
		return $result;
	}

	function plugin($pluginDetails)
	{
		$key = '§' . md5('plugin:'.self::$pluginCount) . '§';
		self::$pluginCount++;
		self::$plugins[$key] = $this->unprotectSpecialChars($pluginDetails['body'], true);

		return $this->parse( $pluginDetails['body'] );
	}

	function newLine($content)
	{
		return $content;
	}
	//end state handlers
	//Wiki Syntax Objects Parsing Start
	function bold($content)
	{
		return $content;
	}

	function box($content)
	{
		return $content;
	}

	function center($content)
	{
		return $content;
	}

	function colortext($content)
	{
		return $content;
	}

	function content($content)
	{
		return $content;
	}

	function italics($content)
	{
		return $content;
	}

	function header($content)
	{
		return $content;
	}

	function hr($content)
	{
		return $content;
	}

	function link($content)
	{
		return $content;
	}

	function smile($smile)
	{
		return $smile;
	}

	function strikethrough($content)
	{
		return $content;
	}

	function tableParser($content)
	{
		return $content;
	}


	function titlebar($content)
	{
		return $content;
	}

	function underscore($content)
	{
		return $content;
	}

	function wikilink($content)
	{
		return $content;
	}

	function html($content)
	{
		return $content;
	}

	function formatContent($content)
	{
		return $content;
	}
}