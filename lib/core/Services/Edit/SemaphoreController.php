<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Class Services_Semaphore_Controller
 *
 * Controller for warning on edit conflicts
 *
 */
class Services_Edit_SemaphoreController
{
	/** @var  TikiDb_Table */
	private $table;

	function setUp()
	{
		Services_Exception_Disabled::check('feature_wiki');
		Services_Exception_Disabled::check('feature_warn_on_edit');

		$this->table = TikiDb::get()->table('tiki_semaphores');
	}

	/**
	 * @param JitFilter $input
	 * @return mixed
	 */
	function action_set($input)
	{
		global $user;

		if ($user == '') {
			$user = 'anonymous';
		}

		$object_id = $input->object_id->pagename();
		$object_type = $input->object_type->pagename();
		$object_type = $object_type ? $object_type : 'wiki page';

		$now = TikiLib::lib('tiki')->now;

		$this->table->delete(array('semName' => $object_id, 'objectType' => $object_type));
		$this->table->insert(
			array(
				'semName' => $object_id,
				'objectType' => $object_type,
				'timestamp' => $now,
				'user' => $user,
			)
		);

		$_SESSION[$this->getSessionId($input)] = $now;

		return $now;
	}

	/**
	 * @param JitFilter $input
	 * @return mixed
	 */
	function action_unset($input)
	{
		$object_id = $input->object_id->pagename();
		$object_type = $input->object_type->pagename();
		$object_type = $object_type ? $object_type : 'wiki page';
		$lock = $input->lock->int();

		$lock = $lock ? $lock : $_SESSION[$this->getSessionId($input)];

		$this->table->delete(
			[
				'semName' => $object_id,
				'timestamp' => (int)$lock,
				'objectType' => $object_type,
			]
		);

		unset($_SESSION[$this->getSessionId($input)]);
	}

	/**
	 * @param JitFilter $input
	 * @return mixed
	 */
	function action_is_set($input)
	{
		global $prefs;

		$object_id = $input->object_id->pagename();
		$object_type = $input->object_type->pagename();
		$object_type = $object_type ? $object_type : 'wiki page';
		$limit = $input->limit->int();

		if (! $limit) {
			$limit = (int)$prefs['warn_on_edit_time'] * 60;
		}

		$lim = TikiLib::lib('tiki')->now - $limit;

		// remove expired ones
		$this->table->deleteMultiple(array('timestamp' => $this->table->lesserThan((int)$lim)));

		return (
			$this->table->fetchCount([
				'semName' => $object_id,
				'objectType' => $object_type,
			]) > 0
		);
	}

	/**
	 * @param JitFilter $input
	 * @return mixed
	 */
	function action_get_user($input)
	{
		global $user;

		if (! $input->check->int()) {
			if (! $this->action_is_set($input)) {
				return '';
			}
		}

		$object_id = $input->object_id->pagename();
		$object_type = $input->object_type->pagename();
		$object_type = $object_type ? $object_type : 'wiki page';

		$semUser = $this->table->fetchOne(
			'user',
			[
				'semName' => $object_id,
				'objectType' => $object_type,
			]
		);

		if ($semUser && $semUser != $user || (! $user && $semUser === 'anonymous')) {
			return $semUser;
		} else {
			return $user;
		}

	}

	private function getSessionId($input) {
		$object_id = $input->object_id->pagename();
		$object_type = $input->object_type->pagename();
		$object_type = $object_type ? $object_type : 'wiki page';

		return 'semaphore_' .
			str_replace(' ', '_', TikiLib::remove_non_word_characters_and_accents($object_id)) . '_ ' .
			str_replace(' ', '_', $object_type);
	}

}


