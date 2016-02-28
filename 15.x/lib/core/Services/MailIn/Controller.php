<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_MailIn_Controller
{
	function setUp()
	{
		Services_Exception_Disabled::check('feature_mailin');

		$perms = Perms::get();
		if (! $perms->admin_mailin) {
			throw new Services_Exception_Denied(tr('Reserved for administrators.'));
		}
	}

	function action_replace_account($input)
	{
		$mailinlib = TikiLib::lib('mailin');
		$accountId = $input->accountId->int();	// array('html' => $result);

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$account = [
				'protocol' => $input->protocol->word(),
				'host' => $input->host->url(),
				'port' => $input->port->int(),
				'username' => $input->username->text(),
				'pass' => $input->pass->none(),
			];
			try {
				if (! Tiki\MailIn\Account::test($account)) {
					throw new Services_Exception_FieldError('username', tr('Failed to connect or authenticate with remote host.'));
				}
			} catch (Exception $e) {
				throw new Services_Exception_FieldError('username', tr('Failed to connect or authenticate with remote host. Error "%0"', $e->getMessage()));
			}

			$mailinlib->replace_mailin_account(
				$accountId,
				$input->account->text(),
				$input->protocol->word(),
				$input->host->url(),
				$input->port->int(),
				$input->username->text(),
				$input->pass->none(),
				$input->type->text(),
				$input->active->int() ? 'y' : 'n',
				$input->anonymous->int() ? 'y' : 'n',
				$input->admin->int() ? 'y' : 'n',
				$input->attachments->int() ? 'y' : 'n',
				$input->routing->int() ? 'y' : 'n',
				$input->article_topicId->int(),
				$input->article_type->text(),
				$input->discard_after->text(),
				$input->show_inlineImages->int() ? 'y' : 'n',
				$input->save_html->int() ? 'y' : 'n',
				$input->categoryId->int(),
				$input->namespace->pagename(),
				$input->respond_email->int() ? 'y' : 'n',
				$input->leave_email->int() ? 'y' : 'n'
			);
		}

		$info = $mailinlib->get_mailin_account($accountId);
		$artlib = TikiLib::lib('art');
		return [
			'title' => $info ? tr('Modify Account') : tr('Create Account'),
			'types' => $artlib->list_types(),
			'topics' => $artlib->list_topics(),
			'accountId' => $accountId,
			'mailinTypes' => $mailinlib->list_available_types(),
			'info' => $info ?: [
				'account' => '',
				'username' => '',
				'pass' => '',
				'protocol' => 'pop',
				'host' => '',
				'port' => 110,
				'type' => 'wiki-put',
				'active' => 'y',
				'anonymous' => 'n',
				'admin' => 'y',
				'attachments' => 'y',
				'routing' => 'y',
				'article_topicId' => '',
				'article_type' => '',
				'show_inlineImages' => 'y',
				'save_html' => 'y',
				'categoryId' => 0,
				'namespace' => '',
				'respond_email' => 'y',
				'leave_email' => 'n',
			],
		];
	}

	function action_remove_account($input)
	{
		$mailinlib = TikiLib::lib('mailin');
		$accountId = $input->accountId->int();	// array('html' => $result);
		$info = $mailinlib->get_mailin_account($accountId);

		if (! $info) {
			throw new Services_Exception_NotFound;
		}

		$success = false;
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$mailinlib->remove_mailin_account($accountId);
			$success = true;
		}

		return [
			'title' => tr('Remove Account'),
			'info' => $info,
			'success' => $success,
		];
	}
}

