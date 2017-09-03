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
 * Generate Sitemap for Blogs
 */
class Blog extends AbstractType
{
	/**
	 * Generate Sitemap
	 */
	public function generate()
	{
		if (! $this->checkFeatureAndPermissions('feature_blogs', 'tiki_p_read_blog')) {
			return;
		}

		$blogLib = TikiLib::lib('blog');

		$listPages = $blogLib->list_blogs();
		$this->addEntriesToSitemap($listPages, '/tiki-view_blog.php?blogId=%s', 'blogId', 'blog', 'title', 'lastModif', '0.8');

		$posts = $blogLib->list_posts();
		$this->addEntriesToSitemap($posts, '/tiki-view_blog_post.php?postId=%s', 'postId', 'blogpost');
	}
}