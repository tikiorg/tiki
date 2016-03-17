<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Wiki_Controller
{
	function setUp()
	{
		Services_Exception_Disabled::check('feature_wiki');
	}

	function action_get_page($input)
	{
		$wikilib = TikiLib::lib('wiki');
		$page = $input->page->text();
		$info = $wikilib->get_page_info($page);
		if (!$info) {
			throw new Services_Exception_NotFound(tr('Page "%0" not found', $page));
		}
		$canBeRefreshed = false;
		$data = $wikilib->get_parse($page, $canBeRefreshed);
		return array('data' => $data);
	}

	function action_regenerate_slugs($input)
	{
		global $prefs;
		Services_Exception_Denied::checkGlobal('admin');

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$pages = TikiDb::get()->table('tiki_pages');

			$initial = TikiLib::lib('slugmanager');
			$tracker = new Tiki\Wiki\SlugManager\InMemoryTracker;
			$manager = clone $initial;
			$manager->setValidationCallback($tracker);

			$list = $pages->fetchColumn('pageName', []);
			$pages->updateMultiple(['pageSlug' => null], []);

			foreach ($list as $page) {
				$slug = $manager->generate($prefs['wiki_url_scheme'], $page, $prefs['url_only_ascii'] === 'y');

				$count = 1;
				while ($pages->fetchCount(['pageSlug' => $slug]) && $count < 100) {
					$count++;
					$slug = $manager->generate($prefs['wiki_url_scheme'], $page . ' ' . $count, $prefs['url_only_ascii'] === 'y');
				}

				$tracker->add($page);
				$pages->update(['pageSlug' => $slug], ['pageName' => $page]);
			}

			TikiLib::lib('access')->redirect('tiki-admin.php?page=wiki');
		}

		return array(
			'title' => tr('Regenerate Wiki URLs'),
		);
	}
}
