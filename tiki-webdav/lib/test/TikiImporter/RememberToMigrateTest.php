<?php

/**
 * @group integration
 */

class TikiImporter_RememberToMigrateTest extends PHPUnit_Framework_TestCase
{
	public function testRememberToMigrate() 
	{
		$message =
			"Integration tests in lib/test/importer are inactive and have been for a while.".
			"Need to migrate them to lib/test/TikiImporter and use the __autoload naming convention.";
		$this->markTestSkipped($message);
	}

    public function provider()
    {
        return array(
          array('', ''),
          array('foo', "foo<br />\n"),
          array('---', "<hr />\n"),
          array('%%%', "<br /><br />\n"), // Line break
          
          //array("''foo''", '<i>foo</i>' . "<br />\n"),

          array('__foo__', "<strong>foo</strong><br />" . "\n"),   // bold
          array('__ foo __', "<strong> foo </strong><br />" . "\n"),   // bold

          array('===foo===', '<span style="text-decoration:underline;">foo</span>' . "<br />" . "\n"), // underline
          array('=== foo ===', '<span style="text-decoration:underline;"> foo </span>' . "<br />" . "\n"), // underline

          array('-=foo=-', '<div class="titlebar">foo</div>' . "\n"),  // title bar
          array('-= foo =-', '<div class="titlebar"> foo </div>' . "\n"),  // title bar

          array('^foo^', '<div class="simplebox">foo</div><br />' . "\n"),  // box
          array('^ foo ^', '<div class="simplebox"> foo </div><br />' . "\n"),  // box

          array('::foo::', '<div style="text-align: center;">foo</div><br />' . "\n"),  // center align
          array(':: foo ::', '<div style="text-align: center;"> foo </div><br />' . "\n"),  // center align

          array('! foo', '<h2 class="showhide_heading" id="foo"> foo</h2>' . "\n"),  // heading 1
          array('!!foo', '<h3 class="showhide_heading" id="foo">foo</h3>' . "\n"),  // heading 2
          array('!! foo', '<h3 class="showhide_heading" id="foo"> foo</h3>' . "\n"),  // heading 2

          array('--foo--', "<del>foo</del><br />\n"),  // strike out
          array('-- foo --', "-- foo --<br />\n"),  // not parsed

          array('[foo]', '<a class="wiki"  href="foo" rel="">foo</a><br />' . "\n"), // link
          array('[foo|bar]', '<a class="wiki"  href="foo" rel="">bar</a><br />' . "\n"), // link
          
			array('[[foo', '[foo<br />' . "\n"), // Square brackets
					
			array('-+foo+- ', '<code>foo</code><br />'. "\n"), // Monospace font
			array('-+ foo +- ', '<code> foo </code><br />'. "\n"), // Monospace font
				
			array('{r2l}foo', "<div dir='rtl'>foo<br />\n</div>"), // Right to left
			array('{l2r}foo', "<div dir='ltr'>foo<br />\n</div>"), // Left to right
			array('{rm}foo', "&rlm;foo<br />\n"),
			array('~amp~foo', "&amp;foo<br />\n"), // Special character &amp;
			array('~hs~foo', "&nbsp;foo<br />\n"), // Hard space 


			array(";foo1:bar1\n;foo2:bar2", "<dl><dt>foo1</dt><dd>bar1</dd></dl><br />\n<dl><dt>foo2</dt><dd>bar2</dd></dl><br />\n"), // Definition list

// These ones are failing for some reason
//					
//          array("* foo\n* bar\n", "<ul><li> foo\n</li><li> bar\n</li></ul>\n\n"), // Bulleted list
//          array("* foo1\n** foo11\n**foo12\n* bar1\n", "<ul><li> foo1\n<ul><li> foo11\n</li><li>foo12\n</li></ul></li><li> bar1\n</li></ul>\n\n"), // Nested Bulleted list
//          array("* foo\n+ Continuation1\n+Continuation2\n* bar\n", "<ul><li> foo\n<br /> Continuation1\n<br />Continuation2\n</li><li> bar\n</li></ul>\n\n"), // Bulleted list with continuation
//
//          array("# foo\n# bar\n", "<ol><li> foo\n</li><li> bar\n</li></ol>\n\n"),  // Numbered list
//          array("# foo1\n## foo11\n##foo12\n# bar1\n", "<ol><li> foo1\n<ol><li> foo11\n</li><li>foo12\n</li></ol></li><li> bar1\n</li></ol>\n\n"),  // Nested Numbered list
//          array("# foo\n+ Continuation1\n+Continuation2\n# bar\n", "<ol><li> foo\n<br /> Continuation1\n<br />Continuation2\n</li><li> bar\n</li></ol>\n\n"), // Numbered list with continuation
//
//          array("||r1c1|r1c2\nr2c1|r2c2||", "||r1c1|r1c2<br />\nr2c1|r2c2||<br />\n"),


        );
    }
}
