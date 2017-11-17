<?php

function upgrade_20130608_convert_mailin_pwd_tiki($installer)
{
	$mailinlib = TikiLib::lib('mailin');

	$fields = $installer->fetchAll('SELECT accountId, pass FROM tiki_mailin_accounts ');
	foreach ($fields as $field) {
		$accountId = $field['accountId'];
		$pass = $field['pass'];

		$crypt = $mailinlib->encryptPassword($pass);

		$query = 'update tiki_mailin_accounts set pass=? where accountId=?';
		$params = [$crypt,  $accountId];
		$installer->query($query, $params);
	}
}
