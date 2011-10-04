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
require_once 'lib/admin/adminlib.php';


class EditLib_ParseToWysiwyg_LinkTest extends TikiTestCase
{
	
	private $dir = '';  // the unmodifed directory
	private $el = null; // the EditLib
	private $ext1 = 'test_ext1'; // name of the external Wiki 1
	
	
	function __construct() {
		$this->dir = getcwd();
		
		// we must set the page regex, otherwise the links get not parsed
		// taken from: 'lib/setup/wiki.php' with  $prefs['wiki_page_regex'] == 'full'
		global $page_regex;
		$page_regex = '([A-Za-z0-9_]|[\x80-\xFF])([\.: A-Za-z0-9_\-]|[\x80-\xFF])*([A-Za-z0-9_]|[\x80-\xFF])';
	}
		
	
	function setUp() {
		$_SERVER['HTTP_HOST'] = ''; // editlib expects that HTTP_HOST is defined	
		$_SERVER['SERVER_NAME'] = 'myserver'; // the ParserLib expects the servername to be set	

		global $prefs;
		$prefs['feature_sefurl'] = 'n'; // default
		
		$this->el = new EditLib();
		
		chdir($this->dir);
		chdir('../../'); // the tiki installation directory
	}
	
		
	function tearDown() {
		chdir($this->dir);

		/*
		 * remove the external Wikis defined in the tests 
		 */		
		$al = new AdminLib();
		$more = true;
		$o = 0;
		$n = 10;
		while ($more) {
			$ret = $al->list_extwiki($o, $n, 'extwikiId_desc', '');
			if (count($ret['data'])){
				foreach($ret['data'] as $ext) {
					switch ( $ext['name']) {
						case $this->ext1 : $id = $ext['extwikiId']; break;
						default: $id = -1;
					}
					if ($id >= 0) {
						$al->remove_extwiki($id);
						$o += $n - 1;
					} else {
						$o += $n;						
					}
				}
			} else {
				$more = false;
			}
		}
		
	}	
		

	/**
	 * Test links to pages of an external Wiki
	 * 
	 * This test is used to detect changes in the parser. Here, the EditLib is not used.
	 */
	function testExternalWiki() {

		$this->markTestIncomplete('Work in progress.');
		/*
		 * setup the external wikis and the parser
		 */
		$al = new AdminLib();
		$al->replace_extwiki(0, 'http://tikiwiki.org/tiki-index.php?page=$page', $this->ext1);
		$p = $al->lib('parser');


		/*
		 * External Wiki ($page defined)
		 * - page name
		 */
		$inData = "(($this->ext1:Download))" ;		
		$ex = '<a href="http://tikiwiki.org/tiki-index.php?page=Download" class="wiki external test_ext1">Download</a>';
		$out = trim($p->parse_data($inData));
		$this->assertEquals($ex, $out);			
		
		
		/*
		 * External Wiki ($page defined)
		 * - page name
		 * - anchor
		 */
		$inData = "(($this->ext1:Download|#LTS_-_the_Long_Term_Support_release))" ;
		$ex = '<a href="http://tikiwiki.org/tiki-index.php?page=Download#LTS_-_the_Long_Term_Support_release" class="wiki external test_ext1">Download</a>';
		$out = trim($p->parse_data($inData));
		$this->assertEquals($ex, $out);			
		
		
		/*
		 * External Wiki ($page defined)
		 * - page name
		 * - anchor
		 * - description
		 */
		$inData = "(($this->ext1:Download|#LTS_-_the_Long_Term_Support_release|Download LTS))" ;	
		$ex = '<a href="http://tikiwiki.org/tiki-index.php?page=Download#LTS_-_the_Long_Term_Support_release" class="wiki external test_ext1">Download LTS</a>';
		$out = trim($p->parse_data($inData));
		$this->assertEquals($ex, $out);			
	}
	
	
	/**
	 * Test link to anchor within a page
	 */
	function testInPage() {
		
		$inData = '[#A_Heading|Link to heading]';		
		$ex = '<a class="wiki" href="#A_Heading" rel="">Link to heading</a>';
		$out = trim( $this->el->parseToWysiwyg($inData) );
		$out = preg_replace('/  /', ' ', $out); // the parser writes to many spaces
		$this->assertEquals($ex, $out);				
	}
	
	
	/**
	 * Test link for creating e-mail
	 */
	function testMailTo() {

		
		/*
		 * e-mail
		 */
		$inData = '[mailto:sombody@nowhere.xyz]';		
		$ex = '<a class="wiki"  href="mailto:sombody@nowhere.xyz" rel="">mailto:sombody@nowhere.xyz</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertEquals($ex, $out);				
		
				
		/*
		 * e-mail with description
		 */
		$inData = '[mailto:sombody@nowhere.xyz|Mail to "Somebody"]';		
		$ex = '<a class="wiki"  href="mailto:sombody@nowhere.xyz" rel="">Mail to "Somebody"</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertEquals($ex, $out);				
	}
	
	
	/**
	 * Test links to articles, blogs, ...
	 */
	function testOtherTikiPages() {

		/*
		 * article
		 */
		$inData = '[article1]';
		$ex = '<a class="wiki"  href="article1" rel="">article1</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertEquals($ex, $out);		
				
		$inData = '[article1|An Article]';
		$ex = '<a class="wiki"  href="article1" rel="">An Article</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertEquals($ex, $out);		
		
		
		/*
		 * blog
		 */
		$inData = '[blog1]';
		$ex = '<a class="wiki"  href="blog1" rel="">blog1</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertEquals($ex, $out);		
		
		$inData = '[blog1|A Blog]';
		$ex = '<a class="wiki"  href="blog1" rel="">A Blog</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertEquals($ex, $out);		
		
		
		/*
		 * forum
		 */
		$inData = '[forum1]';
		$ex = '<a class="wiki"  href="forum1" rel="">forum1</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertEquals($ex, $out);		
		
		$inData = '[forum1|A Forum]';
		$ex = '<a class="wiki"  href="forum1" rel="">A Forum</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertEquals($ex, $out);			
	}
	
	
	
	/**
	 * Test links to web pages
	 */
	function testWebResource() {
			
		
		/*
		 * Web Page:
		 * - link
		 */
		$inData = '[http://www.tiki.org]';		
		$ex = '<a class="wiki external" target="_blank" href="http://www.tiki.org" rel="external nofollow">http://www.tiki.org</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertEquals($ex, $out);			
		
		
		/*
		 * Web Page:
		 * - link
		 * - description
		 */
		$inData = '[http://www.tiki.org|Tiki Wiki CMS Groupware]';		
		$ex = '<a class="wiki external" target="_blank" href="http://www.tiki.org" rel="external nofollow">Tiki Wiki CMS Groupware</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertEquals($ex, $out);			
		
		
		/*
		 * Web Page:
		 * - link
		 * - description
		 * - anchor
		 */
		$inData = '[http://www.tiki.org#Tiki_News_|News of the Tiki Wiki CMS Groupware]';		
		$ex = '<a class="wiki external" target="_blank" href="http://www.tiki.org#Tiki_News_" rel="external nofollow">News of the Tiki Wiki CMS Groupware</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertEquals($ex, $out);			
		
		
		/*
		 * Web Page:
		 * - link
		 * - description
		 * - anchor
		 * - box
		 */
		$inData = '[http://www.tiki.org#Tiki_News_|News of the Tiki Wiki CMS Groupware|box]';		
		$ex = '<a class="wiki external" target="_blank" href="http://www.tiki.org#Tiki_News_" rel="box external nofollow">News of the Tiki Wiki CMS Groupware</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertEquals($ex, $out);			

		
		/*
		 * Link to video
		 * - link
		 * 
		 */
		$inData = '[http://www.youtube.com/v/KBewVCducWw&autoplay=1|nocache]';		
		$ex = '<a class="wiki external" target="_blank" href="http://www.youtube.com/v/KBewVCducWw&amp;autoplay=1" rel="external nofollow">http://www.youtube.com/v/KBewVCducWw&amp;autoplay=1</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertEquals($ex, $out);			
		
		
		/*
		 * Link to video
		 * - link
		 * - description
		 */
		$inData = '[http://www.youtube.com/v/KBewVCducWw&autoplay=1|You Tube video in their flash player|nocache]';		
		$ex = '<a class="wiki external" target="_blank" href="http://www.youtube.com/v/KBewVCducWw&amp;autoplay=1" rel="external nofollow">You Tube video in their flash player</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertEquals($ex, $out);			
		
		
		/*
		 * Link to video
		 * - link
		 * - description
		 * - box
		 */
		$inData = '[http://www.youtube.com/v/KBewVCducWw&autoplay=1|You Tube video in their flash player|box]'; // additional nocache does not work		
		$ex = '<a class="wiki external" target="_blank" href="http://www.youtube.com/v/KBewVCducWw&amp;autoplay=1" rel="box external nofollow">You Tube video in their flash player</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertEquals($ex, $out);			
		
		
		/*
		 * Link to video
		 * - link
		 * - description
		 * - box with dimensions
		 */
		$inData = '[http://www.youtube.com/v/KBewVCducWw&autoplay=1|You Tube video in their flash player|box;width=405;height=340;]'; // additional nocache does not work		
		$ex = '<a class="wiki external" target="_blank" href="http://www.youtube.com/v/KBewVCducWw&amp;autoplay=1" rel="box;width=405;height=340; external nofollow">You Tube video in their flash player</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertEquals($ex, $out);			
	}
	
	
	/**
	 * Test links to internal wiki pages
	 */
	function testWikiPage() {

		global $tikilib;
		
		
		/*
		 * 'HomePage' must exists
		 */
		$homePage = 'HomePage';
		$info = $tikilib->get_page_info($homePage, false);
		$this->assertTrue($info != null);
		
		
		/*
		 * 'Page does not exist not exist' must not exist
		 */
		$noPage = 'Page does not exist not exist';
		$info = $tikilib->get_page_info($noPage, false);
		$this->assertFalse($info);
		
		
		/*
		 * - existing page
		 */
		$inData = "(($homePage))";		
		$ex = '<a href="tiki-index.php?page=HomePage" title="HomePage" class="wiki page">HomePage</a>';
		$out = trim( $this->el->parseToWysiwyg($inData) );
		$this->assertEquals($ex, $out);			
		
		
		/*
		 * - existing page
		 * - description
		 */
		$inData = "(($homePage|The Home Page))";		
		$ex = '<a href="tiki-index.php?page=HomePage" title="HomePage" class="wiki page">The Home Page</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertEquals($ex, $out);			
		
		
		/*
		 * - existing name
		 * - link to an anchor
		 * - description
 		 */
		$inData = "(($homePage|#Get_Started_using_Admin_Panel|Home Page, Heading \"Admin Panel\"))";		
		$ex = '<a href="tiki-index.php?page=HomePage#Get_Started_using_Admin_Panel" title="HomePage" class="wiki page">Home Page, Heading &quot;Admin Panel&quot;</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertEquals($ex, $out);	
		

		/*
		 * Default behavior -> class="wiki wikinew"
		 * 
		 * - inexistent page
 		 */
		$inData = "(($noPage))";
		$ex = 'Page does not exist not exist<a href="tiki-editpage.php?page=Page+does+not+exist+not+exist" title="Create page: Page does not exist not exist" class="wiki wikinew">?</a>';
		$out = trim($tikilib->lib('parser')->parse_Data($inData));
		$this->assertEquals($ex, $out);			
		
		/*
		 * Default behavior -> class="wiki wikinew"
		 * 
		 * - inexistent page
		 * - description
 		 */
		$inData = "(($noPage|Page does not exist))";
		$ex = 'Page does not exist<a href="tiki-editpage.php?page=Page+does+not+exist+not+exist" title="Create page: Page does not exist not exist" class="wiki wikinew">?</a>';
		$out = trim($tikilib->lib('parser')->parse_Data($inData));
		$this->assertEquals($ex, $out);		
		
		
		/*
		 * Default behavior -> class="wiki wikinew"
		 * 
		 * - inexistent page
		 * - link to an anchor
		 * - description
 		 */
		$inData = "(($noPage|#anchor|Page does not exist))";
		$ex = 'Page does not exist<a href="tiki-editpage.php?page=Page+does+not+exist+not+exist" title="Create page: Page does not exist not exist" class="wiki wikinew">?</a>';
		$out = trim($tikilib->lib('parser')->parse_Data($inData));
		$this->assertEquals($ex, $out);			
		
		
		/*
		 * CKE behavior -> class="wiki page"
		 * - inexistent page
 		 */
		$cke_opts = array( 'absolute_links'=>true, 'suppress_icons' => true, 'ck_editor' => true);
		$inData = "(($noPage))";
		$ex = '<a href="tiki-index.php?page=Page+does+not+exist+not+exist" title="Page does not exist not exist" class="wiki page">Page does not exist not exist</a>';
		$out = trim($tikilib->lib('parser')->parse_Data($inData, $cke_opts));
		$this->assertEquals($ex, $out);			
		

		/*
		 * CKE behavior -> class="wiki page"
		 * 
		 * - inexistent page
		 * - description
 		 */
		$inData = "(($noPage|Page does not exist))";
		$ex = '<a href="tiki-index.php?page=Page+does+not+exist+not+exist" title="Page does not exist not exist" class="wiki page">Page does not exist</a>';
		$out = trim($tikilib->lib('parser')->parse_Data($inData, $cke_opts));
		$this->assertEquals($ex, $out);		
		
		
		/*
		 * CKE behavior -> class="wiki page"
		 * 
		 * - inexistent page
		 * - link to an anchor
		 * - description
 		 */
		$inData = "(($noPage|#anchor|Page does not exist))";
		$ex = '<a href="tiki-index.php?page=Page+does+not+exist+not+exist#anchor" title="Page does not exist not exist" class="wiki page">Page does not exist</a>';
		$out = trim($this->el->parseToWysiwyg($inData, $cke_opts));
		$this->assertEquals($ex, $out);		

		
		/*
		 * Internation characters
		 */	
		$inData = "((äöü€ Page))";
		$ex = '<a href="tiki-index.php?page=%C3%A4%C3%B6%C3%BC%E2%82%AC+Page" title="&auml;&ouml;&uuml;&euro; Page" class="wiki page">&auml;&ouml;&uuml;&euro; Page</a>';
		$out = trim($this->el->parseToWysiwyg($inData, $cke_opts));		
		$this->assertEquals($ex, $out);		
	}
	
}
