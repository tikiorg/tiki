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

class EditLib_ParseToWysiwyg_CharacterTest extends TikiTestCase
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


	function testFontFamily()
	{
		$this->markTestIncomplete('Work in progress.');
		
		$el = new Editlib();
		
		$inData = '{FONT(type="span", font-family="tahoma")}text{FONT}';
		$exp = '<span style="font-family:tahoma;">text<span>';
		$out = $el->parseToWysiwyg($inData);
		$this->assertContains($exp, $out);
	}

	
	function testFontSize()
	{
		$this->markTestIncomplete('Work in progress.');
		
		$el = new Editlib();
		
		$inData = '{FONT(type="span", font-size="12px")}text{FONT}';
		$exp = '<span style="font-size:12px;">text<span>';
		$out = $el->parseToWysiwyg($inData);
		$this->assertContains($exp, $out);
	}
	
	
	function testBold()
	{
		$inData = '__bold__';
		$exp = '<strong>bold</strong>'; // like CKE
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertContains($exp, $out);		
	}
	
	
	function testItalic()
	{
		$inData = '\'\'italic\'\'';
		$exp = '<em>italic</em>'; // like CKE
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertContains($exp, $out);
	}
	
	
	function testUnderlined()
	{
		$inData = '===underlined===';
		$exp = '<u>underlined</u>'; // like CKE
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertContains($exp, $out);
	}
	
	
	function testStrike()
	{
		$inData = '--strike through--';
		$exp = '<strike>strike through</strike>'; // like CKE
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertContains($exp, $out);
	}
	
	
	function testSubscript()
	{
		$this->markTestIncomplete('Work in progress.');
		$inData = '{SUB()}subscript{SUB}';
		$exp = '<sub>subscript</sub>';
		$out = $this->el->parseToWysiwyg($inData);
		$this->assertContains($exp, $out);
	}	

	
	function testSuperscript()
	{
		$this->markTestIncomplete('Work in progress.');
		
		$el = new EditLib();
		
		$inData = '{SUP()}superscript{SUP}';
		$exp = '<sup>superscript</sup>';
		$out = $el->parseToWysiwyg($inData);
		$this->assertContains($exp, $out);
	}		
	
	
	function testMonospaced()
	{
		$this->markTestIncomplete('Work in progress.');
		
		$el = new EditLib();
		
		$inData = '-+monospaced+-';
		$exp = '<code>monospaced</code>';
		$out = $el->parseToWysiwyg($inData);
		$this->assertContains($exp, $out);
	}

	
	function testTeletype()
	{
		$this->markTestIncomplete('Work in progress.');
		
		$el = new EditLib();
		
		$inData = '{DIV(type="tt")}teletype{DIV}';
		$exp = '<tt>teletype</tt>';
		$out = $el->parseToWysiwyg($inData);
		$this->assertContains($exp, $out);
	}
	
	
	function testColor()
	{
		$inData = '~~#112233:text~~';
		$exp = '<span style="color:#112233; background-color:">text</span>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertContains($exp, $out);			
				
		$inData = '~~ ,#112233:text~~';
		$exp = '<span style="color: ; background-color:#112233">text</span>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertContains($exp, $out);			
				
		$inData = '~~#AABBCC,#112233:text~~';
		$exp = '<span style="color:#AABBCC; background-color:#112233">text</span>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertContains($exp, $out);			
	}
}
