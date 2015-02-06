<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Class for forum_post_topic events
 */
class Reports_Send_EmailBuilder_ForumPostTopic extends Reports_Send_EmailBuilder_Abstract
{
	public function getTitle()
	{
		return tr('New posts in forums:');
	}

	public function getOutput(array $change)
	{
		$base_url = $change['data']['base_url'];

		$output = tr(
			'%0 created the topic %1 at forum %2',
			"<u>" . $change['data']['user'] . "</u>",
			"<a href=\"{$base_url}tiki-view_forum_thread.php?comments_parentId={$change['data']['topicId']}&forumId={$change['data']['forumId']}\">{$change['data']['threadName']}</a>",
			"<a href=\"{$base_url}tiki-view_forum.php?forumId={$change['data']['forumId']}\">{$change['data']['forumName']}</a>."
		);

		return $output;
	}
}
