<?php
class TikiWikiParser_Test extends PHPUnit_Framework_TestCase
{
    /**
     * @covers TikiLib::parse_data
     * @dataProvider provider
     */
	public function testWikiParser($input, $output)
	{
        $o = new TikiLib;
        $this->assertEquals($output, $o->parse_data($input));
	}

    public function provider()
    {
        return array(
          array('', ''),
          array('foo', "foo<br />\n"),
          array('!foo', '<h1 class="showhide_heading" id="foo">foo</h1>' . "\n"),
          array('!!foo', '<h2 class="showhide_heading" id="foo">foo</h2>' . "\n"),
          array('--foo--', "<del>foo</del><br />\n"),
          array('[foo]', '<a class="wiki"  href="foo" rel="">foo</a><br />' . "\n"),
          array('[foo|bar]', '<a class="wiki"  href="foo" rel="">bar</a><br />' . "\n"),
          array("* foo\n* bar\n", "<ul><li> foo\n</li><li> bar\n</li></ul>\n\n"),
        );
    }
}
