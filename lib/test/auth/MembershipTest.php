<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
/**
 * @group integration
 */
class MembershipTest extends TikiTestCase
{

	protected $userlib;

	function setUp()
	{
		parent::setUp();
		$cachelib = TikiLib::lib('cache');

		$this->userlib = new UsersLib();

		$cachelib->invalidate('grouplist');

		$this->userlib->remove_user('membershiptest_a');
		$this->userlib->remove_user('membershiptest_b');
		$this->userlib->remove_user('membershiptest_c');
		$this->userlib->remove_group('MembershipTest');

		$cachelib->invalidate('grouplist');

		$this->userlib->add_group('MembershipTest', '', '', 0, 0, '', '', 0, '', 0, 0, 'n', 45);

		global $user_preferences;
		$user_preferences = [];

		$this->userlib->add_user('membershiptest_a', 'abc', 'a@example.com');
		$this->userlib->add_user('membershiptest_b', 'abc', 'a@example.com');
		$this->userlib->add_user('membershiptest_c', 'abc', 'a@example.com');

		$this->userlib->assign_user_to_group('membershiptest_a', 'MembershipTest');
		$this->userlib->assign_user_to_group('membershiptest_b', 'MembershipTest');
	}

	function tearDown()
	{
		parent::tearDown();
		$this->userlib->remove_user('membershiptest_a');
		$this->userlib->remove_user('membershiptest_b');
		$this->userlib->remove_user('membershiptest_c');
		$this->userlib->remove_group('MembershipTest');
	}

	function testExtendMembership()
	{
		//$this->markTestIncomplete('Marking this test as incomplete since it is failing and the problem is the test itself and not the code that is being tested. If you are familiar with these test please fix it.');
		$id = $this->userlib->get_user_id('membershiptest_a');

		$expires = $this->userlib->getOne(
			'SELECT `expire` FROM `users_usergroups` WHERE `userId` = ? AND `groupName` = "MembershipTest"',
			[$id]
		);

		//convert start date to object
		$rawstartutc = new DateTimeImmutable('@' . $expires);
		global $prefs;
		$tz = TikiDate::TimezoneIsValidId($prefs['server_timezone']) ? $prefs['server_timezone'] : 'UTC';
		$timezone = new DateTimeZone($tz);
		$startlocal = $rawstartutc->setTimezone($timezone);

		$extendto = $startlocal->modify('+' . 45 * 3 . ' days');
		$expect = $extendto->getTimestamp();

		$this->userlib->extend_membership('membershiptest_a', 'MembershipTest', 3);


		$this->assertEquals(
			$expect,
			$this->userlib->getOne(
				'SELECT `expire` FROM `users_usergroups` WHERE `userId` = ? AND `groupName` = "MembershipTest"',
				[$id]
			)
		);
		$this->assertEquals(
			$expires,
			$this->userlib->getOne(
				'SELECT `expire` FROM `users_usergroups` WHERE `userId` = ? AND `groupName` = "MembershipTest"',
				[$this->userlib->get_user_id('membershiptest_b')]
			)
		);
	}

	function testExtendExpiredMembership()
	{
		$id = $this->userlib->get_user_id('membershiptest_b');

		$expires = $this->userlib->getOne(
			'SELECT `created` FROM `users_usergroups` WHERE `userId` = ? AND `groupName` = "MembershipTest"',
			[$id]
		);

		//convert start date to object
		$rawstartutc = new DateTimeImmutable('@' . $expires);
		global $prefs;
		$tz = TikiDate::TimezoneIsValidId($prefs['server_timezone']) ? $prefs['server_timezone'] : 'UTC';
		$timezone = new DateTimeZone($tz);
		$startlocal = $rawstartutc->setTimezone($timezone);

		$extendto = $startlocal->modify('+' . 45 * 2 . ' days');
		$expect = $extendto->getTimestamp();

		$this->userlib->query('UPDATE `users_usergroups` SET `expire` = `expire` - 12*3600 - 45*24*3600 WHERE `userId` = ?', [$id]);

		$this->userlib->extend_membership('membershiptest_b', 'MembershipTest', 2);

		$this->assertEquals(
			$expect,
			$this->userlib->getOne(
				'SELECT `expire` FROM `users_usergroups` WHERE `userId` = ? AND `groupName` = "MembershipTest"',
				[$id]
			)
		);
	}
}
