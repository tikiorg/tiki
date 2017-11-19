<?php

class Profile_AnalyserTest extends PHPUnit_Framework_TestCase
{
	function testReadGroupList()
	{
		$builder = new Services_Workspace_ProfileBuilder;
		$builder->addGroup('Base', $builder->user('group'));
		$builder->addGroup('World', $builder->user('group') . ' World', true);
		$builder->setManagingGroup('Base');
		$builder->setPermissions(
			'Base',
			'category',
			$builder->user('category'),
			[
				'view',
				'edit',
			]
		);
		$builder->setPermissions(
			'World',
			'category',
			$builder->user('category'),
			['view']
		);

		$profile = Tiki_Profile::fromString($builder->getContent());
		$analyser = new Services_Workspace_ProfileAnalyser($profile);

		$this->assertEquals(
			[
				'Base' => [
					'name' => '{group}',
					'managing' => true,
					'autojoin' => false,
					'permissions' => ['view', 'edit'],
				],
				'World' => [
					'name' => '{group} World',
					'managing' => false,
					'autojoin' => true,
					'permissions' => ['view'],
				],
			],
			$analyser->getGroups('category', $analyser->user('category'))
		);
	}

	function testGetObjects()
	{
		$builder = new Services_Workspace_ProfileBuilder;
		$builder->addObject(
			'wiki_page',
			'foo',
			[
				'name' => 'Foo',
				'namespace' => $builder->user('namespace'),
				'content' => 'Hello',
				'categories' => $builder->user('category'),
			]
		);
		$builder->addObject(
			'wiki_page',
			'bar',
			[
				'name' => 'Bar',
				'namespace' => $builder->user('namespace'),
				'content' => 'World',
				'categories' => $builder->user('category'),
			]
		);

		$profile = Tiki_Profile::fromString($builder->getContent());
		$analyser = new Services_Workspace_ProfileAnalyser($profile);

		$this->assertEquals(
			[
				[
					'name' => 'Foo',
					'namespace' => '{namespace}',
					'content' => 'Hello',
				],
				[
					'name' => 'Bar',
					'namespace' => '{namespace}',
					'content' => 'World',
				],
			],
			$analyser->getObjects('wiki_page')
		);
	}
}
