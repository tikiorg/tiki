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

class EditLib_ParseToWiki_LinkTest extends TikiTestCase
{
	private $dir = '';  // the unmodifed directory
	private $el = null; // the EditLib
	private $ext1 = 'test_ext1'; // name of the external Wiki 1


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
		chdir($this->dir);

		/*
		 * remove the external Wikis defined in the tests 
		 */
		global $tikilib;

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
	 * Note: Links with an invalid wiki identifier are parsed as regular Wiki page links.
	 */
	function testExternalWiki()
	{

		/*
		 * setup the external wikis and the parser
		 */
		global $tikilib;
		$tikilib->lib('admin')->replace_extwiki(0, 'http://tikiwiki.org/tiki-index.php?page=$page', $this->ext1);


		/*
		 * External Wiki
		 * - page name
		 */
		$inData = '<a href="http://tikiwiki.org/tiki-index.php?page=Download" class="wiki ext_page test_ext1">Download</a>';		
		$ex = "(($this->ext1:Download))";
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);			


		/*
		 * External Wiki
		 * - page name
		 * - anchor
		 */
		$inData = '<a href="http://tikiwiki.org/tiki-index.php?page=Download#LTS_-_the_Long_Term_Support_release" class="wiki ext_page test_ext1">Download</a>';
		$ex = "(($this->ext1:Download|#LTS_-_the_Long_Term_Support_release))";
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);			


		/*
		 * External Wiki
		 * - page name
		 * - anchor
		 * - description
		 */
		$inData = '<a href="http://tikiwiki.org/tiki-index.php?page=Download#LTS_-_the_Long_Term_Support_release" class="wiki ext_page test_ext1">Download LTS</a>';	
		$ex = "(($this->ext1:Download|#LTS_-_the_Long_Term_Support_release|Download LTS))";
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);			


		/*
		 * External Wiki
		 * - page name
		 * - additional class name
		 */
		$inData = '<a href="http://tikiwiki.org/tiki-index.php?page=Download" class="wiki ext_page test_ext1 otherclass">Download</a>';		
		$ex = "(($this->ext1:Download))";
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);			


		/*
		 * External Wiki
		 * - page name
		 * - invalid class name
		 */
		$inData = '<a href="http://tikiwiki.org/tiki-index.php?page=Download" class="wiki ext_page invalid">Download</a>';		
		$ex = '[http://tikiwiki.org/tiki-index.php?page=Download|Download]';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);			


		/*
		 * External Wiki
		 * - line breaks
		 */
		$inData = '<a href="http://tikiwiki.org/tiki-index.php?page=Download" class="wiki ext_page test_ext1">Download<br />Download</a><br />Text';		
		$ex = '((' . $this->ext1 . ':Download %%% Download))\nText';
		$out = $this->el->parseToWiki($inData);
		$out = preg_replace('/\n/', '\n', $out); // fix LF encoding for comparison		
		$this->assertEquals($ex, $out);				
	}


	/**
	 * Test link to anchor within a page
	 */	
	function testInPage()
	{

		/*
		 * no description
		 */
		$inData = '<a class="wiki" href="#A_Heading" rel="">#A_Heading</a>';
		$ex = '[#A_Heading]';
		$out = trim($this->el->parseToWiki($inData));
		$this->assertEquals($ex, $out);				


		/*
		 * with description
		 */
		$inData = '<a class="wiki" href="#A_Heading" rel="">Link to heading</a>';
		$ex = '[#A_Heading|Link to heading]';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);


		/*
		 * line breaks
		 */
		$inData = '<a class="wiki" href="#A_Heading" rel="">Link to<br />heading</a><br />Text';
		$ex = '[#A_Heading|Link to %%% heading]\nText';
		$out = $this->el->parseToWiki($inData);
		$out = preg_replace('/\n/', '\n', $out); // fix LF encoding for comparison		
		$this->assertEquals($ex, $out);		
	}


	/**
	 * Test link for creating e-mail
	 */	
	function testMailTo()
	{


		/*
		 * e-mail
		 */
		$inData = '<a class="wiki"  href="mailto:sombody@nowhere.xyz" rel="">mailto:sombody@nowhere.xyz</a>';		
		$ex = '[mailto:sombody@nowhere.xyz]';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);				


		/*
		 * e-mail with description
		 */
		$inData = '<a class="wiki"  href="mailto:sombody@nowhere.xyz" rel="">Mail to "Somebody"</a>';
		$ex = '[mailto:sombody@nowhere.xyz|Mail to "Somebody"]';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);		


		/*
		 * line breaks
		 */
		$inData = '<a class="wiki"  href="mailto:sombody@nowhere.xyz" rel="">Mail to<br />"Somebody"</a><br />Text';
		$ex = '[mailto:sombody@nowhere.xyz|Mail to %%% "Somebody"]\nText';
		$out = $this->el->parseToWiki($inData);
		$out = preg_replace('/\n/', '\n', $out); // fix LF encoding for comparison		
		$this->assertEquals($ex, $out);			
	}


	/**
	 * Test links to articles, blogs, ...
	 */	
	function testOtherTikiPages()
	{


		/*
		 * article
		 */
		$inData = '<a class="wiki"  href="article1" rel="">article1</a>';
		$ex = '[article1]';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);		

		$inData = '<a class="wiki"  href="article1" rel="">An Article</a>';
		$ex = '[article1|An Article]';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);		

		$inData = '<a class="wiki"  href="article1" rel="">An<br />Article</a><br />Text';
		$ex = '[article1|An %%% Article]\nText';
		$out = $this->el->parseToWiki($inData);
		$out = preg_replace('/\n/', '\n', $out); // fix LF encoding for comparison		
		$this->assertEquals($ex, $out);		


		/*
		 * blog
		 */
		$inData = '<a class="wiki"  href="blog1" rel="">blog1</a>';
		$ex = '[blog1]';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);		

		$inData = '<a class="wiki"  href="blog1" rel="">A Blog</a>';
		$ex = '[blog1|A Blog]';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);		

		$inData = '<a class="wiki"  href="blog1" rel="">A<br />Blog</a><br />Text';
		$ex = '[blog1|A %%% Blog]\nText';
		$out = $this->el->parseToWiki($inData);
		$out = preg_replace('/\n/', '\n', $out); // fix LF encoding for comparison		
		$this->assertEquals($ex, $out);				


		/*
		 * forum
		 */
		$inData = '<a class="wiki"  href="forum1" rel="">forum1</a>';
		$ex = '[forum1]';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);		

		$inData = '<a class="wiki"  href="forum1" rel="">A Forum</a>';
		$ex = '[forum1|A Forum]';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);				

		$inData = '<a class="wiki"  href="forum1" rel="">A<br />Forum</a><br />Text';
		$ex = '[forum1|A %%% Forum]\nText';
		$out = $this->el->parseToWiki($inData);
		$out = preg_replace('/\n/', '\n', $out); // fix LF encoding for comparison		
		$this->assertEquals($ex, $out);				
	}


	/**
	 * Test links to web pages
	 */	
	function testWebResource()
	{

		/*
		 * Web Page:
		 * - link
		 */
		$inData = '<a class="wiki external" target="_blank" href="http://www.tiki.org" rel="external nofollow">http://www.tiki.org</a>';
		$ex = '[http://www.tiki.org]';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);			


		/*
		 * Web Page:
		 * - link
		 * - description
		 */
		$inData = '<a class="wiki external" target="_blank" href="http://www.tiki.org" rel="external nofollow">Tiki Wiki CMS Groupware</a>';
		$ex = '[http://www.tiki.org|Tiki Wiki CMS Groupware]';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);			


		/*
		 * Web Page:
		 * - link
		 * - description
		 * - anchor
		 */
		$inData = '<a class="wiki external" target="_blank" href="http://www.tiki.org#Tiki_News_" rel="external nofollow">News of the Tiki Wiki CMS Groupware</a>';
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
		$inData = '<a class="wiki external" target="_blank" href="http://www.tiki.org#Tiki_News_" rel="box external nofollow">News of the Tiki Wiki CMS Groupware</a>';
		$ex = '[http://www.tiki.org#Tiki_News_|News of the Tiki Wiki CMS Groupware|box]';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);			


		/*
		 * Web Page:
		 * - link
		 * - description and linebreak
		 */
		$inData = '<a class="wiki external" target="_blank" href="http://www.tiki.org" rel="external nofollow">Tiki Wiki<br />CMS Groupware</a><br />Text';
		$ex = '[http://www.tiki.org|Tiki Wiki %%% CMS Groupware]\nText';
		$out = $this->el->parseToWiki($inData);
		$out = preg_replace('/\n/', '\n', $out); // fix LF encoding for comparison		
		$this->assertEquals($ex, $out);			


		/*
		 * Link to video
		 * - link
		 * 
		 */
		$inData = '<a class="wiki external" target="_blank" href="http://www.youtube.com/v/KBewVCducWw&amp;autoplay=1" rel="external nofollow">http://www.youtube.com/v/KBewVCducWw&amp;autoplay=1</a>';
		$ex = '[http://www.youtube.com/v/KBewVCducWw&autoplay=1]';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);			


		/*
		 * Link to video
		 * - link
		 * - description
		 */
		$inData = '<a class="wiki external" target="_blank" href="http://www.youtube.com/v/KBewVCducWw&amp;autoplay=1" rel="external nofollow">You Tube video in their flash player</a>';
		$ex = '[http://www.youtube.com/v/KBewVCducWw&autoplay=1|You Tube video in their flash player]';
		$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);			


		/*
		 * Link to video
		 * - link
		 * - description
		 * - box
		 */
		$inData = '<a class="wiki external" target="_blank" href="http://www.youtube.com/v/KBewVCducWw&amp;autoplay=1" rel="box external nofollow">You Tube video in their flash player</a>';
		$ex = '[http://www.youtube.com/v/KBewVCducWw&autoplay=1|You Tube video in their flash player|box]'; // additional nocache does not work
			$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);			


		/*
		 * Link to video
		 * - link
		 * - description
		 * - box with dimensions
		 */
		$inData = '<a class="wiki external" target="_blank" href="http://www.youtube.com/v/KBewVCducWw&amp;autoplay=1" rel="box;width=405;height=340; external nofollow">You Tube video in their flash player</a>';
		$ex = '[http://www.youtube.com/v/KBewVCducWw&autoplay=1|You Tube video in their flash player|box;width=405;height=340;]'; // additional nocache does not work
			$out = $this->el->parseToWiki($inData);
		$this->assertEquals($ex, $out);			
	}


	/**
	 * Test links to internal wiki pages
	 */	
	function testWikiPage()
	{


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
		 */
		$inData = '<a href="tiki-index.php?page=HomePage#Get_Started_using_Admin_Panel" title="HomePage" class="wiki wiki_page">HomePage</a>';		
		$ex = '((HomePage|#Get_Started_using_Admin_Panel))';
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


	/*
	 * Test anchors conversion to {ANAME}
	 */
	function testPluginAname()
	{

		$ex = "{ANAME()}anchor{ANAME}";
		$inData = '<a id="anchor"></a>';
		$out = $this->el->parseToWiki($inData);		
		$this->assertEquals($ex, $out);				
	}	

}
