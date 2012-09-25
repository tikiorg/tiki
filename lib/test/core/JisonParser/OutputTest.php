<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class JisonParser_OutputTest extends JisonParser_Abstract
{
	function provider()
	{
		$this->syntaxSets = array(
			'simple_break'      => array(),
			//old syntax checks, somewhat modified
			array('! foo', '<h1 class="showhide_heading" id="foo"> foo</h1>'),	// heading 1
			array('!!foo', '<h2 class="showhide_heading" id="foo1">foo</h2>'),	// heading 2
			array('!! foo', '<h2 class="showhide_heading" id="foo2"> foo</h2>'),	// heading 2

			//heading 1 with collapsible text open
			/*array(
				"!+foo\nheading text section",
				"<h1 class=\"showhide_heading\" id=\"foo\">foo</h1><a id=\"flipperidHomePage1\" class=\"link\" href=\"javascript:flipWithSign('idHomePage1')\">[-]</a><div id=\"idHomePage1\" class=\"showhide_heading\" style=\"display:block;\">\nheading text section<br />\n</div>",
				array('page' => 'HomePage'),
			),

			//heading 1 with collapsible text closed
			array(
				"!-foo\nheading text section",
				"<h1 class=\"showhide_heading\" id=\"foo\">foo</h1><a id=\"flipperidHomePage1\" class=\"link\" href=\"javascript:flipWithSign('idHomePage1')\">[+]</a><div id=\"idHomePage1\" class=\"showhide_heading\" style=\"display:none;\">\nheading text section<br />\n</div>",
				array('page' => 'HomePage'),
			),*/

			"strike_out" => array('--foo--', "<strike>foo</strike>"),
			"not_parsed" => array('-- foo --', "-- foo --"),

			array('[[foo', '[foo'), // Square brackets
			array('[[foo]]', '[[foo]]'), // Square brackets
			array('[[foo]', '[foo]'), // Square brackets

			array('-+foo+- ', '<code>foo</code> '), // Monospace font
			array('-+ foo +- ', '<code> foo </code> '), // Monospace font

			array('{r2l}foo', "<div dir='rtl'>foo</div>"), // Right to left
			array('{l2r}foo', "<div dir='ltr'>foo</div>"), // Left to right
			array('{rm}foo', "&rlm;foo"),
			array('~amp~foo', "&amp;foo"), // Special character &amp;
			array('~hs~foo', "&nbsp;foo"), // Hard space

			"definition_list" => array(),

			"old_bulleted_list1" => array(),
			"old_bulleted_list2" => array(),
			"old_bulleted_list3" => array(),

			"old_numbered_list1" => array(),
			"old_numbered_list2" => array(),
			"old_numbered_list3" => array(),

			array("||r1c1|r1c2\nr2c1|r2c2||", '<table class="wikitable"><tr><td class="wikicell">r1c1</td><td class="wikicell">r1c2</td></tr><tr><td class="wikicell">r2c1</td><td class="wikicell">r2c2</td></tr></table>'),
			array("~pp~foo~/pp~", "<pre>foo</pre>"),

			//empty or no parse
			"empty" => array('', ''),
			"no_parse" => array('foo', "foo"),

			//good state tracking syntax
			'italic'            => array("''text''", '<em>text</em>'),
			'bold'              => array('__text__', '<strong>text</strong>'),
			'bold_spaces'       => array('__ text __', '<strong> text </strong>'),
			'linethrough'       => array('--text--', '<strike>text</strike>'),
			'box'               => array('^text^', '<div class="simplebox">text</div>'),
			'box_spaces'        => array('^ text ^', '<div class="simplebox"> text </div>'),
			'center'            => array('::text::', '<div style="text-align: center;">text</div>'),
			'center_spaces'     => array(':: text ::', '<div style="text-align: center;"> text </div>'),
			'underscore'        => array('===text===', '<u>text</u>'),
			'underscore_spaces' => array('=== text ===', '<u> text </u>'),
			'titlebar'          => array("-=text=-", '<div class="titlebar">text</div>'),
			'titlebar_spaces'   => array("-= text =-", '<div class="titlebar"> text </div>'),
			'color_text1'       => array('~~red:text~~', '<span style="color: red;">text</span>'),
			'color_text2'       => array('~~#ff00ff:text~~', '<span style="color: #ff00ff;">text</span>'),
			'htmllink'          => array("[www.google.com]", '<a class="wiki" href="www.google.com">www.google.com</a>'),
			'htmllink1'         => array("[www.google.com|Google]", '<a class="wiki" href="www.google.com">Google</a>'),
			'wikilink'          => array("((Wiki Page))", '<a class="wiki" title="Wiki Page" href="tiki-index.php?page=Wiki Page">Wiki Page</a>'),
			'table'             => array("||A1|B1|C1\nA2|B2|C2||", '<table class="wikitable"><tr><td class="wikicell">A1</td><td class="wikicell">B1</td><td class="wikicell">C1</td></tr><tr><td class="wikicell">A2</td><td class="wikicell">B2</td><td class="wikicell">C2</td></tr></table>'),


			//error recovery state tracking syntax
			'italic_r'          => array("''text", '<em>text</em>'),
			'bold_r'            => array('__text', '<strong>text</strong>'),
			'linethrough_r'     => array('--text', '<strike>text</strike>'),
			'box_r'             => array('^text', '<div class="simplebox">text</div>'),
			'center_r'          => array('::text', '<div style="text-align: center;">text</div>'),
			'underscore_r'      => array('===text', '<u>text</u>'),
			'titlebar_r'        => array("-=text", '<div class="titlebar">text</div>'),
			'color_text1_r'     => array('~~red:text', '<span style="color: red;">text</span>'),
			'color_text2_r'     => array('~~#ff00ff:text', '<span style="color: #ff00ff;">text</span>'),
			'htmllink_r'        => array("[www.google.com|Google", '<a class="wiki" href="www.google.com">Google</a>'),
			'wikilink_r'        => array("((Wiki Page", '<a class="wiki" title="Wiki Page" href="tiki-index.php?page=Wiki Page">Wiki Page</a>'),
			'table_r'           => array("||A1|B1|C1\nA2|B2|C2", '<table class="wikitable"><tr><td class="wikicell">A1</td><td class="wikicell">B1</td><td class="wikicell">C1</td></tr><tr><td class="wikicell">A2</td><td class="wikicell">B2</td><td class="wikicell">C2</td></tr></table>'),

			'wikilink_nested'   => array("(([Linked]))", '<a class="wiki" title="[Linked]" href="tiki-index.php?page=[Linked]">[Linked]</a>'),
			'htmllink_nested'   => array("[((Linked))]", '<a class="wiki" href="((Linked))">((Linked))</a>'),

			//non state tracking syntax
			"forced_break"      => array('%%%', "<br />"),
			'horizontal_line'   => array('---', "<hr />"),

			'np'                => array('~np~~np~--np--~/np~~/np~', '~np~--np--~/np~'),
			'tc'                => array('~tc~text~/tc~', ''),
			'amp'               => array('&', '&amp;'),
			'bs'                => array('~bs~', '&#92;'),
			'hs'                => array('~hs~', '&nbsp;'),
			'amp2'              => array('~amp~', '&amp;'),
			'ldg'               => array('~ldq~', '&ldquo;'),
			'rdq'               => array('~rdq~', '&rdquo;'),
			'lsq'               => array('~lsq~', '&lsquo;'),
			'rsq'               => array('~rsq~', '&rsquo;'),
			'c'                 => array('~c~', '&copy;'),
			'dash1'             => array('~--~', '&mdash;'),
			'dash2'             => array(' -- ', ' &mdash; '),
			'lt'                => array('~lt~', '&lt;'),
			'gt'                => array('~gt~', '&gt;'),
			//$content = preg_replace("/~([0-9]+)~/", "&#$1;", $content);

			//block level syntax
			'header1'           => array('!header1', '<h1 class="showhide_heading" id="header1">header1</h1>'),
			'header2'           => array('!!header2', '<h2 class="showhide_heading" id="header2">header2</h2>'),
			'header3'           => array('!!!header3', '<h3 class="showhide_heading" id="header3">header3</h3>'),
			'header4'           => array('!!!!header4', '<h4 class="showhide_heading" id="header4">header4</h4>'),
			'header5'           => array('!!!!!header5', '<h5 class="showhide_heading" id="header5">header5</h5>'),
			'header6'           => array('!!!!!!header6', '<h6 class="showhide_heading" id="header6">header6</h6>'),
			'header7'           => array('!!!!!!!header7', '<h6 class="showhide_heading" id="header7">header7</h6>'),//max is 6

			'list1'             => array(),
			'list2'             => array(),
			'list3'             => array(),
			'list4'             => array(),

			'listnested1'       => array(),
			'listnested2'       => array(),

			//html output
			'html_not_allowed'  => array(),
			'html_allowed'  => array(),
			'complex_state_tracking' => array(),
			'no_line_skipping' => array(),
		);
	}

	function definition_list()
	{
		$syntax = array(
			";foo1:bar1\n" .
			";foo2:bar2"
		,
			'<dl class="tikiList" id="" style="">' .
				'<dt>foo1</dt><dd>bar1</dd>' . "\n" .
				'<dt>foo2</dt><dd>bar2</dd>' . "\n" .
			'</dl>'
		);

		$parsed = $this->parser->parse($syntax[0]);
		$this->tryRemoveIdsFromHtmlList($parsed);

		return array("parsed" => $parsed, "syntax" => $syntax);
	}

	function old_bulleted_list1()
	{
		$syntax = array(
			"* foo\n" .
			"* bar"
		,
			'<ul class="tikiList" id="" style="">' .
				'<li class="tikiListItem"> foo' . '</li>' . "\n" .
				'<li class="tikiListItem"> bar' . '</li>' . "\n" .
			'</ul>'
		);

		$parsed = $this->parser->parse($syntax[0]);
		$this->tryRemoveIdsFromHtmlList($parsed);

		return array("parsed" => $parsed, "syntax" => $syntax);
	}

	function old_bulleted_list2()
	{
		$syntax = array(
			"* foo1\n" .
			"** foo11\n" .
			"**foo12\n" .
			"* bar1\n"
		,
			'<ul class="tikiList" id="" style="">' .
				'<li class="tikiListItem"> foo1' .
					'<ul class="tikiList" id="" style="">' .
						'<li class="tikiListItem"> foo11</li>' . "\n" .
						'<li class="tikiListItem">foo12</li>'. "\n" .
					'</ul>' .
				'</li>'. "\n" .
				'<li class="tikiListItem"> bar1</li>' . "\n" .
			'</ul>'
		);

		$parsed = $this->parser->parse($syntax[0]);
		$this->tryRemoveIdsFromHtmlList($parsed);

		return array("parsed" => $parsed, "syntax" => $syntax);
	}

	function old_bulleted_list3()
	{
		$syntax = array(
			"* foo\n" .
			"+ Continuation1\n" .
			"+Continuation2\n" .
			"* bar\n"
		,
			'<ul class="tikiList" id="" style="">' .
				'<li class="tikiListItem"> foo' .
					"<br /> Continuation1\n" .
					"<br />Continuation2\n" .
				"</li>\n" .
				'<li class="tikiListItem"> bar</li>' . "\n" .
			"</ul>"
		);

		$parsed = $this->parser->parse($syntax[0]);
		$this->tryRemoveIdsFromHtmlList($parsed);

		return array("parsed" => $parsed, "syntax" => $syntax);
	}

	function old_numbered_list1()
	{
		$syntax = array(
			"# foo\n" .
			"# bar\n"
		,
			'<ol class="tikiList" id="" style="">' .
				'<li class="tikiListItem"> foo</li>' . "\n" .
				'<li class="tikiListItem"> bar</li>' . "\n" .
			"</ol>"
		);

		$parsed = $this->parser->parse($syntax[0]);
		$this->tryRemoveIdsFromHtmlList($parsed);

		return array("parsed" => $parsed, "syntax" => $syntax);
	}

	function old_numbered_list2()
	{
		$syntax = array(
			"# foo1\n" .
			"## foo11\n" .
			"##foo12\n" .
			"# bar1\n"
		,
			'<ol class="tikiList" id="" style="">' .
				'<li class="tikiListItem"> foo1' .
					'<ol class="tikiList" id="" style="">' .
						'<li class="tikiListItem"> foo11</li>' . "\n" .
						'<li class="tikiListItem">foo12</li>' . "\n" .
					'</ol>' .
				'</li>' . "\n" .
				'<li class="tikiListItem"> bar1</li>' . "\n" .
			'</ol>'
		);

		$parsed = $this->parser->parse($syntax[0]);
		$this->tryRemoveIdsFromHtmlList($parsed);

		return array("parsed" => $parsed, "syntax" => $syntax);
	}

	function old_numbered_list3()
	{
		$syntax = array(
			"# foo\n" .
			"+ Continuation1\n" .
			"+Continuation2\n" .
			"# bar\n"
		,
			'<ol class="tikiList" id="" style="">' .
				'<li class="tikiListItem"> foo' .
					"<br /> Continuation1\n" .
					"<br />Continuation2\n" .
				"</li>\n" .
				'<li class="tikiListItem"> bar</li>' . "\n" .
			"</ol>"
		);

		$parsed = $this->parser->parse($syntax[0]);
		$this->tryRemoveIdsFromHtmlList($parsed);

		return array("parsed" => $parsed, "syntax" => $syntax);
	}

	function simple_break()
	{
		$syntax = array(
			"\n" .
			"text\n"
		,
			"<br />\n" .
			"text<br />\n"
		); //a block is open content close, or "\ncontent\n" so a single block should only have 1 br

		$parsed = $this->parser->parse($syntax[0]);

		return array("parsed" => $parsed, "syntax" => $syntax);
	}

	function list1()
	{
		$syntax = array(
			"*line 1\n" .
			"*line 2\n" .
			"*line 3"
		,
			'<ul class="tikiList" id="" style="">' .
				'<li class="tikiListItem">line 1</li>' . "\n" .
				'<li class="tikiListItem">line 2</li>' . "\n" .
				'<li class="tikiListItem">line 3</li>' . "\n" .
			'</ul>'
		);

		$parsed = $this->parser->parse($syntax[0]);
		$this->tryRemoveIdsFromHtmlList($parsed);

		return array("parsed" => $parsed, "syntax" => $syntax);
	}

	function list2()
	{
		$syntax = array(
			"#line 1\n" .
			"#line 2\n" .
			"#line 3"
		,
			'<ol class="tikiList" id="" style="">' .
				'<li class="tikiListItem">line 1</li>' . "\n" .
				'<li class="tikiListItem">line 2</li>' . "\n" .
				'<li class="tikiListItem">line 3</li>' . "\n" .
			'</ol>'
		);

		$parsed = $this->parser->parse($syntax[0]);
		$this->tryRemoveIdsFromHtmlList($parsed);

		return array("parsed" => $parsed, "syntax" => $syntax);
	}

	function list3()
	{
		$syntax = array(
			"*line 1\n" .
			"*line 2\n" .
			"+line 3"
		,
			'<ul class="tikiList" id="" style="">' .
				'<li class="tikiListItem">line 1</li>' . "\n" .
				'<li class="tikiListItem">line 2' .
					'<br />line 3' . "\n" .
				'</li>' . "\n" .
			'</ul>'
		);

		$parsed = $this->parser->parse($syntax[0]);
		$this->tryRemoveIdsFromHtmlList($parsed);

		return array("parsed" => $parsed, "syntax" => $syntax);
	}

	function list4()
	{
		$syntax = array(
			"+line 1\n" .
			"*line 2\n" .
			"+line 3"
		,
			'<ul class="tikiList" id="" style="">' .
				'<li class="tikiListItem">line 1</li>' . "\n" .
				'<li class="tikiListItem">line 2' .
				'<br />line 3' . "\n" .
				'</li>' . "\n" .
			'</ul>'
		);

		$parsed = $this->parser->parse($syntax[0]);
		$this->tryRemoveIdsFromHtmlList($parsed);

		return array("parsed" => $parsed, "syntax" => $syntax);
	}

	function listnested1()
	{
		$syntax = array(
			"*line 1\n" .
			"**line 2\n" .
			"***line 3\n" .
			"**line 4\n" .
			"*line 5\n" .
			"**line 6\n" .
			"***line 7\n" .
			"**line 8\n" .
			"*line 9"
		,
			'<ul class="tikiList" id="" style="">' .
				'<li class="tikiListItem">line 1' .
					'<ul class="tikiList" id="" style="">' .
						'<li class="tikiListItem">line 2' .
							'<ul class="tikiList" id="" style="">' .
								'<li class="tikiListItem">line 3</li>' . "\n" .
							'</ul>' .
						'</li>' . "\n" .
						'<li class="tikiListItem">line 4</li>' . "\n" .
					'</ul>' .
				'</li>' . "\n" .
				'<li class="tikiListItem">line 5' .
					'<ul class="tikiList" id="" style="">' .
						'<li class="tikiListItem">line 6' .
							'<ul class="tikiList" id="" style="">' .
								'<li class="tikiListItem">line 7</li>' . "\n" .
							'</ul>' .
						'</li>' . "\n" .
						'<li class="tikiListItem">line 8</li>' . "\n" .
					'</ul>' .
				'</li>' . "\n" .
				'<li class="tikiListItem">line 9</li>' . "\n" .
			'</ul>'
		);

		$parsed = $this->parser->parse($syntax[0]);
		$this->tryRemoveIdsFromHtmlList($parsed);

		return array("parsed" => $parsed, "syntax" => $syntax);
	}

	function listnested2()
	{
		$syntax = array(
			"*line 1\n" .
			"##line 2\n" .
			"##line 3\n" .
			"**-line 4"
		,
			'<ul class="tikiList" id="" style="">' .
				'<li class="tikiListItem">line 1<br />' . "\n" .
					'<a id="fillerid" href="javascript:flipWithSign(' . "''" . ');" class="link">[+]</a>' .
					'<ol class="tikiList" id="" style="display: none;">' .
						'<li class="tikiListItem">line 2</li>' . "\n" .
						'<li class="tikiListItem">line 3</li>' . "\n" .
						'<li class="tikiListItem">line 4</li>' . "\n" .
					'</ol>' .
				'</li>' . "\n" .
			'</ul>'
		);

		$parsed = $this->parser->parse($syntax[0]);
		$this->tryRemoveIdsFromHtmlList($parsed);
		$parsed = preg_replace('/flipperid[0-9]+/', 'fillerid', $parsed);
		$parsed = preg_replace("/flipWithSign[(][']id[0-9]+['][)]/", "flipWithSign('')", $parsed);

		return array("parsed" => $parsed, "syntax" => $syntax);
	}

	function html_not_allowed()
	{

		$is_html = $this->parser->getOption('is_html');

		$this->parser->setOption(array('is_html' => false));
		$syntax = array(
			"\n" .
			"<div>\n" .
				"html not allowed\n" .
				"<script>\n" .
				"</script>\n" .
			"</div>\n" . //we should get a break here, between block tags
			"<script>\n" .
			"</script>\n" .
			"<span>give me a break</span>\n"
		,
			"<br />\n" .
			"&lt;div&gt;\n" .
				"html not allowed\n" .
				"&lt;script&gt;\n" .
				"&lt;/script&gt;\n" .
			"&lt;/div&gt;\n" . //<-- note, break here
			"&lt;script&gt;\n" .
			"&lt;/script&gt;<br />\n" .
			"&lt;span&gt;give me a break&lt;/span&gt;<br />\n"
		);

		$parsed = $this->parser->parse($syntax[0]);

		$result = array("parsed" => $parsed, "syntax" => $syntax);

		$this->parser->setOption(array('is_html' => $is_html));

		return $result;
	}

	function html_allowed()
	{
		$this->parser->setOption(array('is_html' => true));

		$syntax = array(
			"\n" .
			"<div>html allowed\n" .
				"<script>\n\n\n</script>\n" .
			"</div>\n\n"
		,
			"<br />\n" .
			"<div>html allowed\n" .
				"<script>\n\n\n</script>\n" .
			"</div>\n" .
			"<br />\n"
		);

		$parsed = $this->parser->parse($syntax[0]);

		$result = array("parsed" => $parsed, "syntax" => $syntax);

		$this->parser->resetOption();

		return $result;
	}

	function complex_state_tracking()
	{
		$this->parser->setOption(array('is_html' => true));

		$syntax = array(
			"__Here\n" .
				"''we are test\n" .
					"^testing state^\n" .
				"''\n" .
				"===Tracking===\n" .
				"--it can get--\n" .
				"::complex at times, so we want it\n" .
					"~~purple:to be right\n" .
						"^even in complex scenarios^\n" .
						"so that it is easy on the end user~~\n" .
					"See how we can handle multi lines easily?\n" .
				"::.\n" .
				"This should be bold\n\n"
			,
				"<strong>Here<br />\n" .
					"<em>we are test<br />\n" .
						"<div class=" . '"' . "simplebox" . '"' . ">testing state</div><br />\n" .
					"</em><br />\n" .
					"<u>Tracking</u><br />\n" .
					"<strike>it can get</strike><br />\n" .
					'<div style="text-align: center;">complex at times, so we want it' . "<br />\n" .
						"<span style=" . '"' . "color: purple;" . '"' . ">to be right<br />\n" .
						"<div class=" . '"' . "simplebox" . '"' . ">even in complex scenarios</div><br />\n" .
						"so that it is easy on the end user</span><br />\n" .
						"See how we can handle multi lines easily?<br />\n" .
					"</div>.<br />\n" .
					"This should be bold<br />\n" .
					"<br />\n" .
				"</strong>"
			); //this is detected as open and auto closed.

		$parsed = $this->parser->parse($syntax[0]);

		$result = array("parsed" => $parsed, "syntax" => $syntax);

		$this->parser->resetOption();

		return $result;
	}

	function no_line_skipping()
	{
		$this->parser->setOption(array('is_html' => true));

		$syntax = array(
			"{DIV()}\n" .
			"{DIV()}\n" .
			"{DIV()}\n" .
			"{DIV()}\n" .
			"{DIV}{DIV}{DIV}{DIV}\n" .
			";foo:foo definition\n" .
			";foo2:foo2 definition\n" .
			"[[__bold__]\n" .
			"Test" .
			"''Test Italics''\n"
		,
			"<div  >\n" .
			"<div  >\n" .
			"<div  >\n" .
			"<div  >\n" .
			"</div></div></div></div>" .
			'<dl class="tikiList" id="" style=""><dt>foo</dt><dd>foo definition</dd>' . "\n" .
			"<dt>foo2</dt><dd>foo2 definition</dd>\n" .
			"</dl>\n" .
			"[<strong>bold</strong>]<br />\n" .
			"Test<em>Test Italics</em><br />\n"
		);


		$parsed = $this->parser->parse($syntax[0]);

		$this->tryRemoveIdsFromHtmlList($parsed);

		$result = array("parsed" => $parsed, "syntax" => $syntax);

		$this->parser->resetOption();

		return $result;
	}
}

