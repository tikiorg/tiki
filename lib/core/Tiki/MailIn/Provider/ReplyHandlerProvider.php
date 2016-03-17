<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\MailIn\Provider;
use Tiki\MailIn\Action;

class ReplyHandlerProvider implements ProviderInterface
{
	function isEnabled()
	{
		global $prefs;
		return ! empty($prefs['monitor_reply_email_pattern']);
	}

	function getType()
	{
		return 'reply-handler';
	}

	function getLabel()
	{
		return tr('Reply Handler');
	}

	function getActionFactory(array $acc)
	{
		return new Action\RecipientPlaceholderFactory(array(
			'comment' => 'Tiki\MailIn\Action\Comment',
		));
	}
}
