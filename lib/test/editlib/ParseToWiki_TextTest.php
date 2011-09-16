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

class EditLib_ParseToWiki_TextTest extends TikiTestCase
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
	 * Align Divs 'left'
	 */
	function testBlockAlignLeft() {
		global $prefs;

		/*
		 * default
		 */
		$ex = 'This text is aligned left';
		
		$inData = 'This text is aligned left';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
				

		/*
		 * explicit
		 */
		$ex = '{DIV(align="left")}This text is aligned left{DIV}';
		
		$inData = '<div style="text-align: left;">This text is aligned left</div>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);

		$inData = '<div align="left">This text is aligned left</div>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
	}
	
	
	/**
	 * Align Divs 'center'
	 */
	function testBlockAlignCentered() {
		global $prefs;

		/*
		 * two colon, no line break
		 */
		$prefs['feature_use_three_colon_centertag'] = 'n';
		$ex = '::This text is centered::';
		
		$inData = '<div style="text-align: center;">This text is centered</div>';
		$out = trim( $this->el->parseToWiki($inData) );
		$this->assertEquals($ex, $out);
		
		$inData = '<div align="center">This text is centered</div>';
		$out = trim( $this->el->parseToWiki($inData) );
		$this->assertEquals($ex, $out);

		
		/*
		 * three colon, no line break
		 */
		$prefs['feature_use_three_colon_centertag'] = 'y';
		$ex = ':::This text is centered:::';
		
		$inData = '<div style="text-align: center;">This text is centered</div>';
		$out = trim( $this->el->parseToWiki($inData) );
		$this->assertEquals($ex, $out);
		
		$inData = '<div align="center">This text is centered</div>';
		$out = trim( $this->el->parseToWiki($inData) );
		$this->assertEquals($ex, $out);
		
		
		/*
		 * two colon, line break
		 */
		$prefs['feature_use_three_colon_centertag'] = 'n';
		$ex = '::This text is centered::\n::This text is centered::';
		
		$inData = '<div style="text-align: center;">This text is centered<br />This text is centered</div>';
		$out = trim( $this->el->parseToWiki($inData) );
		$out = preg_replace('/\n/', '\n', $out); // fix LF encoding for comparison
		$this->assertEquals($ex, $out);

		$inData = '<div align="center">This text is centered<br />This text is centered</div>';
		$out = trim( $this->el->parseToWiki($inData) );
		$out = preg_replace('/\n/', '\n', $out); // fix LF encoding for comparison
		$this->assertEquals($ex, $out);
		
		
		/*
		 * three colon, line break
		 */
		$prefs['feature_use_three_colon_centertag'] = 'y';
		$ex = ':::This text is centered:::\n:::This text is centered:::';
		
		$inData = '<div style="text-align: center;">This text is centered<br />This text is centered</div>';
		$out = trim( $this->el->parseToWiki($inData) );
		$out = preg_replace('/\n/', '\n', $out); // fix LF encoding for comparison		
		$this->assertEquals($ex, $out);
		
		$inData = '<div align="center">This text is centered<br />This text is centered</div>';
		$out = trim( $this->el->parseToWiki($inData) );
		$out = preg_replace('/\n/', '\n', $out); // fix LF encoding for comparison		
		$this->assertEquals($ex, $out);
	}
	
	
	/**
	 * Align Divs 'right'
	 */
	function testBlockAlignRight() {
		global $prefs;
		
		$ex = '{DIV(align="right")}This text is aligned right{DIV}';

		
		/*
		 * style
		 */
		$inData = '<div style="text-align: right;">This text is aligned right</div>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
		
		
		/*
		 * align
		 */
		$inData = '<div align="right">This text is aligned right</div>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
	}
	
	
	/**
	 * Align Divs 'justify'
	 */
	function testBlockAlignJustified() {
		global $prefs;
		
		$ex = '{DIV(align="justify")}This text is justified{DIV}';

		
		/*
		 * style
		 */
		$inData = '<div style="text-align: justify;">This text is justified</div>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
		
		
		/*
		 * align
		 */
		$inData = '<div align="justify">This text is justified</div>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
	}
	
	
	
	/**
	 * Align paragraphs 'left'
	 */	
	function testParagraphAlignLeft() {
		
		$this->markTestIncomplete('Work in progress.');

		$ex = '{DIV(type="p", align="left")}This text is aligned{DIV}';
		
		
		/*
		 * style
		 */
		$inData = '<p style="text-align: left;">This text is aligned</p>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
		
		
		/*
		 * align
		 */
		$inData = '<p align="left">This text is aligned</p>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
	}
	
	
	/**
	 * Headings 1-6
	 */
	function testNumberedHeadings() {

		$this->markTestIncomplete('Work in progress.');
		
		// all levels, no line break
		$inData = '<h1>9. Heading Level 1</h1>';
		$ex = '!#Heading Level 1';
		$out = trim( $this->el->parseToWiki($inData) );
		$this->assertEquals($ex, $out);

		$inData = '<h2>9.9. Heading Level 2</h2>';
		$ex = '!!#Heading Level 2';
		$out = trim( $this->el->parseToWiki($inData) );
		$this->assertEquals($ex, $out);
		
		$inData = '<h3>9.9.9. Heading Level 3</h3>';
		$ex = '!!!#Heading Level 3';
		$out = trim( $this->el->parseToWiki($inData) );
		$this->assertEquals($ex, $out);
		
		$inData = '<h4>9.9.9.9. Heading Level 4</h4>';
		$ex = '!!!!#Heading Level 4';
		$out = trim( $this->el->parseToWiki($inData) );
		$this->assertEquals($ex, $out);
		
		$inData = '<h5>9.9.9.9.9. Heading Level 5</h5>';
		$ex = '!!!!!#Heading Level 5';
		$out = trim( $this->el->parseToWiki($inData) );
		$this->assertEquals($ex, $out);
		
		$inData = '<h6>9.9.9.9.9.9. Heading Level 6</h6>';
		$ex = '!!!!!!#Heading Level 6';
		$out = trim( $this->el->parseToWiki($inData) );
		$this->assertEquals($ex, $out);		
		
		
		// all levels, line breaks
		$inData = '<h1>9. Heading Level 1<br />and Level 1A<br />and Level 1B</h1>';
		$ex = '!# Heading Level 1%%%and Level 1A%%%and Level 1B';
		$out = trim( $this->el->parseToWiki($inData) );
		$this->assertEquals($ex, $out);	
		
		$inData = '<h2>9.9. Heading Level 2<br />and Level 2A<br />and Level 2B</h2>';
		$ex = '!!# Heading Level 2 %%% and Level 2A %%% and Level 2B';
		$out = trim( $this->el->parseToWiki($inData) );
		$this->assertEquals($ex, $out);
		
		$inData = '<h3>9.9.9. Heading Level 3<br />and Level 3A<br />and Level 3B</h3>';
		$ex = '!!!# Heading Level 3 %%% and Level 3A %%% and Level 3B';
		$out = trim( $this->el->parseToWiki($inData) );
		$this->assertEquals($ex, $out);
		
		$inData = '<h4>9.9.9.9. Heading Level 4<br />and Level 4A<br />and Level 4B</h4>';
		$ex = '!!!!# Heading Level 4 %%% and Level 4A %%% and Level 4B';
		$out = trim( $this->el->parseToWiki($inData) );
		$this->assertEquals($ex, $out);
		
		$inData = '<h5>9.9.9.9.9. Heading Level 5<br />and Level 5A<br />and Level 5B</h5>';
		$ex = '!!!!!# Heading Level 5 %%% and Level 5A %%% and Level 5B';
		$out = trim( $this->el->parseToWiki($inData) );
		$this->assertEquals($ex, $out);
		
		$inData = '<h6>9.9.9.9.9.9. Heading Level 6<br />and Level 6A<br />and Level 6B</h6>';
		$ex = '!!!!!!# Heading Level 6 %%% and Level 6A %%% and Level 6B';
		$out = trim( $this->el->parseToWiki($inData) );
		$this->assertEquals($ex, $out);		
	}
	
	
	/**
	 * Align paragraphs 'center'
	 */	
	function testParagraphAlignCentered() {
		global $prefs;
			

		/*
		 * two colon, no line break
		 */
		$prefs['feature_use_three_colon_centertag'] = 'n';
		$ex = '::This text is centered::';

		$inData = '<p style="text-align: center;">This text is centered</p>';
		$out = trim( $this->el->parseToWiki($inData) );
		$this->assertEquals($ex, $out);
		
		$inData = '<p align="center">This text is centered</p>';
		$out = trim( $this->el->parseToWiki($inData) );
		$this->assertEquals($ex, $out);

		
		/*
		 * three colon, no line break
		 */
		$prefs['feature_use_three_colon_centertag'] = 'y';
		$ex = ':::This text is centered:::';
		
		$inData = '<p style="text-align: center;">This text is centered</p>';
		$out = trim( $this->el->parseToWiki($inData) );
		$this->assertEquals($ex, $out);
		
		$inData = '<p align="center">This text is centered</p>';
		$out = trim( $this->el->parseToWiki($inData) );
		$this->assertEquals($ex, $out);
		
		
		/*
		 * two colon, line break
		 */
		$prefs['feature_use_three_colon_centertag'] = 'n';
		$ex = '::This text is centered::\n::This text is centered::';
		
		$inData = '<p style="text-align: center;">This text is centered<br />This text is centered</p>';
		$out = trim( $this->el->parseToWiki($inData) );
		$out = preg_replace('/\n/', '\n', $out); // fix LF encoding for comparison
		$this->assertEquals($ex, $out);

		$inData = '<p align="center">This text is centered<br />This text is centered</p>';
		$out = trim( $this->el->parseToWiki($inData) );
		$out = preg_replace('/\n/', '\n', $out); // fix LF encoding for comparison
		$this->assertEquals($ex, $out);
		
		
		/*
		 * three colon, line break
		 */
		$prefs['feature_use_three_colon_centertag'] = 'y';
		$ex = ':::This text is centered:::\n:::This text is centered:::';
		
		$inData = '<p style="text-align: center;">This text is centered<br />This text is centered</p>';
		$out = trim( $this->el->parseToWiki($inData) );
		$out = preg_replace('/\n/', '\n', $out); // fix LF encoding for comparison		
		$this->assertEquals($ex, $out);
		
		$inData = '<p align="center">This text is centered<br />This text is centered</p>';
		$out = trim( $this->el->parseToWiki($inData) );
		$out = preg_replace('/\n/', '\n', $out); // fix LF encoding for comparison		
		$this->assertEquals($ex, $out);		
	}	
	

	/**
	 * Align paragraphs 'right'
	 */	
	function testParagraphAlignRight() {
		
		$this->markTestIncomplete('Work in progress.');

		$ex = '{DIV(type="p", align="right")}This text is aligned{DIV}';
		
		
		/*
		 * style
		 */
		$inData = '<p style="text-align: right;">This text is aligned</p>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
		
		
		/*
		 * align
		 */
		$inData = '<p align="right">This text is aligned</p>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
	}	
	
	
	/**
	 * Align paragraphs 'justified'
	 */	
	function testParagraphAlignJustified() {
		
		$this->markTestIncomplete('Work in progress.');
		
		$ex = '{DIV(type="p", align="justify")}This text is aligned{DIV}';

		
		/*
		 * style
		 */
		$inData = '<p style="text-align: justify;">This text is aligned</p>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
		
		
		/*
		 * align
		 */
		$inData = '<p align="justify">This text is aligned</p>';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);
	}

	
	/**
	 * Headings 1-6
	 */
	function testUnnumberedHeadings() {
		
		// all levels, no line break
		$inData = '<h1>Heading Level 1</h1>';
		$ex = '!Heading Level 1';
		$out = trim( $this->el->parseToWiki($inData) );
		$this->assertEquals($ex, $out);

		$inData = '<h2>Heading Level 2</h2>';
		$ex = '!!Heading Level 2';
		$out = trim( $this->el->parseToWiki($inData) );
		$this->assertEquals($ex, $out);
		
		$inData = '<h3>Heading Level 3</h3>';
		$ex = '!!!Heading Level 3';
		$out = trim( $this->el->parseToWiki($inData) );
		$this->assertEquals($ex, $out);
		
		$inData = '<h4>Heading Level 4</h4>';
		$ex = '!!!!Heading Level 4';
		$out = trim( $this->el->parseToWiki($inData) );
		$this->assertEquals($ex, $out);
		
		$inData = '<h5>Heading Level 5</h5>';
		$ex = '!!!!!Heading Level 5';
		$out = trim( $this->el->parseToWiki($inData) );
		$this->assertEquals($ex, $out);
		
		$inData = '<h6>Heading Level 6</h6>';
		$ex = '!!!!!!Heading Level 6';
		$out = trim( $this->el->parseToWiki($inData) );
		$this->assertEquals($ex, $out);
		
		
		// all levels, line breaks
		$inData = '<h1>Heading Level 1<br />and Level 1A<br />and Level 1B</h1>';
		$ex = '!Heading Level 1 %%% and Level 1A %%% and Level 1B';
		$out = trim( $this->el->parseToWiki($inData) );
		$this->assertEquals($ex, $out);		
		
		$inData = '<h2>Heading Level 2<br />and Level 2A<br />and Level 2B</h2>';
		$ex = '!!Heading Level 2 %%% and Level 2A %%% and Level 2B';
		$out = trim( $this->el->parseToWiki($inData) );
		$this->assertEquals($ex, $out);
		
		$inData = '<h3>Heading Level 3<br />and Level 3A<br />and Level 3B</h3>';
		$ex = '!!!Heading Level 3 %%% and Level 3A %%% and Level 3B';
		$out = trim( $this->el->parseToWiki($inData) );
		$this->assertEquals($ex, $out);
		
		$inData = '<h4>Heading Level 4<br />and Level 4A<br />and Level 4B</h4>';
		$ex = '!!!!Heading Level 4 %%% and Level 4A %%% and Level 4B';
		$out = trim( $this->el->parseToWiki($inData) );
		$this->assertEquals($ex, $out);
		
		$inData = '<h5>Heading Level 5<br />and Level 5A<br />and Level 5B</h5>';
		$ex = '!!!!!Heading Level 5 %%% and Level 5A %%% and Level 5B';
		$out = trim( $this->el->parseToWiki($inData) );
		$this->assertEquals($ex, $out);
		
		$inData = '<h6>Heading Level 6<br />and Level 6A<br />and Level 6B</h6>';
		$ex = '!!!!!!Heading Level 6 %%% and Level 6A %%% and Level 6B';
		$out = trim( $this->el->parseToWiki($inData) );
		$this->assertEquals($ex, $out);		
	}
}
