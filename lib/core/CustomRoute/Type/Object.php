<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\CustomRoute\Type;

use \TikiLib;
use Tiki\CustomRoute\Type;

/**
 * Custom route for objects
 */
class Object extends Type
{
	/**
	 * @inheritdoc
	 */
	public function getParams()
	{
		return [
			'type' => [
				'name' => tr('Type'),
				'type' => 'select',
				'required' => true,
				'options' => [
					'' => '',
					'article' => tr('Article'),
					'blog' => tr('Blog'),
					'forum' => tr('Forum'),
					'gallery' => tr('Image Gallery'),
					'wiki page' => tr('Wiki Page'),
				],
			],
			'object' => [
				'name' => tr('Object'),
				'type' => 'select',
				'required' => true,
				'function' => 'getObjectsByType',
				'args' => ['type'],
			],
		];
	}

	/**
	 * Retrieve the list the available objects for a specific type
	 *
	 * @param $type
	 * @return array
	 */
	public function getObjectsByType($type)
	{

		$tikilib = new TikiLib;

		$objects = [];

		switch ($type) {
			case 'article':
				$articles = TikiLib::lib('art')->list_articles(0, -1, 'title_asc');

				foreach ($articles['data'] as $article) {
					$objects[$article['articleId']] = $article['title'];
				}
				break;

			case 'blog':
				$blogs = TikiLib::lib('blog')->list_blogs(0, -1, 'title_asc');

				foreach ($blogs['data'] as $blog) {
					$objects[$blog['blogId']] = $blog['title'];
				}
				break;

			case 'forum':
				$forums = TikiLib::lib('comments')->list_forums(0, -1, 'name_asc');

				foreach ($forums['data'] as $forum) {
					$objects[$forum['forumId']] = $forum['name'];
				}
				break;

			case 'gallery':
				$galleries = $tikilib->list_galleries(0, -1, 'name_desc');

				foreach ($galleries['data'] as $gallery) {
					$objects[$gallery['galleryId']] = $gallery['name'];
				}

				break;

			case 'wiki page':
				$pages = $tikilib->list_pages(0, -1, 'pageName_asc');

				foreach ($pages['data'] as $page) {
					$objects[$page['page_id']] = $page['pageName'];
				}

				break;
		}

		return $objects;
	}
}
