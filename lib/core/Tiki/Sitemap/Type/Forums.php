<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Sitemap\Type;

use Tiki\Sitemap\AbstractType;
use TikiLib;
use Perms;

/**
 * Generate Sitemap for Forums
 */
class Forums extends AbstractType
{
	/**
	 * Generate Sitemap
	 */
	public function generate()
	{
		if (! $this->checkFeatureAndPermissions('feature_forums', 'tiki_p_forum_read')) {
			return;
		}

		$commentsLib = TikiLib::lib('comments');
		$channels = $commentsLib->list_forums();

		$channels['data'] = Perms::filter(array('type' => 'forum'), 'object', $channels['data'], array('object' => 'forumId'), 'forum_read');

		$this->addEntriesToSitemap($channels, '/tiki-view_forum.php?forumId=%s', 'forumId', null, '', 'lastPost', '0.6', 'daily');
	}
}