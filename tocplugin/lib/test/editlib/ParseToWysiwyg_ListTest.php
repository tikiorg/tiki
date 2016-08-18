<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * @group unit
 *
 */
class EditLib_ParseToWysiwyg_ListTest extends TikiTestCase
{

	private $el = null; // the EditLib
	private $dir = '';  // the unmodifed directory

	function __construct()
	{
		$this->dir = getcwd();
	}


	function setUp()
	{

		TikiLib::lib('edit');
		$_SERVER['HTTP_HOST'] = ''; // editlib expects that HTTP_HOST is defined

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
	 * Test single lines with different numbers of '*' 
	 */		
	function testBulletList()
	{

        $this->markTestSkipped("As of 2013-10-02, this test is broken, and nobody knows how to fix it. Mark as Skipped for now.");
		/*
		 * *Item 1
		 * *Item 2 
		 */
		$inData = "*Item 1\n*Item 2\n";		
		$ex = '<ul><li>Item 1\n';
		$ex .= '</li><li>Item 2\n';
		$ex .= '</li></ul><br />\n';
		$out = $this->el->parseToWysiwyg($inData);
		$out = preg_replace('/\n/', '\n', $out); // fix LF encoding for comparison		
		$this->assertEquals($ex, $out);			


		/*
		 * *Item 1
		 * **Item 1a
		 * *Item 2
		 */
		$inData = "*Item 1\n**Item 1a\n*Item 2\n";
		$ex = '<ul><li>Item 1\n';
		$ex .= '<ul><li>Item 1a\n';
		$ex .= '</li></ul></li><li>Item 2\n';
		$ex .= '</li></ul><br />\n';
		$out = $this->el->parseToWysiwyg($inData);
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

        $this->markTestSkipped("As of 2013-10-02, this test is broken, and nobody knows how to fix it. Mark as Skipped for now.");

		/*
		 * *Item 1
		 * +Continuation
		 * *Item 2
		 */
		$inData = "*Item 1\n+Continuation\n*Item 2\n";		
		$ex = '<ul><li>Item 1\n';
		$ex .= '<br />Continuation\n';
		$ex .= '</li><li>Item 2\n';
		$ex .= '</li></ul><br />\n';
		$out = $this->el->parseToWysiwyg($inData);
		$out = preg_replace('/\n/', '\n', $out); // fix LF encoding for comparison		
		$this->assertEquals($ex, $out);			


		/*
		 * **Item 1
		 * ++Continuation
		 * **Item 2
		 */		
		$inData = "**Item 1\n++Continuation\n**Item 2\n";		
		$ex = '<ul><ul><li>Item 1\n';
		$ex .= '<br />Continuation\n';
		$ex .= '</li><li>Item 2\n';
		$ex .= '</li></ul></ul><br />\n';
		$out = $this->el->parseToWysiwyg($inData);
		$out = preg_replace('/\n/', '\n', $out); // fix LF encoding for comparison		
		$this->assertEquals($ex, $out);					
	}


	/**
	 * Test numbered lists
	 * 
	 * Test single lines with different numbers of '#' 
	 */			
	function testNumberedList()
	{

        $this->markTestSkipped("As of 2013-10-02, this test is broken, and nobody knows how to fix it. Mark as Skipped for now.");
		/*
		 * #Item 1
		 * #Item 2 
		 */
		$inData = "#Item 1\n#Item 2\n";		
		$ex = '<ol><li>Item 1\n';
		$ex .= '</li><li>Item 2\n';
		$ex .= '</li></ol><br />\n';
		$out = $this->el->parseToWysiwyg($inData);
		$out = preg_replace('/\n/', '\n', $out); // fix LF encoding for comparison		
		$this->assertEquals($ex, $out);			


		/*
		 * #Item 1
		 * ##Item 1a
		 * #Item 2
		 */
		$inData = "#Item 1\n##Item 1a\n#Item 2\n";
		$ex = '<ol><li>Item 1\n';
		$ex .= '<ol><li>Item 1a\n';
		$ex .= '</li></ol></li><li>Item 2\n';
		$ex .= '</li></ol><br />\n';
		$out = $this->el->parseToWysiwyg($inData);
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

        $this->markTestSkipped("As of 2013-10-02, this test is broken, and nobody knows how to fix it. Mark as Skipped for now.");

		/*
		 * #Item 1
		 * +Continuation
		 * #Item 2
		 */
		$inData = "#Item 1\n+Continuation\n#Item 2\n";		
		$ex = '<ol><li>Item 1\n';
		$ex .= '<br />Continuation\n';
		$ex .= '</li><li>Item 2\n';
		$ex .= '</li></ol><br />\n';
		$out = $this->el->parseToWysiwyg($inData);
		$out = preg_replace('/\n/', '\n', $out); // fix LF encoding for comparison		
		$this->assertEquals($ex, $out);		


		/*
		 * ##Item 1
		 * ++Continuation
		 * ##Item 2
		 */	
		$inData = "##Item 1\n++Continuation\n##Item 2\n";		
		$ex = '<ol><ol><li>Item 1\n';
		$ex .= '<br />Continuation\n';
		$ex .= '</li><li>Item 2\n';
		$ex .= '</li></ol></ol><br />\n';
		$out = $this->el->parseToWysiwyg($inData);
		$out = preg_replace('/\n/', '\n', $out); // fix LF encoding for comparison		
		$this->assertEquals($ex, $out);					
	}

}

