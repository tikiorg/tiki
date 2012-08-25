<?php

class Profile_AnalyserTest extends PHPUnit_Framework_TestCase
{
	function testReadGroupList()
	{
		$builder = new Tiki_Profile_Builder;
		$builder->addGroup('Base', $builder->user('group'));
		$builder->addGroup('World', $builder->user('group') . ' World');
		$builder->setManagingGroup('Base');
		$builder->setPermissions('Base', 'category', $builder->user('category'), array(
			'view',
			'edit',
		));
		$builder->setPermissions('World', 'category', $builder->user('category'), array(
			'view',
		));

		$profile = Tiki_Profile::fromString($builder->getContent());
		$analyser = new Tiki_Profile_Analyser($profile);

		$this->assertEquals(array(
			'Base' => array(
				'name' => '{group}',
				'managing' => true,
				'permissions' => array('view', 'edit'),
			),
			'World' => array(
				'name' => '{group} World',
				'managing' => false,
				'permissions' => array('view'),
			),
		), $analyser->getGroups('category', $analyser->user('category')));
	}
}

