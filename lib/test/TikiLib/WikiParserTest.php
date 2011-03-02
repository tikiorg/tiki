<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * @group integration
 * 
 */

class TikiLib_WikiParserTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers TikiLib::parse_data
	 * @dataProvider provider
	 */
	public function testWikiParser($input, $output, $options = array())
	{
		global $prefs;
		$prefs['feature_page_title'] = 'y';
		$prefs['feature_wiki_paragraph_formatting'] = 'n';
		$prefs['pass_chr_special'] = 'n';
		$o = new TikiLib;
		$this->assertEquals($output, $o->parse_data($input, $options));
	}

	public function provider()
	{
		return array(
			array('', ''),
			array('foo', "foo<br />\n"),
			array('---', "<hr />\n"),
			array('%%%', "<br /><br />\n"), // Line break
			
			array("''foo''", '<em>foo</em>' . "<br />\n"),

			array('__foo__', "<strong>foo</strong><br />" . "\n"),	 // bold
			array('__ foo __', "<strong> foo </strong><br />" . "\n"),	 // bold

			array('===foo===', '<span style="text-decoration:underline;">foo</span>' . "<br />" . "\n"), // underline
			array('=== foo ===', '<span style="text-decoration:underline;"> foo </span>' . "<br />" . "\n"), // underline

			array('-=foo=-', '<div class="titlebar">foo</div>' . "\n"),	// title bar
			array('-= foo =-', '<div class="titlebar"> foo </div>' . "\n"),	// title bar

			array('^foo^', '<div class="simplebox">foo</div><br />' . "\n"),	// box
			array('^ foo ^', '<div class="simplebox"> foo </div><br />' . "\n"),	// box

			array('::foo::', '<div style="text-align: center;">foo</div><br />' . "\n"),	// center align
			array(':: foo ::', '<div style="text-align: center;"> foo </div><br />' . "\n"),	// center align

			array('! foo', '<h2 class="showhide_heading" id="foo"> foo</h2>' . "\n"),	// heading 1
			array('!!foo', '<h3 class="showhide_heading" id="foo">foo</h3>' . "\n"),	// heading 2
			array('!! foo', '<h3 class="showhide_heading" id="foo"> foo</h3>' . "\n"),	// heading 2

			//heading 1 with collapsible text open
			array(
				"!+foo\nheading text section",
				"<h2 class=\"showhide_heading\" id=\"foo\">foo</h2><a id=\"flipperidHomePage1\" class=\"link\" href=\"javascript:flipWithSign('idHomePage1')\">[-]</a><div id=\"idHomePage1\" class=\"showhide_heading\" style=\"display:block;\">\nheading text section<br />\n</div>",
				array('page' => 'HomePage'),
			),
			
			//heading 1 with collapsible text closed
			array(
				"!-foo\nheading text section",
				"<h2 class=\"showhide_heading\" id=\"foo\">foo</h2><a id=\"flipperidHomePage1\" class=\"link\" href=\"javascript:flipWithSign('idHomePage1')\">[+]</a><div id=\"idHomePage1\" class=\"showhide_heading\" style=\"display:none;\">\nheading text section<br />\n</div>",
				array('page' => 'HomePage'),
			),			
			
			array('--foo--', "<del>foo</del><br />\n"),	// strike out
			array('-- foo --', "-- foo --<br />\n"),	// not parsed

			array('[foo]', '<a class="wiki"  href="foo" rel="">foo</a><br />' . "\n"), // link
			array('[foo|bar]', '<a class="wiki"  href="foo" rel="">bar</a><br />' . "\n"), // link
			
			array('[[foo', '[foo<br />' . "\n"), // Square brackets
			array('[[foo]]', '[[foo]]<br />' . "\n"), // Square brackets
			array('[[foo]', '[foo]<br />' . "\n"), // Square brackets
					
			array('-+foo+- ', '<code>foo</code><br />'. "\n"), // Monospace font
			array('-+ foo +- ', '<code> foo </code><br />'. "\n"), // Monospace font
				
			array('{r2l}foo', "<div dir='rtl'>foo<br />\n</div>"), // Right to left
			array('{l2r}foo', "<div dir='ltr'>foo<br />\n</div>"), // Left to right
			array('{rm}foo', "&rlm;foo<br />\n"),
			array('~amp~foo', "&amp;foo<br />\n"), // Special character &amp;
			array('~hs~foo', "&nbsp;foo<br />\n"), // Hard space 

			array(";foo1:bar1\n;foo2:bar2", "<dl><dt>foo1</dt><dd>bar1</dd></dl><br />\n<dl><dt>foo2</dt><dd>bar2</dd></dl><br />\n"), // Definition list

			array("* foo\n* bar\n", "<ul><li> foo\n</li><li> bar\n</li></ul><br />\n"), // Bulleted list
			array("* foo1\n** foo11\n**foo12\n* bar1\n", "<ul><li> foo1\n<ul><li> foo11\n</li><li>foo12\n</li></ul></li><li> bar1\n</li></ul><br />\n"), // Nested Bulleted list
			array("* foo\n+ Continuation1\n+Continuation2\n* bar\n", "<ul><li> foo\n<br /> Continuation1\n<br />Continuation2\n</li><li> bar\n</li></ul><br />\n"), // Bulleted list with continuation

			array("# foo\n# bar\n", "<ol><li> foo\n</li><li> bar\n</li></ol><br />\n"),	// Numbered list
			array("# foo1\n## foo11\n##foo12\n# bar1\n", "<ol><li> foo1\n<ol><li> foo11\n</li><li>foo12\n</li></ol></li><li> bar1\n</li></ol><br />\n"),	// Nested Numbered list
			array("# foo\n+ Continuation1\n+Continuation2\n# bar\n", "<ol><li> foo\n<br /> Continuation1\n<br />Continuation2\n</li><li> bar\n</li></ol><br />\n"), // Numbered list with continuation

			array("||r1c1|r1c2\nr2c1|r2c2||", '<table class="wikitable"><tr><td class="wikicell" >r1c1</td><td class="wikicell" >r1c2</td></tr><tr><td class="wikicell" >r2c1</td><td class="wikicell" >r2c2</td></tr></table><br />' . "\n"),
			array("~pp~foo~/pp~", "<pre>foo</pre><br />\n"),
		);
	}
}
