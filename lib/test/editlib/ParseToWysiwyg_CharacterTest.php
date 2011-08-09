<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * @group unit
 *
 */

require_once 'lib/wiki/editlib.php';

class EditLib_ParseToWysiwyg_CharacterTest extends TikiTestCase {
	
	private $dir = '';  // the unmodifed directory
	
	function __construct() {
		$this->dir = getcwd();
	}
		
	
	function setUp() {
		
		$_SERVER['HTTP_HOST'] = ''; // editlib expects that HTTP_HOST is defined
				
		chdir($this->dir);
		chdir('../../'); // the tiki installation directory
	}
	
		
	function tearDown() {
		chdir($this->dir);
	}	


	function testFontFamily() {
		$this->markTestIncomplete('Work in progress.');
		
		$el = new Editlib();
		
		$inData = '{FONT(type="span", font-family="tahoma")}text{FONT}';
		$exp = '<span style="font-family:tahoma;">text<span>';
		$out = $el->parseToWysiwyg($inData, false);
		$this->assertEquals($exp, $out);
	}

	
	function testFontSize() {
		$this->markTestIncomplete('Work in progress.');
		
		$el = new Editlib();
		
		$inData = '{FONT(type="span", font-size="12px")}text{FONT}';
		$exp = '<span style="font-size:12px;">text<span>';
		$out = $el->parseToWysiwyg($inData, false);
		$this->assertEquals($exp, $out);
	}
	
	
	function testBold() {
		$this->markTestIncomplete('Work in progress.');
		
		$el = new Editlib();
		
		$inData = '__bold__';
		$exp = '<strong>bold</strong>'; // like CKE
		$out = $el->parseToWysiwyg($inData, false);
		$this->assertEquals($exp, $out);
	}
	
	
	function testItalic() {
		$this->markTestIncomplete('Work in progress.');
		
		$el = new EditLib();
		
		$inData = '\'\'italic\'\'';
		$exp = '<em>italic</em>'; // like CKE
		$out = $el->parseToWysiwyg($inData, false);
		$this->assertEquals($exp, $out);
	}
	
	
	function testUnderlined() {
		$this->markTestIncomplete('Work in progress.');
		
		$el = new EditLib();
		
		$inData = '===underlined===';
		$exp = '<u>underlined</u>'; // like CKE
		$out = $el->parseToWysiwyg($inData, false);
		$this->assertEquals($exp, $out);
	}
	
	
	function testStrike() {
		$this->markTestIncomplete('Work in progress.');
		
		$el = new EditLib();
		
		$inData = '--strike through--';
		$exp = '<strike>strike through</strike>'; // like CKE
		$out = $el->parseToWysiwyg($inData, false);
		$this->assertEquals($exp, $out);
	}
	
	
	function testSubscript() {
		$this->markTestIncomplete('Work in progress.');
		
		$el = new EditLib();
		
		$inData = '{SUB()}subscript{SUB}';
		$exp = '<sub>subscript</sub>';
		$out = $el->parseToWysiwyg($inData, false);
		$this->assertEquals($exp, $out);
	}	

	
	function testSuperscript() {
		$this->markTestIncomplete('Work in progress.');
		
		$el = new EditLib();
		
		$inData = '{SUP()}superscript{SUP}';
		$exp = '<sup>superscript</sup>';
		$out = $el->parseToWysiwyg($inData, false);
		$this->assertEquals($exp, $out);
	}		
	
	
	function testMonospaced() {
		$this->markTestIncomplete('Work in progress.');
		
		$el = new EditLib();
		
		$inData = '<code>monospaced</code>';
		$exp = '-+monospaced+-';
		$out = $el->parseToWysiwyg($inData, false);
		$this->assertEquals($exp, $out);
	}

	
	function testTeletype() {
		$this->markTestIncomplete('Work in progress.');
		
		$el = new EditLib();
		
		$inData = '{DIV(type="tt")}teletype{DIV}';
		$exp = '<tt>teletype</tt>';
		$out = $el->parseToWysiwyg($inData, false);
		$this->assertEquals($exp, $out);
	}
	
	
	function testColor() {
		$this->markTestIncomplete('Work in progress.');
		
		$el = new EditLib();
		
		$inData = '~~#112233:text~~';
		$exp = '<span style="color:#112233;">text</span>';
		$out = $el->parseToWysiwyg($inData, false);
		$this->assertEquals($exp, $out);			
				
		$inData = '~~ ,#112233:text~~';
		$exp = '<span style="background-color:#112233;">text</span>';
		$out = $el->parseToWysiwyg($inData, false);
		$this->assertEquals($exp, $out);			
				
		$inData = '~~#AABBCC,#112233:text~~';
		$exp = '<span style="color:#AABBCC; background-color=#112233;">text</span>';
		$out = $el->parseToWysiwyg($inData, false);
		$this->assertEquals($exp, $out);			
				
		$inData = '~~ #AABBCC , #112233 :text~~';
		$exp = '<span style="color:#AABBCC; background-color=#112233;">text</span>';
		$out = $el->parseToWysiwyg($inData, false);
		$this->assertEquals($exp, $out);
	}
}
