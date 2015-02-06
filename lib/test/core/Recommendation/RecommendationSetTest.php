<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Recommendation;

class RecommendationSetTest extends \PHPUnit_Framework_TestCase
{
	function testAddFiltersRecommendations()
	{
		$set = new RecommendationSet('X');
		$set->add(new Debug\SourceDocument('forum post', 1));
		$set->add(new Recommendation('forum post', 42));

		$expect = new RecommendationSet('X');
		$expect->addDebug(new Debug\SourceDocument('forum post', 1));
		$expect->add(new Recommendation('forum post', 42));

		$this->assertEquals($expect, $set);
	}

	function testDebugInformationExcludedFromIteration()
	{
		$set = new RecommendationSet('X');
		$set->addDebug(new Debug\SourceDocument('forum post', 1));
		$set->add($rec = new Recommendation('forum post', 42));

		$out = [];
		foreach ($set as $r) {
			$out[] = $r;
		}

		$this->assertSame($out, [$rec]);
	}
}
