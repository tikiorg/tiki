<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Sitemap\Type;

use Tiki\Sitemap\AbstractType;

/**
 * Generate Sitemap for Pages
 */
class Page extends AbstractType
{
	/**
	 * Generate Sitemap
	 */
	public function generate()
	{
		global $tikilib;

		if (! $this->checkFeatureAndPermissions('feature_wiki', 'tiki_p_view')) {
			return;
		}

		/** @var \TikiLib $tikilib */
		$listPages = $tikilib->list_pages();
		$this->addEntriesToSitemap($listPages, '/tiki-index.php?page=%s', 'pageSlug', null, '', 'lastModif');
	}
}
