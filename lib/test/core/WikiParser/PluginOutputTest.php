<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class WikiParser_PluginOutputTest extends PHPUnit_Framework_TestCase
{
	function testWikiToWikiOutput()
	{
		$output = WikiParser_PluginOutput::wiki('^Hello world!^');

		$this->assertEquals('^Hello world!^', $output->toWiki());
	}

	function testWikiToHtmlOutput()
	{
		$output = WikiParser_PluginOutput::wiki('^Hello world!^');

		$this->assertContains('<div class="well">Hello world!</div>', $output->toHtml());
	}

	function testHtmlToWikiOutput()
	{
		$output = WikiParser_PluginOutput::html('<div>Hello</div>');

		$this->assertEquals('~np~<div>Hello</div>~/np~', $output->toWiki());
	}

	function testHtmlToHtmlOutput()
	{
		$output = WikiParser_PluginOutput::html('<div>Hello</div>');

		$this->assertEquals('<div>Hello</div>', $output->toHtml());
	}

	function testInternalError()
	{
		$output = WikiParser_PluginOutput::internalError(tra('Unknown conversion'));
		
		$this->assertContains('Unknown conversion', $output->toHtml());
	}

	function testMissingArguments()
	{
		$output = WikiParser_PluginOutput::argumentError(array('id', 'test'));

		$this->assertContains('<li>id</li>', $output->toHtml());
	}
}

