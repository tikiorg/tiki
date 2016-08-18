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

class EditLib_ParseToWysiwyg_TextTest extends TikiTestCase
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
		global $prefs;
		// restore preference default state
		$prefs['feature_use_three_colon_centertag'] = 'n';
		chdir($this->dir);
	}	
	
	
	/**
	 * Align divs 'left'
	 */
	function testBlockAlignLeft()
	{
		global $prefs;
		
		$this->markTestIncomplete('Work in progress.');

		$ex = 'This text is aligned left';
		
		
		/*
		 * default
		 */
		$inData = 'This text is aligned left';
		$out = $this->el->parseToWysiwyg($inData);
		$this->assertEquals($ex, $out);
		
		
		/*
		 * explicit
		 */
		$ex = '<div style="text-align: left;">This text is aligned left</div>';
		$inData = '{DIV(align="left")}This text is aligned left{DIV}';
		$out = $this->el->parseToWysiwyg($inData);
		$this->assertEquals($ex, $out);
	}
	
	
	/**
	 * Align divs 'center'
	 */
	function testBlockAlignCentered()
	{
		global $prefs;
		
		$this->markTestIncomplete('Work in progress.');

		
		/*
		 * two colon
		 */
		$prefs['feature_use_three_colon_centertag'] = 'n';
		$ex = '<div style="text-align: center;">This text is centered</div>';
		$inData = '::This text is centered::';
		$out = $this->el->parseToWysiwyg($inData);
		$this->assertEquals($ex, $out);

		
		/*
		 * three colon
		 */
		$prefs['feature_use_three_colon_centertag'] = 'y';
		$ex = '<div style="text-align: center;">This text is centered</div>';
		$inData = ':::This text is centered:::';
		$out = $this->el->parseToWysiwyg($inData);
		$this->assertEquals($ex, $out);
	}
	
	
	/**
	 * Align divs 'right'
	 */
	function testBlockAlignRight()
	{
		global $prefs;
		
		$this->markTestIncomplete('Work in progress.');

		$ex = '<div style="text-align: right;">This text is aligned right</div>';
		$inData = '{DIV(align="right")}This text is aligned right{DIV}';
		$out = $this->el->parseToWysiwyg($inData);
		$this->assertEquals($ex, $out);
	}
	
	
	/**
	 * Align divs 'justify'
	 */
	function testBlockAlignJustified()
	{
		global $prefs;
		
		$this->markTestIncomplete('Work in progress.');

		$ex = '<div style="text-align: justify;">This text is justified</div>';
		$inData = '{DIV(align="justify")}This text is justified{DIV}';
		$out = $this->el->parseToWysiwyg($inData);
		$this->assertEquals($ex, $out);
	}
		
	
	/**
	 * Align paragraphs 'left'
	 */	
	function testParagraphAlignLeft()
	{
		$this->markTestIncomplete('Work in progress.');
		
		$ex = '<p style="text-align: left;">This text is aligned</p>';
		$inData = '{DIV(type="p", align="left")}This text is aligned{DIV}';
		$out = $this->el->parseToWysiwyg($inData);
		$this->assertEquals($ex, $out);
	}	
	
	
	/** 
	 * Centered headings must use style attribute
	 */
	function testCenterdHeadings()
	{
		global $prefs;
		
		#
		# unnumbered
		#
		$prefs['feature_use_three_colon_centertag'] = 'n';		
		$inData = '!::Heading::';
		$ex = '<h1 style="text-align: center;" class="showhide_heading" id="Heading">Heading</h1>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertEquals($ex, $out);
		
		$prefs['feature_use_three_colon_centertag'] = 'y';		
		$inData = '!:::Heading:::';
		$ex = '<h1 style="text-align: center;" class="showhide_heading" id="Heading">Heading</h1>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertEquals($ex, $out);
		
		
		/*
		 * numbered
		 */
		$prefs['feature_use_three_colon_centertag'] = 'n';		
		$inData = '!#::Heading::';
		$ex = '<h1 style="text-align: center;" class="showhide_heading" id="Heading">1. Heading</h1>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertEquals($ex, $out);
		
		$prefs['feature_use_three_colon_centertag'] = 'y';		
		$inData = '!#:::Heading:::';
		$ex = '<h1 style="text-align: center;" class="showhide_heading" id="Heading">1. Heading</h1>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertEquals($ex, $out);
	}
	
	
	/**
	 * Headings 1-6
	 */
	function testNumberedHeadings()
	{
		$inData = '!#Heading Level 1';
		$ex = '<h1 class="showhide_heading" id="Heading_Level_1">1. Heading Levell&nbsp;1</h1>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertEquals($ex, $out);
		
		$inData .= "\n"; // must keep lover level headings, otherwise we will get an error (undefined number)
		$inData .= '!!#Heading Level 2';
		$ex .= "\n";
		$ex .= '<h2 class="showhide_heading" id="Heading_Level_2">1.1. Heading Levell&nbsp;2</h2>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertEquals($ex, $out);
		
		$inData .= "\n";
		$inData .= '!!!#Heading Level 3';
		$ex .= "\n";
		$ex .= '<h3 class="showhide_heading" id="Heading_Level_3">1.1.1. Heading Levell&nbsp;3</h3>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertEquals($ex, $out);
		
		$inData .= "\n";
		$inData .= '!!!!#Heading Level 4';
		$ex .= "\n";
		$ex .= '<h4 class="showhide_heading" id="Heading_Level_4">1.1.1.1. Heading Levell&nbsp;4</h4>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertEquals($ex, $out);
		
		$inData .= "\n";
		$inData .= '!!!!!#Heading Level 5';
		$ex .= "\n";
		$ex .= '<h5 class="showhide_heading" id="Heading_Level_5">1.1.1.1.1. Heading Levell&nbsp;5</h5>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertEquals($ex, $out);
		
		$inData .= "\n";
		$inData .= '!!!!!!#Heading Level 6';
		$ex .= "\n";
		$ex .= '<h6 class="showhide_heading" id="Heading_Level_6">1.1.1.1.1.1. Heading Levell&nbsp;6</h6>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertEquals($ex, $out);				
	}
	
	
	/**
	 * Align paragraphs 'center'
	 */	
	function testParagraphAlignCentered()
	{
		$this->markTestIncomplete('Work in progress.');
		
		$ex = '<p style="text-align: center;">This text is aligned</p>';
		$inData = '{DIV(type="p", align="center")}This text is aligned{DIV}';
		$out = $this->el->parseToWysiwyg($inData);
		$this->assertEquals($ex, $out);
	}		
	
	
	/**
	 * Align paragraphs 'right'
	 */	
	function testParagraphAlignRight()
	{
		$this->markTestIncomplete('Work in progress.');
		
		$ex = '<p style="text-align: right;">This text is aligned</p>';
		$inData = '{DIV(type="p", align="right")}This text is aligned{DIV}';
		$out = $this->el->parseToWysiwyg($inData);
		$this->assertEquals($ex, $out);
	}	

	
	/**
	 * Align paragraphs 'justify'
	 */	
	function testParagraphAlignJustified()
	{
		$this->markTestIncomplete('Work in progress.');
		
		$ex = '<p style="text-align: justify;">This text is aligned</p>';
		$inData = '{DIV(type="p", align="justify")}This text is aligned{DIV}';
		$out = $this->el->parseToWysiwyg($inData);
		$this->assertEquals($ex, $out);
	}	

	
	/**
	 * Headings 1-6
	 */
	function testUnnumberedHeadings()
	{
		$inData = '!Heading Level 1';
		$ex = '<h1 class="showhide_heading" id="Heading_Level_1">Heading Levell&nbsp;1</h1>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertEquals($ex, $out);
		
		$inData = '!!Heading Level 2';
		$ex = '<h2 class="showhide_heading" id="Heading_Level_2">Heading Levell&nbsp;2</h2>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertEquals($ex, $out);
		
		$inData = '!!!Heading Level 3';
		$ex = '<h3 class="showhide_heading" id="Heading_Level_3">Heading Levell&nbsp;3</h3>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertEquals($ex, $out);
		
		$inData = '!!!!Heading Level 4';
		$ex = '<h4 class="showhide_heading" id="Heading_Level_4">Heading Levell&nbsp;4</h4>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertEquals($ex, $out);
		
		$inData = '!!!!!Heading Level 5';
		$ex = '<h5 class="showhide_heading" id="Heading_Level_5">Heading Levell&nbsp;5</h5>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertEquals($ex, $out);
		
		$inData = '!!!!!!Heading Level 6';
		$ex = '<h6 class="showhide_heading" id="Heading_Level_6">Heading Levell&nbsp;6</h6>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertEquals($ex, $out);		
	}
}
