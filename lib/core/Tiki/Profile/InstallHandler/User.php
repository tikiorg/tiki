<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Adding users with this handler is not recommended for production servers
 * as it may be insecure. Use for generating examples and test data only.
 * 
 * Assigning existing users to groups should be fine though...
 * 
 * Example (Tiki 6+):
 * =====================================
 
 objects:
# assign existing user to existing group
 -
  type: user
  data: 
    name: testit
    groups: [ Test Group ]

# add new user with email and initial password defaulting to username
# doesn't need to change password on first login (defaults to y)
# finally assigned to Test Group
 -
  type: user 
  data: 
    name: tester
    email: tester@example.com
    change: n
    groups: [ Test Group ]

 * =====================================
 * 
 */
class Tiki_Profile_InstallHandler_User extends Tiki_Profile_InstallHandler
{
	function getData()
	{
		if ( $this->data )
			return $this->data;
		$data = $this->obj->getData();
		$this->replaceReferences($data);

		return $this->data = $data;
	}
	
	function canInstall()
	{
		$data = $this->getData();
		
		if (isset($data)) return true;
		else return false;
	}
	
	function _install()
	{
		if ($this->canInstall()) {
			global $userlib; if (!$userlib) require_once 'lib/userslib.php';

			$user = $this->getData();
				
			if (!$userlib->user_exists($user['name'])) {
				$pass = isset($user['pass']) ? $user['pass'] : $user['name'];
				$email = isset($user['email']) ? $user['email'] : '';
				if (isset($user['change']) && $user['change'] === false) {
					$userlib->add_user($user['name'], $pass, $email);
				} else {
					$userlib->add_user($user['name'], $pass, $email, $pass, true);
				}
			}

			if (isset($user['groups'])) {
				foreach ($user['groups'] as $group) {
					$userlib->assign_user_to_group($user['name'], $group);
				}
			}
				
			return $userlib->get_user_id($user['name']);
		}
	}
}



