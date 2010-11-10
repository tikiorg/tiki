<?php
class PasswordTest extends TikiTestCase
{
	function test_pass() {
		global $prefs;
		global $userlib;
		$prefs['pass_chr_num'] = $prefs['pass_chr_case'] = $prefs['pass_chr_special'] = $prefs['pass_repetition'] = 'y';
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