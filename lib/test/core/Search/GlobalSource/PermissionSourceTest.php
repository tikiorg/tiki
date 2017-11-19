<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_GlobalSource_PermissionSourceTest extends PHPUnit_Framework_TestCase
{
	private $indexer;
	private $index;
	private $globalAlternate;
	private $perms;

	function setUp()
	{
		$perms = new Perms;
		$perms->setCheckSequence(
			[
				$this->globalAlternate = new Perms_Check_Alternate('admin'),
				new Perms_Check_Direct,
			]
		);
		$perms->setResolverFactories(
			[
				new Perms_ResolverFactory_StaticFactory(
					'global',
					new Perms_Resolver_Static(
						[
							'Anonymous' => ['tiki_p_view'],
							'Registered' => ['tiki_p_view', 'tiki_p_topic_read'],
						]
					)
				),
			]
		);

		$index = new Search_Index_Memory;
		$indexer = new Search_Indexer($index);

		$this->indexer = $indexer;
		$this->index = $index;
		$this->perms = $perms;
	}

	function testSingleGroup()
	{
		$contentSource = new Search_ContentSource_Static(
			[
				'HomePage' => ['view_permission' => 'tiki_p_topic_read'],
			],
			['view_permission' => 'identifier']
		);

		$this->indexer->addGlobalSource(new Search_GlobalSource_PermissionSource($this->perms));
		$this->indexer->addContentSource('wiki page', $contentSource);
		$this->indexer->rebuild();

		$document = $this->index->getDocument(0);

		$typeFactory = $this->index->getTypeFactory();
		$this->assertEquals($typeFactory->multivalue(['Registered']), $document['allowed_groups']);
	}

	function testMultipleGroup()
	{
		$contentSource = new Search_ContentSource_Static(
			['HomePage' => ['view_permission' => 'tiki_p_view'],],
			['view_permission' => 'identifier']
		);

		$this->indexer->addGlobalSource(new Search_GlobalSource_PermissionSource($this->perms));
		$this->indexer->addContentSource('wiki page', $contentSource);
		$this->indexer->rebuild();

		$document = $this->index->getDocument(0);

		$typeFactory = $this->index->getTypeFactory();
		$this->assertEquals($typeFactory->multivalue(['Anonymous', 'Registered']), $document['allowed_groups']);
	}

	function testNoMatches()
	{
		$contentSource = new Search_ContentSource_Static(
			['HomePage' => ['view_permission' => 'tiki_p_do_stuff'],],
			['view_permission' => 'identifier']
		);

		$this->indexer->addGlobalSource(new Search_GlobalSource_PermissionSource($this->perms));
		$this->indexer->addContentSource('wiki page', $contentSource);
		$this->indexer->rebuild();

		$document = $this->index->getDocument(0);

		$typeFactory = $this->index->getTypeFactory();
		$this->assertEquals($typeFactory->multivalue([]), $document['allowed_groups']);
	}

	function testUndeclaredPermission()
	{
		$contentSource = new Search_ContentSource_Static(
			[
				'HomePage' => [],
			],
			['view_permission' => 'identifier']
		);

		$this->indexer->addGlobalSource(new Search_GlobalSource_PermissionSource($this->perms));
		$this->indexer->addContentSource('wiki page', $contentSource);
		$this->indexer->rebuild();

		$document = $this->index->getDocument(0);

		$typeFactory = $this->index->getTypeFactory();
		$this->assertEquals($typeFactory->multivalue([]), $document['allowed_groups']);
	}

	function testWithParentPermissionSpecified()
	{
		$contentSource = new Search_ContentSource_Static(
			[
				'10' => [
					'parent_view_permission' => 'tiki_p_topic_read',
					'parent_object_id' => '1',
					'parent_object_type' => 'forum'
				],
			],
			[
				'parent_view_permission' => 'identifier',
				'parent_object_id' => 'identifier',
				'parent_object_type' => 'identifier'
			]
		);

		$this->indexer->addGlobalSource(new Search_GlobalSource_PermissionSource($this->perms));
		$this->indexer->addContentSource('forum post', $contentSource);
		$this->indexer->rebuild();

		$document = $this->index->getDocument(0);

		$typeFactory = $this->index->getTypeFactory();
		$this->assertEquals($typeFactory->multivalue(['Registered']), $document['allowed_groups']);
	}

	function testWithBothSpecified()
	{
		$contentSource = new Search_ContentSource_Static(
			[
				'10' => [
					'parent_view_permission' => 'tiki_p_topic_read',
					'parent_object_id' => '1',
					'parent_object_type' => 'forum',
					'view_permission' => 'tiki_p_article_read'
				],
			],
			[
				'parent_view_permission' => 'identifier',
				'parent_object_id' => 'identifier',
				'parent_object_type' => 'identifier',
				'view_permission' => 'identifier'
			]
		);

		$this->indexer->addGlobalSource(new Search_GlobalSource_PermissionSource($this->perms));
		$this->indexer->addContentSource('forum post', $contentSource);
		$this->indexer->rebuild();

		$document = $this->index->getDocument(0);

		$typeFactory = $this->index->getTypeFactory();
		$this->assertEquals($typeFactory->multivalue(['Registered']), $document['allowed_groups']);
	}
}
