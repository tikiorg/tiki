<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: UserWatchesTest.php 57964 2016-03-17 20:04:05Z jonnybradley $

class UserWatchesTest extends TikiTestCase
{

  private $lib;

  protected function setUp()
  {
    $this->lib = TikiLib::lib('tiki');
    $this->userWatches = $this->lib->table('tiki_user_watches');
    $this->userWatches->insert(array(
      'user' => 'tester',
      'event' => 'thread_comment_replied',
      'object' => 1
    ));
    $this->userWatches->insert(array(
      'user' => 'tester',
      'event' => 'thread_comment_replied',
      'object' => 2
    ));
  }

  protected function tearDown()
  {
    $this->userWatches->deleteMultiple(array('user' => 'tester'));
  }

  public function testGetUserEventWatches()
  {
    $set1 = $this->lib->get_user_event_watches('tester', 'thread_comment_replied', 1);
    $set2 = $this->lib->get_user_event_watches('tester', 'thread_comment_replied', array(1, 2));
    $set3 = $this->lib->get_user_event_watches('tester', 'thread_comment_replied', 33);
    $this->assertEquals(1, count($set1));
    $this->assertEquals(2, count($set2));
    $this->assertEquals(0, count($set3));
  }

  public function testGetEventWatches()
  {
    $watches = $this->lib->get_event_watches('thread_comment_replied', 1);
    $this->assertEquals(1, count($watches));
    $this->assertEquals('tester', $watches[0]['user']);
    $watches = $this->lib->get_event_watches('wiki_comment_changes', 'Test Page');
    $this->assertEquals(0, count($watches));
  }
}