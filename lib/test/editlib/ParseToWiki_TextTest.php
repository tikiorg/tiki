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

class EditLib_ParseToWiki_TextTest extends TikiTestCase {

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
	 * Align blocks
	 * 
	 * - left
	 * - center
	 * - right
	 * - justify
	 */
	function testBlockAlignement() {
		global $prefs;
		

		/*
		 * left
		 */
		$ex = 'This text is aligned left';
		
		$inData = 'This text is aligned left';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
				
		$ex = '{DIV(align="left")}This text is aligned left{DIV}';
		
		$inData = '<div style="text-align: left;">This text is aligned left</div>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
		$inData = '<div align="left">This text is aligned left</div>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);


		/*
		 * center
		 */
		$prefs['feature_use_three_colon_centertag'] = 'n';
		$ex = '::This text is centered::';
		
		$inData = '<div style="text-align: center;">This text is centered</div>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
		$inData = '<div align="center">This text is centered</div>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);

		$prefs['feature_use_three_colon_centertag'] = 'y';
		$ex = ':::This text is centered:::';
		
		$inData = '<div style="text-align: center;">This text is centered</div>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
		$inData = '<div align="center">This text is centered</div>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
		
		
		/*
		 * right
		 */
		$ex = '{DIV(align="right")}This text is aligned right{DIV}';

		$inData = '<div style="text-align: right;">This text is aligned right</div>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
		$inData = '<div align="right">This text is aligned right</div>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
		
		
		/*
		 * justify
		 */
		$ex = '{DIV(align="justify")}This text is justified{DIV}';
		
		$inData = '<div style="text-align: justify;">This text is justified</div>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
		$inData = '<div align="justify">This text is justified</div>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
	}
	
	
	/**
	 * Align paragraphs
	 * 
	 * - left
	 * - center
	 * - right
	 * - justify
	 */	
	function testParagraphAlignement() {
		
		$this->markTestIncomplete('Work in progress.');
		
		
		/*
		 * left
		 */
		$ex = '{DIV(type="p", align="left")}This text is aligned{DIV}';
		
		$inData = '<p style="text-align: left;">This text is aligned</p>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
		$inData = '<p align="left">This text is aligned</p>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
		
				
		/*
		 * center
		 */
		$ex = '{DIV(type="p", align="center")}This text is aligned{DIV}';
		
		$inData = '<p style="text-align: center;">This text is aligned</p>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
		$inData = '<p align="center">This text is aligned</p>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
		
		
		/*
		 * right
		 */
		$ex = '{DIV(type="p", align="right")}This text is aligned{DIV}';
		
		$inData = '<p style="text-align: right;">This text is aligned</p>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
		$inData = '<p align="right">This text is aligned</p>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
		
		
		/*
		 * justify
		 */
		$ex = '{DIV(type="p", align="justify")}This text is aligned{DIV}';
		
		$inData = '<p style="text-align: justify;">This text is aligned</p>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
		$inData = '<p align="justify">This text is aligned</p>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
	}
}