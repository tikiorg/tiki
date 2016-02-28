<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class WikiParser_PluginParser
{
	private $argumentParser;
	private $pluginRunner;

	function parse( $text )
	{
		if ( ! $this->argumentParser || ! $this->pluginRunner )
			return $text;
	}

	function setArgumentParser( /* WikiParser_PluginArgumentParser */ $parser )
	{
		$this->argumentParser = $parser;
	}

	function setPluginRunner( /* WikiParser_PluginRunner */ $runner )
	{
		$this->pluginRunner = $runner;
	}
}
