<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Adding users with this handler is not generally recommended for production servers
 * as it may be insecure unless you take care to restrict its access. 
 * Use for generating examples and test data is obviously OK.
 * 
 * Assigning existing users to groups should be generally fine though...
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
# and default Group set as Test Group (Tiki 15 addition and backported to 12 & 14)
# generate password option introduced in Tiki 14.1 and backported to 12.5
# Example 12.5 +
 -
  type: user 
  data: 
    name: $profilerequest:testuser$NO testuser SET$
    pass: generate
    email: geoff.brickell@btinternet.com
    change: n
    groups: [Registered]
    defaultgroup: Registered

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
			$userlib = TikiLib::lib('user');
			$tikilib = TikiLib::lib('tiki');

			$user = $this->getData();
			$retpass = false;
				
			if (!$userlib->user_exists($user['name'])) {
// if pass parameter has not been set use the name parameter as the password as well	
				$pass = isset($user['pass']) ? $user['pass'] : $user['name'];
// for the special case where pass has been set to 'generate' then generate a random password				
				if ($pass == 'generate') {
					$pass = $tikilib->genPass();
					$retpass = true;
				}				
				$email = isset($user['email']) ? $user['email'] : '';
				if (isset($user['change']) && $user['change'] === false) {
					$user['name'] = $userlib->add_user($user['name'], $pass, $email);
				} else {
					$user['name'] = $userlib->add_user($user['name'], $pass, $email, $pass, true);
				}
			}

			if (isset($user['groups'])) {
				foreach ($user['groups'] as $group) {
					$userlib->assign_user_to_group($user['name'], $group);
				}
			}
		
			if (isset($user['defaultgroup'])) {
				$userlib->set_default_group($user['name'], $user['defaultgroup']);
			}			

// if a password has been generated then return this value instead of the userId				
			if ($retpass) {
				return $pass;
			} else {
				return $userlib->get_user_id($user['name']);
			}
		}
	}
}
