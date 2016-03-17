<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Elastic_FacetBuilderTest extends PHPUnit_Framework_TestCase
{
	function testBuildNoFacet()
	{
		$builder = new Search_Elastic_FacetBuilder;
		$this->assertEquals(array(), $builder->build(array()));
	}

	function testBuildSingleFacet()
	{
		$builder = new Search_Elastic_FacetBuilder;
		$this->assertEquals(
			array(
				'facets' => array(
					'categories' => array(
						'terms' => array('field' => 'categories', 'size' => 10),
					),
				),
			), $builder->build(
				array(
					new Search_Query_Facet_Term('categories'),
				)
			)
		);
	}

	function testBuildMultipleFacets()
	{
		$builder = new Search_Elastic_FacetBuilder(8);
		$this->assertEquals(
			array(
				'facets' => array(
					'categories' => array(
						'terms' => array('field' => 'categories', 'size' => 8),
					),
					'deep_categories' => array(
						'terms' => array('field' => 'deep_categories', 'size' => 15),
					),
				),
			), $builder->build(
				array(
					Search_Query_Facet_Term::fromField('categories'),
					Search_Query_Facet_Term::fromField('deep_categories')
						->setCount(15),
				)
			)
		);
	}
}

