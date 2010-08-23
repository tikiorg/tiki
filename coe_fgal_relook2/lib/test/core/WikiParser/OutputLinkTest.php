<?php

class WikiParser_OutputLinkTest extends TikiTestCase
{
	private $info;

	function setUp() {
		$this->info = array();
	}

	function testCreateLink() {
		// ((Test)) on missing page
		$link = new WikiParser_OutputLink;
		$link->setIdentifier( 'Test' );
		
		$this->assertXmlStringEqualsXmlString(
			'<span>Test<a href="tiki-editpage.php?page=Test" title="Create page: Test" class="wiki wikinew">?</a></span>',
			'<span>' . $link->getHtml() . '</span>' );
	}

	function testCreateLinkWithLanguage() {
		// ((Test)) on missing page, with multilingual specified
		$link = new WikiParser_OutputLink;
		$link->setIdentifier( 'Test' );
		$link->setLanguage( 'fr' );
		
		$this->assertXmlStringEqualsXmlString(
			'<span>Test<a href="tiki-editpage.php?page=Test&amp;lang=fr" title="Create page: Test" class="wiki wikinew">?</a></span>',
			'<span>' . $link->getHtml() . '</span>' );
	}

	function testCreateLinkWithDescription() {
		// ((Test|Hello World))
		$link = new WikiParser_OutputLink;
		$link->setIdentifier( 'Test' );
		$link->setDescription( 'Hello World' );
		
		$this->assertXmlStringEqualsXmlString(
			'<span>Hello World<a href="tiki-editpage.php?page=Test" title="Create page: Test" class="wiki wikinew">?</a></span>',
			'<span>' . $link->getHtml() . '</span>' );
	}

	function testCreateLinkWithRelationType() {
		// (real(Test))
		$link = new WikiParser_OutputLink;
		$link->setIdentifier( 'Test' );
		$link->setQualifier( 'real' );
		
		$this->assertXmlStringEqualsXmlString(
			'<span>Test<a href="tiki-editpage.php?page=Test" title="Create page: Test" class="wiki wikinew real">?</a></span>',
			'<span>' . $link->getHtml() . '</span>' );
	}

	function testPageDoesExist() {
		$this->info['Test'] = array(
			'pageName' => 'Test',
			'description' => 'Testing',
			'lastModif' => 1234567890,
		);

		$link = new WikiParser_OutputLink;
		$link->setIdentifier( 'Test' );
		$link->setWikiLookup( array( $this, 'getPageInfo' ) );
		$link->setWikiLinkBuilder( array( $this, 'getWikiLink' ) );
		
		$this->assertXmlStringEqualsXmlString(
			'<a href="Test" title="Testing" class="wiki">Test</a>',
			$link->getHtml() );
	}

	function testInfoFunctionProvidesAlias() {
		$this->info['Test'] = array(
			'pageName' => 'Test1.2',
			'description' => 'Testing',
			'lastModif' => 1234567890,
		);

		$link = new WikiParser_OutputLink;
		$link->setIdentifier( 'Test' );
		$link->setWikiLookup( array( $this, 'getPageInfo' ) );
		$link->setWikiLinkBuilder( array( $this, 'getWikiLink' ) );
		
		$this->assertXmlStringEqualsXmlString(
			'<a href="Test1.2" title="Testing" class="wiki">Test</a>',
			$link->getHtml() );
	}

	function testExistsWithRelType() {
		$this->info['Test'] = array(
			'pageName' => 'Test',
			'description' => 'Testing',
			'lastModif' => 1234567890,
		);

		$link = new WikiParser_OutputLink;
		$link->setIdentifier( 'Test' );
		$link->setQualifier( 'abc' );
		$link->setWikiLookup( array( $this, 'getPageInfo' ) );
		$link->setWikiLinkBuilder( array( $this, 'getWikiLink' ) );
		
		$this->assertXmlStringEqualsXmlString(
			'<a href="Test" title="Testing" class="wiki abc">Test</a>',
			$link->getHtml() );
	}

	function testUndefinedExternalLink() {
		$link = new WikiParser_OutputLink;
		$link->setIdentifier( 'out:Test' );
		$link->setWikiLookup( array( $this, 'getPageInfo' ) );
		$link->setWikiLinkBuilder( array( $this, 'getWikiLink' ) );
		
		$this->assertXmlStringEqualsXmlString(
			'<span>out:Test<a href="tiki-editpage.php?page=out%3ATest" title="Create page: out:Test" class="wiki wikinew">?</a></span>',
			'<span>' . $link->getHtml() . '</span>' );
	}

	function testWithDefinedExternal() {
		$link = new WikiParser_OutputLink;
		$link->setIdentifier( 'out:Test' );
		$link->setExternals( array(
			'out' => 'http://example.com/$page',
			'other' => 'http://www.example.com/$page',
		) );
		
		$this->assertXmlStringEqualsXmlString(
			'<a href="http://example.com/Test" class="wiki external">Test</a>',
			$link->getHtml() );
	}

	function testWithDefinedExternalAndDescription() {
		$link = new WikiParser_OutputLink;
		$link->setIdentifier( 'out:Test' );
		$link->setDescription( 'ABC' );
		$link->setExternals( array(
			'out' => 'http://example.com/$page',
			'other' => 'http://www.example.com/$page',
		) );
		
		$this->assertXmlStringEqualsXmlString(
			'<a href="http://example.com/Test" class="wiki external">ABC</a>',
			$link->getHtml() );
	}

	function testHandlePlural() {
		$this->info['Policies'] = false;
		$this->info['Policy'] = array(
			'pageName' => 'Policy',
			'description' => 'Some Page',
			'lastModif' => 1234567890,
		);

		$link = new WikiParser_OutputLink;
		$link->setIdentifier( 'Policies' );
		$link->setWikiLookup( array( $this, 'getPageInfo' ) );
		$link->setWikiLinkBuilder( array( $this, 'getWikiLink' ) );
		$link->setHandlePlurals( true );
		
		$this->assertXmlStringEqualsXmlString(
			'<a href="Policy" title="Some Page" class="wiki">Policies</a>',
			$link->getHtml() );
	}

	function getPageInfo( $page ) {
		if( isset( $this->info[$page] ) ) {
			return $this->info[$page];
		}
	}

	function getWikiLink( $page ) {
		return $page;
	}
}

