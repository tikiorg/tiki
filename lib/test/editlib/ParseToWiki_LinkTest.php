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

class EditLib_ParseToWiki_LinkTest extends TikiTestCase
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
	 * Test links to pages of an external Wiki
	 */
	function testExternalWiki() {
		
		$this->markTestIncomplete('Work in progress.');
				
		/*
		 * External Wiki ($page defined)
		 * - page name
		 */
		$inData = '???';		
		$ex = '(($this->ext1:Download))';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);			
		
		
		/*
		 * External Wiki ($page defined)
		 * - page name
		 * - anchor
		 */
		$inData = '???';
		$ex = '(($this->ext1:Download|#LTS_-_the_Long_Term_Support_release))';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);			
		
		
		/*
		 * External Wiki ($page defined)
		 * - page name
		 * - anchor
		 * - description
		 */
		$inData = '???';	
		$ex = '(($this->ext1:Download|#LTS_-_the_Long_Term_Support_release|Download LTS))';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);			
	}
		
	
	/**
	 * Test link to anchor within a page
	 */	
	function testInPage() {
		
		$this->markTestIncomplete('Work in progress.');		
		
		$inData = '???';		
		$ex = '[#A_Heading|Link to heading]';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);						
	}
	
	
	/**
	 * Test link for creating e-mail
	 */	
	function testMailTo() {
		
		$this->markTestIncomplete('Work in progress.');		
		
		/*
		 * e-mail
		 */
		$inData = '???';		
		$ex = '[mailto:sombody@nowhere.xyz]';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);				
		
				
		/*
		 * e-mail with description
		 */
		$inData = '???';		
		$ex = '[mailto:sombody@nowhere.xyz|Mail to "Somebody"]';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);				
	}
	
	
	/**
	 * Test links to articles, blogs, ...
	 */	
	function testOtherTikiPages() {
		
		$this->markTestIncomplete('Work in progress.');		
		
		/*
		 * article
		 */
		$inData = '???';
		$ex = '[article1]';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);		
				
		$inData = '???';
		$ex = '[article1|An Article]';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);		
		
		
		/*
		 * blog
		 */
		$inData = '???';
		$ex = '[blog1]';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);		
		
		$inData = '???';
		$ex = '[blog1|A Blog]';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);		
		
		
		/*
		 * forum
		 */
		$inData = '???';
		$ex = '[forum1]';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);		
		
		$inData = '???';
		$ex = '[forum1|A Forum]';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);				
	}
	
	
	/**
	 * Test links to web pages
	 */	
	function testWebResource() {
		
		$this->markTestIncomplete('Work in progress.');		
		
		/*
		 * Web Page:
		 * - link
		 */
		$inData = '???';	
		$ex = '[http://www.tiki.org]';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);			
		
		
		/*
		 * Web Page:
		 * - link
		 * - description
		 */
		$inData = '???';		
		$ex = '[http://www.tiki.org|Tiki Wiki CMS Groupware]';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);			
		
		
		/*
		 * Web Page:
		 * - link
		 * - description
		 * - anchor
		 */
		$inData = '???';		
		$ex = '[http://www.tiki.org#Tiki_News_|News of the Tiki Wiki CMS Groupware]';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);			
		
		
		/*
		 * Web Page:
		 * - link
		 * - description
		 * - anchor
		 * - box
		 */
		$inData = '???';		
		$ex = '[http://www.tiki.org#Tiki_News_|News of the Tiki Wiki CMS Groupware|box]';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);			

		
		/*
		 * Link to video
		 * - link
		 * 
		 */
		$inData = '???';	
		$ex = '[http://www.youtube.com/v/KBewVCducWw&autoplay=1|nocache]';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);			
		
		
		/*
		 * Link to video
		 * - link
		 * - description
		 */
		$inData = '???';	
		$ex = '[http://www.youtube.com/v/KBewVCducWw&autoplay=1|You Tube video in their flash player|nocache]';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);			
		
		
		/*
		 * Link to video
		 * - link
		 * - description
		 * - box
		 */
		$inData = '???';		
		$ex = '[http://www.youtube.com/v/KBewVCducWw&autoplay=1|You Tube video in their flash player|box]'; // additional nocache does not work
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);			
		
		
		/*
		 * Link to video
		 * - link
		 * - description
		 * - box with dimensions
		 */
		$inData = '???';		
		$ex = '[http://www.youtube.com/v/KBewVCducWw&autoplay=1|You Tube video in their flash player|box;width=405;height=340;]'; // additional nocache does not work
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);			
	}
	
	
	/**
	 * Test links to internal wiki pages
	 */	
	function testWikiPage() {
		

		/*
		 * - page name = description
		 */
		$inData = '<a href="tiki-index.php?page=HomePage" title="HomePage" class="wiki wiki_page">HomePage</a>';			
		$ex = '((HomePage))';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);			
		

		/*
		 * - page
		 * - description
		 */
		$inData = '<a href="tiki-index.php?page=HomePage" title="HomePage" class="wiki wiki_page">The Home Page</a>';		
		$ex = '((HomePage|The Home Page))';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);			
		
		
		/*
		 * - page
		 * - link to an anchor
		 * - description
 		 */
		$inData = '<a href="tiki-index.php?page=HomePage#Get_Started_using_Admin_Panel" title="HomePage" class="wiki wiki_page">Home Page, Heading &quot;Admin Panel&quot;</a>';		
		$ex = '((HomePage|#Get_Started_using_Admin_Panel|Home Page, Heading "Admin Panel"))';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);	

		
		/*
		 * Internation characters
		 */	
		$inData = '<a href="tiki-index.php?page=%C3%A4%C3%B6%C3%BC%E2%82%AC+Page" title="äöü€ Page" class="wiki wiki_page">äöü€ Page</a>';	
		$ex = '((äöü€ Page))';
		$out = $this->el->parseToWiki($inData);		
		$this->assertEquals($ex, $out);		
		
		
		/*
		 * Line breaks
		 */
		$inData = '<a href="tiki-index.php?page=HomePage" title="HomePage" class="wiki wiki_page">Home<br />Page</a><br />Another Line';			
		$ex = '((HomePage|Home %%% Page))\nAnother Line';
		$out = $this->el->parseToWiki($inData);
		$out = preg_replace('/\n/', '\n', $out); // fix LF encoding for comparison		
		$this->assertEquals($ex, $out);			
	}			
	
}
