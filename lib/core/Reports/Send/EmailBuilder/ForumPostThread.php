<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Class for forum_post_thread events 
 */
class Reports_Send_EmailBuilder_ForumPostThread extends Reports_Send_EmailBuilder_Abstract {
	public function getTitle()
	{
		return tr('New posts in foruns:');
	}
	
	public function getOutput(array $change)
	{
		global $base_url, $dbTiki;
		
		$commentslib = TikiLib::lib('comments');
		$parent_topic = $commentslib->get_comment($change['data']['topicId']);
		
		$output = "<u>".$change['data']['user']."</u> <a href=\"{$base_url}tiki-view_forum_thread.php?forumId=".$change['data']['forumId']."&comments_parentId=".$change['data']['topicId']."#threadId".$change['data']['threadId']."\">".tra("replied")."</a> ".tra("to the topic")." <a href=\"{$base_url}tiki-view_forum_thread.php?comments_parentId=".$change['data']['topicId']."&forumId=".$change['data']['forumId']."\">".$parent_topic['title']."</a>.";
				
		return $output;
	}
}