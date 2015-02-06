<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\MailIn\Action;
use Tiki\MailIn\Account;
use Tiki\MailIn\Source\Message;
use TikiLib;

class Comment implements ActionInterface
{
	private $type;
	private $object;

	function __construct($args)
	{
		$this->type = $args['type'];
		$this->object = $args['object'];
	}

	function getName()
	{
		return tr('Comment');
	}
	
	function isEnabled()
	{
		$service = new \Services_Comment_Controller;
		return $service->isEnabled($this->type, $this->object);
	}

	function isAllowed(Account $account, Message $message)
	{
		$service = new \Services_Comment_Controller;
		return $service->canPost($this->type, $this->object);
	}

	function execute(Account $account, Message $message)
	{
		$body = $message->getHtmlBody();
		$body = $account->parseBody($body, false);

		$commentslib = TikiLib::lib('comments');
		$message_id = ''; // By ref
		$threadId = $commentslib->post_new_comment(
			"{$this->type}:{$this->object}",
			0,
			$message->getAssociatedUser(),
			$message->getSubject(),
			$body['body'],
			$message_id,
			'',
			'n',
			'',
			'',
			'',
			'',
			'',
			'',
			''
		);

		return true;
	}
}

