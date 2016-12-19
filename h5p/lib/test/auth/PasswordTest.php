<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
class PasswordTest extends TikiTestCase
{
	function test_pass()
	{
		global $prefs;
		$userlib = TikiLib::lib('user');
		$prefs['pass_chr_num'] = $prefs['pass_chr_case'] = $prefs['pass_chr_special'] = $prefs['pass_repetition'] = $prefs['pass_diff_username'] = 'y';
		$passwords = array('1234', 'abcd', '123abc', '123ABc', '123AAbc*');
		foreach ($passwords as $pass) {
			$res = $userlib->check_password_policy($pass);
			$this->assertEquals("$pass=n", "$pass=".($res==''?'y':'n'));
		}
		$pass='123ABcd*';
		$res = $userlib->check_password_policy($pass);
		$this->assertEquals("$pass=y", "$pass=".($res==''?'y':'n'));
	}
}
