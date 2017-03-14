<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Exception_EditConflict extends Services_Exception
{
	function __construct($message = null)
	{
		if (is_null($message)) {
			$message = tr('Edit conflict');
		}

		parent::__construct($message, 403);
	}

	public static function checkSemaphore($object_id, $object_type = 'wiki page')
	{
		global $user, $prefs;

		if ($prefs['feature_warn_on_edit'] !== 'y') {
			return;
		}

		$otherUser = TikiLib::lib('service')->internal(
			'semaphore',
			'get_user',
			[
				'object_id' => $object_id,
				'object_type' => $object_type,
				'check' => 1,
			]
		);

		if ($user && $user !== $otherUser) {
			throw new self(tr('Edit conflict: %0 "%1" is being edited already by %2', $object_type, $object_id, $otherUser));
		}
	}
}

