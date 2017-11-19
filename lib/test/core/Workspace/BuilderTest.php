<?php

class Profile_BuilderTest extends PHPUnit_Framework_TestCase
{
	function testBasicProfile()
	{
		$builder = new Services_Workspace_ProfileBuilder;

		$expect = <<<EXPECT
{  }
EXPECT;
		$this->assertIs($expect, $builder->getContent());
	}

	function testAddObjects()
	{
		$builder = new Services_Workspace_ProfileBuilder;
		$builder->addObject(
			'wiki_page',
			'foo',
			[
				'name' => 'Foo',
				'content' => 'Hello',
			]
		);
		$builder->addObject(
			'trackerfield',
			'date',
			[
				'tracker' => $builder->ref('tracker'),
			]
		);

		$expect = <<<EXPECT
objects:
  -
    type: wiki_page
    ref: foo
    data:
      name: Foo
      content: Hello
  -
    type: trackerfield
    ref: date
    data:
      tracker: \$tracker
EXPECT;
		$this->assertIs($expect, $builder->getContent());
	}

	function testGroups()
	{
		$builder = new Services_Workspace_ProfileBuilder;
		$builder->addGroup('Base', $builder->user('group'));
		$builder->addGroup('Viewer', $builder->user('group') . ' Viewer', true);
		$builder->setManagingGroup('Base');

		$expect = <<<EXPECT
mappings:
  Base: '\$profilerequest:group\$undefined\$'
  Viewer: '\$profilerequest:group\$undefined\$ Viewer'
permissions:
  Base:
    description: '\$profilerequest:group\$undefined\$'
    objects:
      -
        type: group
        id: Base
        allow:
          - group_view
          - group_view_members
          - group_add_member
          - group_remove_member
      -
        type: group
        id: Viewer
        allow:
          - group_view
          - group_view_members
          - group_add_member
          - group_remove_member
  Viewer:
    description: '\$profilerequest:group\$undefined\$ Viewer'
    autojoin: 'y'
    objects:
      -
        type: group
        id: Base
        allow:
          - group_view
          - group_view_members
      -
        type: group
        id: Viewer
        allow:
          - group_view
          - group_view_members
EXPECT;
		$this->assertIs($expect, $builder->getContent());
	}

	function testReplaceSimpleSyntax()
	{
		$builder = new Services_Workspace_ProfileBuilder;
		$builder->addGroup('Base', '{group}');
		$builder->setManagingGroup('Base');

		$expect = <<<EXPECT
mappings:
  Base: '\$profilerequest:group\$undefined\$'
permissions:
  Base:
    description: '\$profilerequest:group\$undefined\$'
    objects:
      -
        type: group
        id: Base
        allow:
          - group_view
          - group_view_members
          - group_add_member
          - group_remove_member
EXPECT;
		$this->assertIs($expect, $builder->getContent());
	}

	function testAssignDefaultGroup()
	{
		$builder = new Services_Workspace_ProfileBuilder;
		$builder->addObject(
			'wiki_page',
			'foo',
			[
				'name' => 'Foo',
				'content' => 'Hello',
				'categories' => $builder->user('category'),
			]
		);

		$expect = <<<EXPECT
objects:
  -
    type: categorize
    data:
      type: wiki_page
      object: \$foo
      categories:
        - '\$profilerequest:category\$undefined\$'
  -
    type: wiki_page
    ref: foo
    data:
      name: Foo
      content: Hello
EXPECT;
		$this->assertIs($expect, $builder->getContent());
	}

	private function assertIs($expect, $content)
	{
		$matches = WikiParser_PluginMatcher::match($content);

		foreach ($matches as $plugin) {
			$this->assertEquals(trim($expect), trim($plugin->getBody()));
		}
	}
}
