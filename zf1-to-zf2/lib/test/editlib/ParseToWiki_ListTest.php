<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * @group unit
 *
 */
class EditLib_ParseToWiki_ListTest extends TikiTestCase
{

	private $dir = '';  // the unmodifed directory
	private $el = null; // the EditLib


	function __construct()
	{
		$this->dir = getcwd();
	}


	function setUp()
	{
		TikiLib::lib('edit');
		$this->el = new EditLib();
		chdir($this->dir);
		chdir('../../'); // the tiki installation directory
	}


	function tearDown()
	{
		chdir($this->dir);
	}


	/**
	 * Test bullet lists
	 * 
	 * Test single lines with different numbers of <ul> 
	 */			
	function testBulletList()
	{

		/*
		 * *Item 1
		 * *Item 2 
		 */
		$ex = '*Item 1\n*Item 2\n';		
		$inData = "<ul><li>Item 1\n";
		$inData .= "</li><li>Item 2\n";
		$inData .= "</li></ul>\n";
		$out = $this->el->parseToWiki($inData);
		$out = preg_replace('/\n/', '\n', $out); // fix LF encoding for comparison		
		$this->assertEquals($ex, $out);			


		/*
		 * *Item 1
		 * **Item 1a
		 * *Item 2
		 */
		$ex = '*Item 1\n**Item 1a\n*Item 2\n';
		$inData = "<ul><li>Item 1\n";
		$inData .= "<ul><li>Item 1a\n";
		$inData .= "</li></ul></li><li>Item 2\n";
		$inData .= "</li></ul>\n";
		$out = $this->el->parseToWiki($inData);
		$out = preg_replace('/\n/', '\n', $out); // fix LF encoding for comparison		
		$this->assertEquals($ex, $out);
	}


	/**
	 * Test the continuation of bullet lists
	 * 
	 * Test level one and two
	 */	
	function testBulletListContinuation()
	{

		/*
		 * *Item 1
		 * +Continuation
		 * *Item 2
		 */
		$ex = '*Item 1\n+Continuation\n*Item 2\n';		
		$inData = "<ul><li>Item 1\n";
		$inData .= "<br />Continuation\n";
		$inData .= "</li><li>Item 2\n";
		$inData .= "</li></ul>\n";
		$out = $this->el->parseToWiki($inData);
		$out = preg_replace('/\n/', '\n', $out); // fix LF encoding for comparison		
		$this->assertEquals($ex, $out);			


		/*
		 * *Item A
		 * **Item 1
		 * ++Continuation
		 * **Item 2
		 */		
		$ex = '*Item A\n**Item 1\n++Continuation\n**Item 2\n';
		$inData = "<ul><li>Item A\n";		
		$inData .= "<ul><li>Item 1\n";
		$inData .= "<br />Continuation\n";
		$inData .= "</li><li>Item 2\n";
		$inData .= "</li></ul></li></ul>\n";
		$out = $this->el->parseToWiki($inData);
		$out = preg_replace('/\n/', '\n', $out); // fix LF encoding for comparison		
		$this->assertEquals($ex, $out);				
	}


	/**
	 * Test numbered lists
	 * 
	 * Test single lines with different numbers of <ol> 
	 */			
	function testNumberedList()
	{

		/*
		 * #Item 1
		 * #Item 2 
		 */
		$ex = '#Item 1\n#Item 2\n';		
		$inData = "<ol><li>Item 1\n";
		$inData .= "</li><li>Item 2\n";
		$inData .= "</li></ol>\n";
		$out = $this->el->parseToWiki($inData);
		$out = preg_replace('/\n/', '\n', $out); // fix LF encoding for comparison		
		$this->assertEquals($ex, $out);			


		/*
		 * #Item 1
		 * ##Item 1a
		 * #Item 2
		 */
		$ex = '#Item 1\n##Item 1a\n#Item 2\n';
		$inData = "<ol><li>Item 1\n";
		$inData .= "<ol><li>Item 1a\n";
		$inData .= "</li></ol></li><li>Item 2\n";
		$inData .= "</li></ol>\n";
		$out = $this->el->parseToWiki($inData);
		$out = preg_replace('/\n/', '\n', $out); // fix LF encoding for comparison		
		$this->assertEquals($ex, $out);							
	}


	/**
	 * Test the continuation of numbered lists
	 * 
	 * Test level one and two
	 */
	function testNumberedListContinuation()
	{

		/*
		 * #Item 1
		 * +Continuation
		 * #Item 2
		 */
		$ex = '#Item 1\n+Continuation\n#Item 2\n';		
		$inData = "<ol><li>Item 1\n";
		$inData .= "<br />Continuation\n";
		$inData .= "</li><li>Item 2\n";
		$inData .= "</li></ol>\n";
		$out = $this->el->parseToWiki($inData);
		$out = preg_replace('/\n/', '\n', $out); // fix LF encoding for comparison		
		$this->assertEquals($ex, $out);		


		/*
		 * #Item A
		 * ##Item 1
		 * ++Continuation
		 * ##Item 2
		 */		
		$ex = '#Item A\n##Item 1\n++Continuation\n##Item 2\n';
		$inData = "<ol><li>Item A\n";		
		$inData .= "<ol><li>Item 1\n";
		$inData .= "<br />Continuation\n";
		$inData .= "</li><li>Item 2\n";
		$inData .= "</li></ol></li></ol>\n";
		$out = $this->el->parseToWiki($inData);
		$out = preg_replace('/\n/', '\n', $out); // fix LF encoding for comparison		
		$this->assertEquals($ex, $out);		
	}

}
