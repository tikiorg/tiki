<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: NotificationEmailLibTest.php 57964 2016-03-17 20:04:05Z jonnybradley $

class NotificationEmailLibTest extends TikiTestCase
{

	function testSendCommentNotification()
	{		
		$tiki = TikiLib::lib('tiki');
		$comment1 = $tiki->table('tiki_comments')->insert(array(
			'object' => 1,
			'objectType' => 'article',
			'parentId' => 0
		));
		$comment2 = $tiki->table('tiki_comments')->insert(array(
			'object' => 1,
			'objectType' => 'article',
			'parentId' => $comment1
		));
		$user_watch = $tiki->table('tiki_user_watches')->insert(array(
			'user' => 'tester',
			'event' => 'thread_comment_replied',
			'object' => $comment1,
			'email' => 'test@example.org'
		));

		require_once 'lib/notifications/notificationemaillib.php';
		$_SERVER["REQUEST_URI"] = '';
		$res = sendCommentNotification('article', 1, 'Test comment', 'test', $comment2, null);
		$this->assertEquals(1, $res);

		$tiki->table('tiki_comments')->delete(array('threadId' => $comment1));
		$tiki->table('tiki_comments')->delete(array('threadId' => $comment2));
		$tiki->table('tiki_user_watches')->delete(array('watchId' => $user_watch));
	}
}

