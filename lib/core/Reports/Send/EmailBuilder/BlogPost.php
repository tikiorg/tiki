<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Class for blog_post events
 */
class Reports_Send_EmailBuilder_BlogPost extends Reports_Send_EmailBuilder_Abstract
{
	public function getTitle()
	{
		return tr('New blog posts:');
	}
	
	public function getOutput(array $change)
	{
		$base_url = $change['data']['base_url'];

		$output = '<u>' . $change['data']['user'] . '</u> ' .
							tra('replied to the blog') .
							" <a href=\"{$base_url}tiki-view_blog.php?blogId=" . $change['data']['blogId'] . "\">" . $change['data']['blogTitle'] . "</a>" .
							" <a href=\"{$base_url}tiki-view_blog_post.php?postId=\"" . $change['data']['postId'] . "></a>.";
		
		return $output;
	}
}
