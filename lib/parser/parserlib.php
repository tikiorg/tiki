<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Parser Library
 *
 * \wiki syntax parser for tiki
 *
 * @package		Tiki
 * @subpackage		Parser
 * @author		Robert Plummer
 * @copyright		Copyright (c) 2002-2011, All Rights Reserved.
 * 			See copyright.txt for details and a complete list of authors.
 * @license		LGPL - See license.txt for details.
 * @version		SVN $Rev$
 * @filesource
 * @link		http://dev.tiki.org/Parser
 * @since		8
 */

 class ParserLib extends TikiLib
{
	var $parser;
	function ParserLib() {
		include_once "WikiParser.php";
		$this->parser = new WikiParser;
		$this->parser->plugin = $this->plugin;
	}
	
	function plugin($pluginDetails) {
	
	
	}
	
	function parse($wikiSyntax) {
		return $this->parser->parse($wikiSyntax);
	}
}

$parserlib = new ParserLib;