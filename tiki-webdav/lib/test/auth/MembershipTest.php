<?php
/** 
 * @group integration
 */
class MembershipTest extends TikiTestCase
{
	function setUp() {
		parent::setUp();
		global $userlib;
		global $cachelib;

		$cachelib->invalidate( 'grouplist' );

		$userlib->remove_user( 'membershiptest_a' );
		$userlib->remove_user( 'membershiptest_b' );
		$userlib->remove_user( 'membershiptest_c' );
		$userlib->remove_group( 'MembershipTest' );

		$cachelib->invalidate( 'grouplist' );

		$userlib->add_group( 'MembershipTest', '', '', 0, 0, '', '', 0, '', 0, 0, 'n', 45 );

		$userlib->add_user( 'membershiptest_a', 'abc', 'a@example.com' );
		$userlib->add_user( 'membershiptest_b', 'abc', 'a@example.com' );
		$userlib->add_user( 'membershiptest_c', 'abc', 'a@example.com' );

		$userlib->assign_user_to_group( 'membershiptest_a', 'MembershipTest' );
		$userlib->assign_user_to_group( 'membershiptest_b', 'MembershipTest' );
	}

	function tearDown() {
		parent::tearDown();
		global $userlib;
		$userlib->remove_user( 'membershiptest_a' );
		$userlib->remove_user( 'membershiptest_b' );
		$userlib->remove_user( 'membershiptest_c' );
		$userlib->remove_group( 'MembershipTest' );
	}

	function testExtendMembership() {
		global $userlib;
		
		$userlib->extend_membership( 'membershiptest_a', 'MembershipTest', 3 );

		$expect = $userlib->now + 45 * 2 * ( 3600 * 24 );

		$this->assertEquals( $expect, 
			$userlib->getOne( 'SELECT `created` FROM `users_usergroups` WHERE `userId` = ? AND `groupName` = "MembershipTest"',
				array( $userlib->get_user_id( 'membershiptest_a' ) )
			)
		);
		$this->assertEquals( $userlib->now, 
			$userlib->getOne( 'SELECT `created` FROM `users_usergroups` WHERE `userId` = ? AND `groupName` = "MembershipTest"',
				array( $userlib->get_user_id( 'membershiptest_b' ) )
			)
		);
	}

	function testExtendExpiredMembership() {
		global $userlib;
		$id = $userlib->get_user_id( 'membershiptest_b' );

		$userlib->query( 'UPDATE `users_usergroups` SET `created` = `created` - 12*3600 - 45*24*3600 WHERE `userId` = ?', array( $id ) );

		$userlib->extend_membership( 'membershiptest_b', 'MembershipTest', 2 );

		$expect = $userlib->now + 45 * ( 3600 * 24 );

		$this->assertEquals( $expect, 
			$userlib->getOne( 'SELECT `created` FROM `users_usergroups` WHERE `userId` = ? AND `groupName` = "MembershipTest"',
				array( $userlib->get_user_id( 'membershiptest_b' ) )
			)
		);
	}
}
