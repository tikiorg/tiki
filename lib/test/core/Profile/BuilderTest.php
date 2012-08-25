<?php

class Profile_BuilderTest extends PHPUnit_Framework_TestCase
{
	function testBasicProfile()
	{
		$builder = new Tiki_Profile_Builder;
		
		$expect = <<<EXPECT
---
EXPECT;
		$this->assertIs($expect, $builder->getContent());
	}

	function testAddObjects()
	{
		$builder = new Tiki_Profile_Builder;
		$builder->addObject('wiki_page', 'foo', array(
			'name' => 'Foo',
			'content' => 'Hello',
		));
		$builder->addObject('trackerfield', 'date', array(
			'tracker' => $builder->ref('tracker'),
		));
		
		$expect = <<<EXPECT
---
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
		$builder = new Tiki_Profile_Builder;
		$builder->addGroup('Base', $builder->user('group'));
		$builder->addGroup('Viewer', $builder->user('group') . ' Viewer');
		$builder->setManagingGroup('Base');
		
		$expect = <<<EXPECT
---
mappings: 
  Base: \$profilerequest:group\$undefined\$
  Viewer: \$profilerequest:group\$undefined\$ Viewer
permissions: 
  Base: 
    description: \$profilerequest:group\$undefined\$
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
    description: \$profilerequest:group\$undefined\$ Viewer
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

	private function assertIs($expect, $content)
	{
		$matches = WikiParser_PluginMatcher::match($content);

		foreach ($matches as $plugin) {
			$this->assertEquals(trim($expect), trim($plugin->getBody()));
		}
	}
}

