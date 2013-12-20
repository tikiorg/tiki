<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Search_StoredController
{
	function setUp()
	{
		Services_Exception_Disabled::check('feature_search');
		Services_Exception_Disabled::check('storedsearch_enabled');
		Services_Exception_Denied::checkAuth('feature_search');
	}

	function action_select($input)
	{
		$lib = TikiLib::lib('storedsearch');
		$queryId = null;

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$label = $input->label->text();
			$priority = $input->priority->word();
			$queryId = $input->queryId->int();

			if (! $queryId) {
				if ($label && $priority) {
					$queryId = $lib->createBlank($label, $priority);
				}
			}

			if (! $queryId) {
				throw new Services_Exception(tr('Could not obtain the query.'), 406);
			}
		}

		return array(
			'title' => tr('Select Query'),
			'priorities' => $lib->getPriorities(),
			'queries' => $lib->getUserQueries(),
			'queryId' => $queryId,
		);
	}

	function action_list($input)
	{
		$lib = TikiLib::lib('storedsearch');

		return array(
			'title' => tr('Stored Queries'),
			'priorities' => $lib->getPriorities(),
			'queries' => $lib->getUserQueries(),
		);
	}

	function action_delete($input)
	{
		if (! $input->queryId->int()) {
			return array(
				'FORWARD' => ['action' => 'list'],
			);
		}

		$lib = TikiLib::lib('storedsearch');
		if (! $data = $lib->getEditableQuery($input->queryId->int())) {
			throw new Services_Exception_NotFound('User query not found.');
		}

		$out = array(
			'title' => tr('Delete User Query'),
			'success' => false,
			'queryId' => $data['queryId'],
			'label' => $data['label'],
			'lastModif' => $data['lastModif'],
		);

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$lib->deleteQuery($data);

			$out['success'] = true;
		}

		return $out;
	}
}
