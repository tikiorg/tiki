<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tracker_OptionsTest extends PHPUnit_Framework_TestCase
{
	function testBuildLegacyStringBuilder()
	{
		$options = Tracker_Options::fromSerialized(
			json_encode(['a' => 3, 'b' => 2, 'c' => 1]),
			[
				'params' => [
					'a' => [
						'legacy_index' => 2,
					],
					'b' => [
						'legacy_index' => 1,
					],
					'c' => [
						'legacy_index' => 0,
					],
					'd' => [
						// No legacy
					],
				],
			]
		);

		$this->assertEquals(['1', '2', '3'], $options->buildOptionsArray());
	}

	function testSeparatorOnEmptyData()
	{
		$options = Tracker_Options::fromString(
			'a,,b',
			[
				'params' => [
					'a' => [
						'legacy_index' => 0,
					],
					'b' => [
						'legacy_index' => 1,
						'separator' => '|',
					],
					'c' => [
						'legacy_index' => 2,
					],
				],
			]
		);

		$this->assertEquals([], $options->getParam('b'));
	}
}
