<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
	 * @covers ParserLib::parse_data
	 * @dataProvider provider
	 */
	public function testWikiParser($input, $output, $options = [])
	{
		global $prefs;
		$prefs['feature_page_title'] = 'y';
		$prefs['feature_wiki_paragraph_formatting'] = 'n';
		$prefs['pass_chr_special'] = 'n';
		$prefs['wiki_heading_links'] = 'n';
		$this->assertEquals($output, TikiLib::lib('parser')->parse_data($input, $options));
	}

	public function provider()
	{
		return [
			['', ''],
			['foo', "foo<br />"],
			['---', "<hr />"],
			['%%%', "<br /><br />"], // Line break

			["''foo''", '<em>foo</em>' . "<br />"],

			['__foo__', "<strong>foo</strong><br />"],	 // bold
			['__ foo __', "<strong> foo </strong><br />"],	 // bold

			['===foo===', '<u>foo</u>' . "<br />"], // underline
			['=== foo ===', '<u> foo </u>' . "<br />"], // underline

			['-=foo=-', '<div class="titlebar">foo</div>' . "\n"],	// title bar
			['-= foo =-', '<div class="titlebar"> foo </div>' . "\n"],	// title bar

			['^foo^', '<div class="well">foo</div><br />'],	// box
			['^ foo ^', '<div class="well"> foo </div><br />'],	// box

			['::foo::', '<div style="text-align: center;">foo</div><br />'],	// center align
			[':: foo ::', '<div style="text-align: center;"> foo </div><br />'],	// center align

			['! foo', '<h1 class="showhide_heading" id="foo"> foo</h1>'],	// heading 1
			['!!foo', '<h2 class="showhide_heading" id="foo">foo</h2>'],	// heading 2
			['!! foo', '<h2 class="showhide_heading" id="foo"> foo</h2>'],	// heading 2

			//heading 1 with collapsible text open
			[
				"!+foo\nheading text section",
				"<h1 class=\"showhide_heading\" id=\"foo\">foo</h1><a id=\"flipperidHomePage1\" class=\"link\" href=\"#\" onclick=\"flipWithSign('idHomePage1');return false;\">[-]</a><div id=\"idHomePage1\" class=\"showhide_heading\" style=\"display:block;\">\nheading text section<br /></div>",
				['page' => 'HomePage'],
			],

			//heading 1 with collapsible text closed
			[
				"!-foo\nheading text section",
				"<h1 class=\"showhide_heading\" id=\"foo\">foo</h1><a id=\"flipperidHomePage1\" class=\"link\" href=\"#\" onclick=\"flipWithSign('idHomePage1');return false;\">[+]</a><div id=\"idHomePage1\" class=\"showhide_heading\" style=\"display:none;\">\nheading text section<br /></div>",
				['page' => 'HomePage'],
			],

			['--foo--', "<strike>foo</strike><br />"],	// strike out
			['-- foo --', "-- foo --<br />"],	// not parsed

			['[foo]', '<a class="wiki"  href="foo" rel="">foo</a><br />'], // link
			['[foo|bar]', '<a class="wiki"  href="foo" rel="">bar</a><br />'], // link

			['[[foo', '[foo<br />'], // Square brackets
			['[[foo]]', '[[foo]]<br />'], // Square brackets
			['[[foo]', '[foo]<br />'], // Square brackets

			['-+foo+- ', '<code>foo</code><br />' . ""], // Monospace font
			['-+ foo +- ', '<code> foo </code><br />' . ""], // Monospace font

			['{r2l}foo', "<div dir='rtl'>foo<br /></div>"], // Right to left
			['{l2r}foo', "<div dir='ltr'>foo<br /></div>"], // Left to right
			['{rm}foo', "&rlm;foo<br />"],
			['~amp~foo', "&amp;foo<br />"], // Special character &amp;
			['~hs~foo', "&nbsp;foo<br />"], // Hard space

			[";foo1:bar1\n;foo2:bar2", "<dl><dt>foo1</dt><dd>bar1</dd></dl><br />\n<dl><dt>foo2</dt><dd>bar2</dd></dl><br />"], // Definition list

			["* foo\n* bar\n", "<ul><li> foo\n</li><li> bar\n</li></ul><br />"], // Bulleted list
			["* foo1\n** foo11\n**foo12\n* bar1\n", "<ul><li> foo1\n<ul><li> foo11\n</li><li>foo12\n</li></ul></li><li> bar1\n</li></ul><br />"], // Nested Bulleted list
			["* foo\n+ Continuation1\n+Continuation2\n* bar\n", "<ul><li> foo\n<br /> Continuation1\n<br />Continuation2\n</li><li> bar\n</li></ul><br />"], // Bulleted list with continuation

			["# foo\n# bar\n", "<ol><li> foo\n</li><li> bar\n</li></ol><br />"],	// Numbered list
			["# foo1\n## foo11\n##foo12\n# bar1\n", "<ol><li> foo1\n<ol><li> foo11\n</li><li>foo12\n</li></ol></li><li> bar1\n</li></ol><br />"],	// Nested Numbered list
			["# foo\n+ Continuation1\n+Continuation2\n# bar\n", "<ol><li> foo\n<br /> Continuation1\n<br />Continuation2\n</li><li> bar\n</li></ol><br />"], // Numbered list with continuation

			["||r1c1|r1c2\nr2c1|r2c2||", '<table class="wikitable table table-striped table-hover"><tr><td class="wikicell" >r1c1</td><td class="wikicell" >r1c2</td></tr><tr><td class="wikicell" >r2c1</td><td class="wikicell" >r2c2</td></tr></table><br />'],
			["~pp~foo~/pp~", "<pre>foo</pre><br />"],
		];
	}
}
