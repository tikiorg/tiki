<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Article_Controller
{
	function setUp()
	{
		Services_Exception_Disabled::check('feature_articles');
	}

	function action_create_from_url($input)
	{
		Services_Exception_Disabled::check('page_content_fetch');
		Services_Exception_Denied::checkGlobal('edit_article');

		$id = null;
		$title = null;
		$url = $input->url->url();
		if ($_SERVER['REQUEST_METHOD'] == 'POST' && $url) {
			$lib = TikiLib::lib('pagecontent');

			$data = $lib->grabContent($url);

			if (! $data) {
				throw new Services_Exception_FieldError($input->errorfield->text() ?: 'url', tr('Content could not be loaded.'));
			}
			$data['content'] = trim($data['content']) == '' ? $data['content'] : '~np~' . $data['content'] . '~/np~';
			$data['description'] = '';
			$data['author'] = '';
			$topicId = $input->topicId->int();
			$articleType = $input->type->text();
			$title = $data['title'];

			$hash = md5($data['title'] . $data['description'] . $data['content']);

			$id = TikiDb::get()->table('tiki_articles')->fetchOne('articleId', array(
				'linkto' => $url,
			)) ?: 0;

			if (! $id) {
				$tikilib = TikiLib::lib('tiki');
				$publication = $tikilib->now;
				$expire = $publication + 3600*24*365;
				$rating = 10;

				$artlib = TikiLib::lib('art');
				$id = $artlib->replace_article(
					$title,
					$data['author'],
					$topicId,
					'n',
					'',
					0,
					'',
					'',
					$data['description'],
					$data['content'],
					$publication,
					$expire,
					$GLOBALS['user'],
					$id,
					0,
					0,
					$articleType,
					'',
					'',
					$url,
					'',
					'',
					$rating,
					'n',
					'',
					'',
					'',
					'',
					'y',
					true
				);
			}
		}

		$db = TikiDb::get();
		$topics = $db->table('tiki_topics')->fetchMap('topicId', 'name', array(), -1, -1, 'name_asc');
		$types = $db->table('tiki_article_types')->fetchColumn('type', array());

		return [
			'title' => tr('Create article from URL'),
			'url' => $url,
			'id' => $id,
			'articleTitle' => $title,
			'topics' => $topics,
			'types' => $types,
		];
	}
}

