<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * @group gui
 */


class AcceptanceTests_SearchTest extends TikiSeleniumTestCase
{
	protected function setUp()
	{
		$this->markTestSkipped("These tests are still too experimental, so skipping it.");
		$this->setBrowserUrl('http://localhost/');
		$this->current_test_db = "searchTestDump.sql";
		$this->restoreDBforThisTest();
	}


	public function ___testRememberToReactivateAllTestsInSearchTest()
	{
		$this->fail("Don't forget to do this");
	}

	/**
	 * @group gui
	 */
	public function testSearchFormIsWellFormed()
	{
		$this->openTikiPage('tiki-index.php');
		$this->logInIfNecessaryAs('admin');
		$this->_assertSearchFormIsWellFormed();
	}

	/**
	 * @group gui
	 */
	public function testFillSearchFormAndSubmit()
	{
		$this->openTikiPage('tiki-index.php');
		$this->logInIfNecessaryAs('admin');
		$query = 'feature';
		//		echo $this->getBodyText();
		$this->_searchFor($query);

		$this->_assertSearchResultsWere(
			array(
				0 => "HomePage",
				1 => 'Multilingual Test Page 1',
				2 => 'Another page containing the word feature'
			),
			$query,
			""
		);
	}


	/**
	 * @group gui
	 */
	public function testSearchIsCaseInsensitive()
	{
		$this->openTikiPage('tiki-index.php');
		$this->logInIfNecessaryAs('admin');
		$query = 'hello';
		$this->_searchFor($query);
		$this->_assertSearchResultsWere(
			array(
				0 => "test page for search 1",
				1 => 'test page for search 2'
			),
			$query,
			"Bad list of search results for query '$query'. Search should have been case insensitive."
		);
	}

	/**
	 * @group gui
	 */
	public function testByDefaultSearchLooksForAnyOfTheQueryTerms()
	{
		$this->openTikiPage('tiki-index.php');
		$this->logInIfNecessaryAs('admin');
		$query = 'hello world';
		$this->_searchFor($query);
		$this->_assertSearchResultsWere(
			array(
				0 => "test page for search 1",
				1 => "test page for search 2",
				2 => 'test page for search 3'
			),
			$query,
			"Bad list of search results for multi word query '$query'. Could be that the search engine did not use an OR to combine the search words."
		);

	}

	/**************************************
	 * Helper methods
	 **************************************/

	private function _searchFor($query)
	{
		$this->type("highlight", $query);
		$this->clickAndWait('search');

	}

	private function _assertSearchFormIsWellFormed()
	{

		$this->assertElementPresent(
			"xpath=//form[@id='search-form']",
			"Search form was not present"
		);

		$this->assertElementPresent(
			"highlight",
			"Search input field not present"
		);

		$this->assertElementPresent(
			"xpath=//div[@id='sitesearchbar']",
			"Site search bar was not present"
		);
	}

	private function _assertSearchResultsWere($listOfHits, $query, $message)
	{
		$this->assertElementPresent(
			"xpath=//ul[@class='searchresults']",
			"List of search results was absent for query '$query'"
		);
		foreach ($listOfHits as $expectedHit) {
			$this->assertElementPresent(
				"link=$expectedHit",
				"$message\nLink to expected hit '$expectedHit' was missing for query '$query'"
			);
		}
	}
}
