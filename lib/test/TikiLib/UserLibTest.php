<?php

class UserLibTest extends TikiTestCase
{

	protected function prepareLdapSyncUserDataUserLibMock($user, $name, $email, $country, $setWillBeCalled, $setValues)
	{
		$userLibMock = $this
			->getMockBuilder('UsersLib')
			->setMethods(['get_user_preference', 'get_user_email', 'set_user_fields'])
			->getMock();

		//realName - get_user_preference
		$userLibMock
			->expects($this->at(0))
			->method('get_user_preference')
			->with($this->equalTo($user), $this->equalTo('realName'))
			->will($this->returnValue($name));

		//email - get_user_email
		$userLibMock
			->expects($this->at(1))
			->method('get_user_email')
			->with($this->equalTo($user))
			->will($this->returnValue($email));

		//country - get_user_preference
		$userLibMock
			->expects($this->at(2))
			->method('get_user_preference')
			->with($this->equalTo($user), $this->equalTo('country'))
			->will($this->returnValue($country));

		if ($setWillBeCalled) {
			// set_user_fields
			$userLibMock
				->expects($this->at(3))
				->method('set_user_fields')
				->with($this->equalTo($setValues))
				->will($this->returnValue(true));
		}

		return $userLibMock;
	}

	/**
	 * @dataProvider dataForLdapSyncUserDataUserWithoutPreferences
	 */
	public function testLdapSyncUserDataUserWithoutPreferences($name, $email, $country, $ldapAttributes, $setValues)
	{
		global $prefs;
		$prefs['auth_ldap_nameattr'] = 'cn';
		$prefs['auth_ldap_emailattr'] = 'mail';
		$prefs['auth_ldap_countryattr'] = 'c';

		$setWillBeCalled = is_array($setValues) && count($setValues) > 0;

		$user = md5(uniqid(true));
		$setValues['login'] = $user;

		$userLib = $this->prepareLdapSyncUserDataUserLibMock($user, $name, $email, $country, $setWillBeCalled, $setValues);


		$userLib->ldap_sync_user_data($user, $ldapAttributes);
	}

	public function dataForLdapSyncUserDataUserWithoutPreferences()
	{
		return [
			[ // empty values
				'name' => null,
				'email' => null,
				'country' => null,
				'ldapAttributes' => [],
				'setValues' => [],
			],
			[ // existing values, no attributes from ldap
				'name' => 'Some Name',
				'email' => 'email@example.com',
				'country' => 'XX',
				'ldapAttributes' => [],
				'setValues' => [
					'realName' => '',
					'email' => '',
					'country' => ''
				],
			],
			[ // existing values, empty values from ldap
				'name' => 'Some Name',
				'email' => 'email@example.com',
				'country' => 'XX',
				'ldapAttributes' => [
					'cn' => '',
					'mail' => '',
					'c' => ''
				],
				'setValues' => [
					'realName' => '',
					'email' => '',
					'country' => ''
				],
			],
			[ // existing values, new values from ldap
				'name' => 'Some Name',
				'email' => 'email@example.com',
				'country' => 'XX',
				'ldapAttributes' => [
					'cn' => 'Ldap Name',
					'mail' => 'ldap@example.com',
					'c' => 'XY'
				],
				'setValues' => [
					'realName' => 'Ldap Name',
					'email' => 'ldap@example.com',
					'country' => 'XY'
				],
			],
			[ //existing values, new values from ldap, including existing value for multi values attributes
				'name' => 'Some Name',
				'email' => 'email@example.com',
				'country' => 'XX',
				'ldapAttributes' => [
					'cn' => 'Ldap Name',
					'mail' => ['ldap@example.com', 'email@example.com'],
					'c' => 'XY'
				],
				'setValues' => [
					'realName' => 'Ldap Name',
					'email' => 'email@example.com',
					'country' => 'XY'
				],
			],
			[ //existing values, new multi values attributes
				'name' => 'Some Name',
				'email' => 'old_email@example.com',
				'country' => 'XX',
				'ldapAttributes' => [
					'cn' => ['Ldap Name', 'Other Name'],
					'mail' => ['ldap@example.com', 'email@example.com'],
					'c' => ['XY', 'XZ']
				],
				'setValues' => [
					'realName' => 'Ldap Name',
					'email' => 'ldap@example.com',
					'country' => 'XY'
				],
			],
		];
	}
}
