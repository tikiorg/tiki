<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

class StoredSearchLib
{
	public function storeUserQuery($user, $query)
	{
		$query = clone $query;

		$unifiedsearchlib = TikiLib::lib('unifiedsearch');
		$unifiedsearchlib->initQueryBase($query);
		$this->loadInIndex($user, $user, $query);
	}

	private function loadInIndex($user, $name, $query)
	{
		$unifiedsearchlib = TikiLib::lib('unifiedsearch');
		$index = $unifiedsearchlib->getIndex();

		if ($index) {
			$userlib = TikiLib::lib('user');
			$groups = array_keys($userlib->get_user_groups_inclusion($user));
			$query->filterPermissions($groups);

			$query->store($name, $index);
		}
	}
}

