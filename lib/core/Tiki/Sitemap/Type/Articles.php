<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Sitemap\Type;

use Tiki\Sitemap\AbstractType;
use TikiLib;

/**
 * Generate Sitemap for Articles
 */
class Articles extends AbstractType
{
	/**
	 * Generate Sitemap
	 */
	public function generate()
	{
		if (! $this->checkFeatureAndPermissions('feature_articles', 'tiki_p_read_article')) {
			return;
		}

		$articleLibrary = TikiLib::lib('art');
		$listPages = $articleLibrary->list_articles(0, -1, 'publishDate_desc', '', 0, 0, false, '', '', 'y', '', '', '', '', '', '', '', false, 'y');
		$listPages['data'] = array_filter($listPages['data'], function ($article) {
			return ($article['disp_article'] === 'y');
		});

		$this->addEntriesToSitemap($listPages, '/tiki-read_article.php?articleId=%s', 'articleId', 'article');
	}
}
