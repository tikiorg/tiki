<?php

require_once 'PHPUnit/Framework.php';
require_once '../tikiimporter_wiki_mediawiki.php';

class TikiImporter_Wiki_Mediawiki_Test extends PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        $this->obj = new TikiImporter_Wiki_Mediawiki;
    }

    public function testValidateInput()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    public function testParseData()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    public function testExtractInfo()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    public function testExtractRevision()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    public function testExtractContributor()
    {
        $dom = new DOMDocument;
        $dom->load('fixtures/mediawiki_contributor.xml');
        $expectedResult = array(
            array('user' => 'SomeUserName', 'ip' => '0.0.0.0'),
            array('ip' => '163.117.200.166', 'user' => 'anonymous'),
            array('user' => 'OtherUserName', 'ip' => '0.0.0.0')
        );
        $contributors = $dom->getElementsByTagName('contributor');

        $i = 0;
        foreach ($contributors as $contributor) {
            $this->assertEquals($expectedResult[$i++], $this->obj->extractContributor($contributor));
        }
    }

    // TODO: find a way to mock the Text_Wiki object inside convertMakup()
    public function testConvertMarkup()
    {
        $mediawikiText = '[[someWikiLink]]';
        $expectedResult = "((someWikiLink))\n\n";

        $this->assertEquals($expectedResult, $this->obj->convertMarkup($mediawikiText));
    }

    public function testConvertMarkupShouldReturnNullIfEmptyMediawikiText()
    {
        $mediawikiText = '';
        $this->assertNull($this->obj->convertMarkup($mediawikiText));
    }
}

?>
