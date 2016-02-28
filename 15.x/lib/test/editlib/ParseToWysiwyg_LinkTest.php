<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * @group unit
 *
 */


class EditLib_ParseToWysiwyg_LinkTest extends TikiTestCase
{
	private $dir = '';  // the unmodifed directory
	private $el = null; // the EditLib
	private $ext1 = 'test_ext1'; // name of the external Wiki 1


	function __construct()
	{
		$this->dir = getcwd();

		// we must set the page regex, otherwise the links get not parsed
		// taken from: 'lib/setup/wiki.php' with  $prefs['wiki_page_regex'] == 'full'
		global $page_regex;
		$page_regex = '([A-Za-z0-9_]|[\x80-\xFF])([\.: A-Za-z0-9_\-]|[\x80-\xFF])*([A-Za-z0-9_]|[\x80-\xFF])';
	}


	function setUp()
	{
		$_SERVER['HTTP_HOST'] = ''; // editlib expects that HTTP_HOST is defined	
		$_SERVER['SERVER_NAME'] = 'myserver'; // the ParserLib expects the servername to be set	

		global $prefs;
		$prefs['feature_sefurl'] = 'n'; // default

		$this->el = TikiLib::lib('edit');

		chdir($this->dir);
		chdir('../../'); // the tiki installation directory
	}


	function tearDown()
	{
		chdir($this->dir);

		/*
		 * remove the external Wikis defined in the tests 
		 */
		$tikilib = TikiLib::lib('tiki');

		$query = 'SELECT `name`, `extwikiId` FROM `tiki_extwiki`';
		$wikis = $tikilib->fetchMap($query);
		$tmp_wikis = array($this->ext1);

		foreach ($tmp_wikis as $w) {
			if (isset($wikis[$w])) {
				$id = $wikis[$w];
				$tikilib->lib('admin')->remove_extwiki($id);
			}
		}
	}	


	/**
	 * Test links to pages of an external Wiki
	 * 
	 * This test is used to detect changes in the parser. Here, the EditLib is not used.
	 * 
	 * Note: Links with an invalid wiki identifier are parsed as regular Wiki page links.
	 */
	function testExternalWiki()
	{

		/*
		 * setup the external wikis and the parser
		 */
		$tikilib = TikiLib::lib('tiki');
		$tikilib->lib('admin')->replace_extwiki(0, 'http://tikiwiki.org/tiki-index.php?page=$page', $this->ext1);
		$p = $tikilib->lib('parser');


		/*
		 * External Wiki 
		 * - page name
		 */
		$inData = "(($this->ext1:Download))" ;		
		$ex = '<a href="http://tikiwiki.org/tiki-index.php?page=Download" class="wiki ext_page test_ext1">Download</a>';
		$out = trim($p->parse_data($inData));
		$this->assertContains($ex, $out);			


		/*
		 * External Wiki
		 * - page name
		 * - anchor
		 */
		$inData = "(($this->ext1:Download|#LTS_-_the_Long_Term_Support_release))" ;
		$ex = '<a href="http://tikiwiki.org/tiki-index.php?page=Download#LTS_-_the_Long_Term_Support_release" class="wiki ext_page test_ext1">Download</a>';
		$out = trim($p->parse_data($inData));
		$this->assertContains($ex, $out);			


		/*
		 * External Wiki
		 * - page name
		 * - anchor
		 * - description
		 */
		$inData = "(($this->ext1:Download|#LTS_-_the_Long_Term_Support_release|Download LTS))" ;	
		$ex = '<a href="http://tikiwiki.org/tiki-index.php?page=Download#LTS_-_the_Long_Term_Support_release" class="wiki ext_page test_ext1">Download LTS</a>';
		$out = trim($p->parse_data($inData));
		$this->assertContains($ex, $out);	
	}


	/**
	 * Test link to anchor within a page
	 */
	function testInPage()
	{

		/*
		 * with description
		 */
		$inData = '[#A_Heading|Link to heading]';		
		$ex = '<a class="wiki" href="#A_Heading" rel="">Link to heading</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$out = preg_replace('/  /', ' ', $out); // the parser writes to many spaces
		$this->assertContains($ex, $out);


		/*
		 * no description
		 */
		$inData = '[#A_Heading]';		
		$ex = '<a class="wiki" href="#A_Heading" rel="">#A_Heading</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$out = preg_replace('/  /', ' ', $out); // the parser writes to many spaces
		$this->assertContains($ex, $out);				
	}


	/**
	 * Test link for creating e-mail
	 */
	function testMailTo()
	{

		/*
		 * e-mail
		 */
		$inData = '[mailto:sombody@nowhere.xyz]';		
		$ex = '<a class="wiki"  href="mailto:sombody@nowhere.xyz" rel="">mailto:sombody@nowhere.xyz</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertContains($ex, $out);				


		/*
		 * e-mail with description
		 */
		$inData = '[mailto:sombody@nowhere.xyz|Mail to "Somebody"]';		
		$ex = '<a class="wiki"  href="mailto:sombody@nowhere.xyz" rel="">Mail to "Somebody"</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertContains($ex, $out);				
	}


	/**
	 * Test links to articles, blogs, ...
	 */
	function testOtherTikiPages()
	{

		/*
		 * article
		 */
		$inData = '[article1]';
		$ex = '<a class="wiki"  href="article1" rel="">article1</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertContains($ex, $out);		

		$inData = '[article1|An Article]';
		$ex = '<a class="wiki"  href="article1" rel="">An Article</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertContains($ex, $out);		


		/*
		 * blog
		 */
		$inData = '[blog1]';
		$ex = '<a class="wiki"  href="blog1" rel="">blog1</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertContains($ex, $out);		

		$inData = '[blog1|A Blog]';
		$ex = '<a class="wiki"  href="blog1" rel="">A Blog</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertContains($ex, $out);		


		/*
		 * forum
		 */
		$inData = '[forum1]';
		$ex = '<a class="wiki"  href="forum1" rel="">forum1</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertContains($ex, $out);		

		$inData = '[forum1|A Forum]';
		$ex = '<a class="wiki"  href="forum1" rel="">A Forum</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertContains($ex, $out);			
	}



	/**
	 * Test links to web pages
	 */
	function testWebResource()
	{

        $this->markTestSkipped("As of 2013-10-02, this test is broken, and nobody knows how to fix it. Mark as Skipped for now.");

		/*
		 * Web Page:
		 * - link
		 */
		$inData = '[http://www.tiki.org]';		
		$ex = '<a class="wiki external" target="_blank" href="http://www.tiki.org" rel="external nofollow">http://www.tiki.org</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertContains($ex, $out);			


		/*
		 * Web Page:
		 * - link
		 * - description
		 */
		$inData = '[http://www.tiki.org|Tiki Wiki CMS Groupware]';		
		$ex = '<a class="wiki external" target="_blank" href="http://www.tiki.org" rel="external nofollow">Tiki Wiki CMS Groupware</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertContains($ex, $out);			


		/*
		 * Web Page:
		 * - link
		 * - description
		 * - anchor
		 */
		$inData = '[http://www.tiki.org#Tiki_News_|News of the Tiki Wiki CMS Groupware]';		
		$ex = '<a class="wiki external" target="_blank" href="http://www.tiki.org#Tiki_News_" rel="external nofollow">News of the Tiki Wiki CMS Groupware</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertContains($ex, $out);			


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
		$this->assertContains($ex, $out);			


		/*
		 * Link to video
		 * - link
		 * 
		 */
		$inData = '[http://www.youtube.com/v/KBewVCducWw&autoplay=1|nocache]';		
		$ex = '<a class="wiki external" target="_blank" href="http://www.youtube.com/v/KBewVCducWw&amp;autoplay=1" rel="external nofollow">http://www.youtube.com/v/KBewVCducWw&amp;autoplay=1</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertContains($ex, $out);			


		/*
		 * Link to video
		 * - link
		 * - description
		 */
		$inData = '[http://www.youtube.com/v/KBewVCducWw&autoplay=1|You Tube video in their flash player|nocache]';		
		$ex = '<a class="wiki external" target="_blank" href="http://www.youtube.com/v/KBewVCducWw&amp;autoplay=1" rel="external nofollow">You Tube video in their flash player</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertContains($ex, $out);			


		/*
		 * Link to video
		 * - link
		 * - description
		 * - box
		 */
		$inData = '[http://www.youtube.com/v/KBewVCducWw&autoplay=1|You Tube video in their flash player|box]'; // additional nocache does not work		
		$ex = '<a class="wiki external" target="_blank" href="http://www.youtube.com/v/KBewVCducWw&amp;autoplay=1" rel="box external nofollow">You Tube video in their flash player</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertContains($ex, $out);			


		/*
		 * Link to video
		 * - link
		 * - description
		 * - box with dimensions
		 */
		$inData = '[http://www.youtube.com/v/KBewVCducWw&autoplay=1|You Tube video in their flash player|box;width=405;height=340;]'; // additional nocache does not work		
		$ex = '<a class="wiki external" target="_blank" href="http://www.youtube.com/v/KBewVCducWw&amp;autoplay=1" rel="box;width=405;height=340; external nofollow">You Tube video in their flash player</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertContains($ex, $out);			
	}


	/**
	 * Test links to internal wiki pages
	 */
	function testWikiPage()
	{

		$tikilib = TikiLib::lib('tiki');

        $this->markTestSkipped("As of 2013-10-02, this test is broken, and nobody knows how to fix it. Mark as Skipped for now.");

		$homePage = 'HomePage';
		$noPage = 'Page does not exist not exist';
		
		/*
		 * - existing page
		 */
		$inData = "(($homePage))";		
		$ex = '<a href="tiki-index.php?page=HomePage" title="HomePage" class="wiki wiki_page">HomePage</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertContains($ex, $out);			


		/*
		 * - existing page
		 * - description
		 */
		$inData = "(($homePage|The Home Page))";		
		$ex = '<a href="tiki-index.php?page=HomePage" title="HomePage" class="wiki wiki_page">The Home Page</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertContains($ex, $out);			


		/*
		 * - existing name
		 * - link to an anchor
		 * - description
		 */
		$inData = "(($homePage|#Get_Started_using_Admin_Panel|Home Page, Heading \"Admin Panel\"))";		
		$ex = '<a href="tiki-index.php?page=HomePage#Get_Started_using_Admin_Panel" title="HomePage" class="wiki wiki_page">Home Page, Heading &quot;Admin Panel&quot;</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertContains($ex, $out);	


		/*
		 * Default behavior -> class="wiki wikinew"
		 * 
		 * - inexistent page
		 */
		$inData = "(($noPage))";
		$ex = 'Page does not exist not exist<a href="tiki-editpage.php?page=Page+does+not+exist+not+exist" title="Create page: Page does not exist not exist" class="wiki wikinew">?</a>';
		$out = trim($tikilib->lib('parser')->parse_Data($inData));
		$this->assertContains($ex, $out);			

		/*
		 * Default behavior -> class="wiki wikinew"
		 * 
		 * - inexistent page
		 * - description
		 */
		$inData = "(($noPage|Page does not exist))";
		$ex = 'Page does not exist<a href="tiki-editpage.php?page=Page+does+not+exist+not+exist" title="Create page: Page does not exist not exist" class="wiki wikinew">?</a>';
		$out = trim($tikilib->lib('parser')->parse_Data($inData));
		$this->assertContains($ex, $out);		


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
		$this->assertContains($ex, $out);			


		/*
		 * CKE behavior -> class="wiki page"
		 * - inexistent page
		 */
		$inData = "(($noPage))";
		$ex = '<a href="tiki-index.php?page=Page+does+not+exist+not+exist" title="Page does not exist not exist" class="wiki wiki_page">Page does not exist not exist</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertContains($ex, $out);			


		/*
		 * CKE behavior -> class="wiki page"
		 * 
		 * - inexistent page
		 * - description
		 */
		$inData = "(($noPage|Page does not exist))";
		$ex = '<a href="tiki-index.php?page=Page+does+not+exist+not+exist" title="Page does not exist not exist" class="wiki wiki_page">Page does not exist</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertContains($ex, $out);		


		/*
		 * CKE behavior -> class="wiki page"
		 * 
		 * - inexistent page
		 * - link to an anchor
		 * - description
		 */
		$inData = "(($noPage|#anchor|Page does not exist))";
		$ex = '<a href="tiki-index.php?page=Page+does+not+exist+not+exist#anchor" title="Page does not exist not exist" class="wiki wiki_page">Page does not exist</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertContains($ex, $out);


		/*
		 * Internation characters
		 */	
		$inData = "((äöü€ Page))";
		$ex = '<a href="tiki-index.php?page=%C3%A4%C3%B6%C3%BC%E2%82%AC+Page" title="&auml;&ouml;&uuml;&euro; Page" class="wiki wiki_page">&auml;&ouml;&uuml;&euro; Page</a>';
		$out = trim($this->el->parseToWysiwyg($inData));
		$this->assertContains($ex, $out);		
	}


	/*
	 * Test anchors generated by {ANAME}
	 */
	function testPluginAname()
	{
		$this->markTestIncomplete('Work in progress.');		

		$inData = "{ANAME()}anchor{ANAME}";
		$ex = '<a id="anchor"></a>';
		$out = trim($this->el->parseToWysiwyg($inData));		
		$this->assertEquals($ex, $out);				
	}
}
