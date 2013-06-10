<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Elastic_FacetBuilderTest extends PHPUnit_Framework_TestCase
{
	private $builder;

	function setUp()
	{
		$this->builder = new Search_Elastic_FacetBuilder;
	}

	function testBuildNoFacet()
	{
		$this->assertEquals(array(), $this->builder->build(array()));
	}

	function testBuildSingleFacet()
	{
		$this->assertEquals(array(
			'facets' => array(
				'categories' => array(
					'terms' => array('field' => 'categories'),
				),
			),
		), $this->builder->build(array(
			new Search_Query_Facet_Term('categories'),
		)));
	}

	function testBuildMultipleFacets()
	{
		$this->assertEquals(array(
			'facets' => array(
				'categories' => array(
					'terms' => array('field' => 'categories'),
				),
				'deep_categories' => array(
					'terms' => array('field' => 'deep_categories'),
				),
			),
		), $this->builder->build(array(
			new Search_Query_Facet_Term('categories'),
			new Search_Query_Facet_Term('deep_categories'),
		)));
	}
}

