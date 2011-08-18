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

class EditLib_ParseToWysiwyg_TextTest extends TikiTestCase 
{
	
private $dir = '';  // the unmodifed directory
	private $el = null; // the EditLib
	
	
	function __construct() {
		$this->dir = getcwd();
	}
		
	
	function setUp() {
		$this->el = new EditLib();
		chdir($this->dir);
		chdir('../../'); // the tiki installation directory
	}
	
		
	function tearDown() {
		chdir($this->dir);
	}	
	
	
	/**
	 * Align divs 'left'
	 */
	function testBlockAlignLeft() {
		global $prefs;
		
		$this->markTestIncomplete('Work in progress.');

		$ex = 'This text is aligned left';
		
		
		/*
		 * default
		 */
		$inData = 'This text is aligned left';
		$out = $this->el->parseToWysiwyg($inData, true);
		$this->assertEquals($ex, $out);
		
		
		/*
		 * explicit
		 */
		$ex = '<div style="text-align: left;">This text is aligned left</div>';
		$inData = '{DIV(align="left")}This text is aligned left{DIV}';
		$out = $this->el->parseToWysiwyg($inData, true);
		$this->assertEquals($ex, $out);
	}
	
	
	/**
	 * Align divs 'center'
	 */
	function testBlockAlignCentered() {
		global $prefs;
		
		$this->markTestIncomplete('Work in progress.');

		
		/*
		 * two colon
		 */
		$prefs['feature_use_three_colon_centertag'] = 'n';
		$ex = '<div style="text-align: center;">This text is centered</div>';
		$inData = '::This text is centered::';
		$out = $this->el->parseToWysiwyg($inData, true);
		$this->assertEquals($ex, $out);

		
		/*
		 * three colon
		 */
		$prefs['feature_use_three_colon_centertag'] = 'y';
		$ex = '<div style="text-align: center;">This text is centered</div>';
		$inData = ':::This text is centered:::';
		$out = $this->el->parseToWysiwyg($inData, true);
		$this->assertEquals($ex, $out);
	}
	
	
	/**
	 * Align divs 'right'
	 */
	function testBlockAlignRight() {
		global $prefs;
		
		$this->markTestIncomplete('Work in progress.');

		$ex = '<div style="text-align: right;">This text is aligned right</div>';
		$inData = '{DIV(align="right")}This text is aligned right{DIV}';
		$out = $this->el->parseToWysiwyg($inData, true);
		$this->assertEquals($ex, $out);
	}
	
	
	/**
	 * Align divs 'justify'
	 */
	function testBlockAlignJustified() {
		global $prefs;
		
		$this->markTestIncomplete('Work in progress.');

		$ex = '<div style="text-align: justify;">This text is justified</div>';
		$inData = '{DIV(align="justify")}This text is justified{DIV}';
		$out = $this->el->parseToWysiwyg($inData, true);
		$this->assertEquals($ex, $out);
	}
		
	
	/**
	 * Align paragraphs 'left'
	 */	
	function testParagraphAlignLeft() {
		
		$this->markTestIncomplete('Work in progress.');
		
		$ex = '<p style="text-align: left;">This text is aligned</p>';
		$inData = '{DIV(type="p", align="left")}This text is aligned{DIV}';
		$out = $this->el->parseToWysiwyg($inData, true);
		$this->assertEquals($ex, $out);
	}	
	
	
	/**
	 * Align paragraphs 'center'
	 */	
	function testParagraphAlignCentered() {
		
		$this->markTestIncomplete('Work in progress.');
		
		$ex = '<p style="text-align: center;">This text is aligned</p>';
		$inData = '{DIV(type="p", align="center")}This text is aligned{DIV}';
		$out = $this->el->parseToWysiwyg($inData, true);
		$this->assertEquals($ex, $out);
	}		
	
	
	/**
	 * Align paragraphs 'right'
	 */	
	function testParagraphAlignRight() {
		
		$this->markTestIncomplete('Work in progress.');
		
		$ex = '<p style="text-align: right;">This text is aligned</p>';
		$inData = '{DIV(type="p", align="right")}This text is aligned{DIV}';
		$out = $this->el->parseToWysiwyg($inData, true);
		$this->assertEquals($ex, $out);
	}	

	
	/**
	 * Align paragraphs 'justify'
	 */	
	function testParagraphAlignJustified() {
		
		$this->markTestIncomplete('Work in progress.');
		
		$ex = '<p style="text-align: justify;">This text is aligned</p>';
		$inData = '{DIV(type="p", align="justify")}This text is aligned{DIV}';
		$out = $this->el->parseToWysiwyg($inData, true);
		$this->assertEquals($ex, $out);
	}		
}
