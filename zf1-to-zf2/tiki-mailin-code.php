<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

use Tiki\MailIn;

require_once ('tiki-setup.php');
$access->check_script($_SERVER["SCRIPT_NAME"], basename(__FILE__));
include_once ("lib/webmail/tikimaillib.php");

$mailinlib = TikiLib::lib('mailin');

// Get a list of ACTIVE emails accounts configured for mailin procedures
$accs = $mailinlib->list_active_mailin_accounts(0, -1, 'account_desc', '');

// foreach account
foreach ($accs['data'] as $acc) {
	if (empty($acc['account'])) {
		continue;
	}

	$account = MailIn\Account::fromDb($acc);
	$account->check();
}
