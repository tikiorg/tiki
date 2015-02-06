<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (! defined('ADODB_FORCE_NULLS')) define('ADODB_FORCE_NULLS', 1);
if (! defined('ADODB_ASSOC_CASE')) define('ADODB_ASSOC_CASE', 2);
if (! defined('ADODB_CASE_ASSOC')) define('ADODB_CASE_ASSOC', 2); // typo in adodb's driver for sybase?

class TikiDb_Initializer_Adodb
{
	function isSupported()
	{
		return class_exists('ADOConnection');
	}

	function getConnection(array $credentials)
	{
		$dbTiki = ADONewConnection('mysqli');

		if (!@$dbTiki->Connect($credentials['host'], $credentials['user'], $credentials['pass'], $credentials['dbs'])) {
			throw new Exception($dbTiki->ErrorMsg());
		}

		// Set the Client Charset
		if ($credentials['charset']) {
			@ $dbTiki->Execute("SET CHARACTER SET $client_charset");
		}

		return new TikiDb_Adodb($dbTiki);
	}
}

