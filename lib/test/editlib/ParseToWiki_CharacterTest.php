<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: ParserTest.php 33195 2011-03-02 17:43:40Z changi67 $

require_once 'lib/wiki/editlib.php';

class EditLib_ParseToWiki_CharacterTest extends TikiTestCase {
	
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
	 * Font family
	 * 
	 * => {FONT(type="span", font-family="tahoma")}text{FONT}
	 * - 'font-family'
	 */
	function testFont() {
		
		$ex = '{FONT(type="span", font-family="tahoma")}text{FONT}';
		
		$inData = '<span style="font-family:tahoma;">text<span>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
	}
	
	
	/**
	 * Size
	 * 
	 * => {FONT(type="span", font-size="tahoma")}text{FONT}
	 * 'font-size'
	 * 
	 */
	function testSize() {

		
		/*
		 * px
		 */
		$ex = '{FONT(type="span", font-size="12px")}text{FONT}';
		
		$inData = '<span style="font-size:12px;">text<span>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);

		
		/*
		 * pt
		 */
		$ex = '{FONT(type="span", font-size="12pt")}text{FONT}';
		
		$inData = '<span style="font-size:12pt;">text<span>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
		
		
		/*
		 * em
		 */
		$ex = '{FONT(type="span", font-size="1.2em")}text{FONT}';
		
		$inData = '<span style="font-size:1.2em;">text<span>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
	}
		
	
	/**
	 * Bold
	 * 
	 * => __
	 * - <b>
	 * - <strong>
	 * - 'font-weight:bold'
	 */
	function testBold() {
		
		$ex = '__bold__';
		
		$inData = '<b>bold</b>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
		
		$inData = '<strong>bold</strong>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
		
		$inData = '<span style="font-weight:bold;">bold</span>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
	}
		
	
	/**
	 * Italic
	 * 
	 * => ''
	 * - <em>
	 * - <i>
	 * - 'font-style:italic'
	 */
	function testItalic() {
		
		$ex = '\'\'italic\'\'';
		
		$inData = '<em>italic</em>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
		
		$inData = '<i>italic</i>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
		
		$inData = '<span style="font-style:italic;">italic</span>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);		
	}
	
	
	/**
	 * Underlined
	 * 
	 * => ===
	 * - <u>
	 * - 'text-decoration:underline'
	 */
	function testUnderlined() {
		
		$ex = '===underlined===';
		
		$inData = '<u>underlined</u>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
		
		$inData = '<span style="text-decoration:underline;">underlined</span>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);			
	}
	
	
	/**
	 * Strikethrough
	 * 
	 * => --
	 * - <strike>
	 * - <del>
	 * - <s>
	 * - 'text-decoration:line-through'
	 */
	function testStrikethrough() {
		
		$ex = '--strikethrough--';
		
		$inData = '<strike>strikethrough</strike>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);

		$inData = '<del>strikethrough</del>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
		
		$inData = '<s>strikethrough</s>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
		
		$inData = '<span style="text-decoration:underline;">strikethrough</span>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);				
	}
	
	
	
	/**
	 * Subscript
	 * 
	 * => {SUB()}
	 * - <sub>
	 */
	function testSubscript() {
		
		$ex = '{SUB()}subscript{SUB}';
		
		$inData = '<sub>subscript</sub>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);		
	}

	
	/**
	 * Superscript
	 * 
	 * => {SUP()}
	 * - <sup>
	 */
	function testSuperscript() {
		
		$ex = '{SUP()}subscript{SUP}';
		
		$inData = '<sup>subscript</sup>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);		
	}	
	
	
	/**
	 * Monospaced
	 * 
	 * => -+
	 * - <code>
	 */
	function testMonospace() {

		$ex = '-+monospaced+-';
		
		$inData = '<code>monospaced</code>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);		
	}

		
	/**
	 * Teletype
	 * 
	 * => {DIV(type="tt")}
	 * - <tt>
	 */
	function testTeletype() {
		
		$ex = '{DIV(type="tt")}typewriter{DIV}';
		
		$inData = '<tt>typewriter</tt>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);			
	}
	
	
	/**
	 * Text and background color
	 * 
	 * => ~~
	 * - 'background'
	 * - 'background-color' 
	 */
	function testColor() {
		
		/*
		 * text only
		 */
		$ex = '~~#FF0000:color~~';
		
		$inData = '<span style="color:#FF0000;">color</span>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);			
		
		
		/*
		 * background only
		 */
		$ex = '~~ ,#FFFF00:color~~';
		
		$inData = '<span style="backround:#FFFF00;">color</span>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);			
		
		$inData = '<span style="backround-color:#FFFF00;">color</span>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);			
		
		
		/*
		 * text and background
		 */
		$ex = '~~#FF0000,#0000FF:color~~';

		$inData = '<span style="color:rgb(255, 0, 0);backround-color:rgb(0, 0, 255);">color</span>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);			
				
		$inData = '<span style="color:#FF0000;backround-color:#0000FF:color;">color</span>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);			
		
		$inData = '<span style="color:#FF0000;backround:#0000FF:color;">color</span>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);			
		
		$inData = '<span style="backround-color:#0000FF:color;color:#FF0000;">color</span>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);			
		
		$inData = '<span style="backround:#0000FF:color;color:#FF0000;">color</span>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);			
	}
	
}