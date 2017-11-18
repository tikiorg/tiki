<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
		Services_Exception_Denied::checkAuth();
	}

	function action_select($input)
	{
		$lib = TikiLib::lib('storedsearch');
		$queryId = null;

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$label = $input->label->text();
			$description = $input->description->wikicontent();
			$priority = $input->priority->word();
			$queryId = $input->queryId->int();

			if (! $queryId) {
				if ($label && $priority) {
					try {
						$queryId = $lib->createBlank($label, $priority, $description);
					} catch (TikiDb_Exception_DuplicateEntry $e) {
						throw new Services_Exception_FieldError('label', tr('A query could not be created because the name is already in use'));
					} catch (TikiDb_Exception $e) {
						throw new Services_Exception($e->getMessage(), 500);
					}
				}
			}

			if (! $queryId) {
				throw new Services_Exception(tr('Could not obtain the query.'), 406);
			}
		}

		return [
			'title' => tr('Save Search'),
			'priorities' => $lib->getPriorities(),
			'queries' => $lib->getUserQueries(),
			'queryId' => $queryId,
		];
	}

	function action_list($input)
	{
		$lib = TikiLib::lib('storedsearch');
		$results = null;
		$query = [
			'query' => null,
			'label' => null,
			'description' => null,
		];

		if ($queryId = $input->queryId->int()) {
			if ($query = $lib->getPresentedQuery($queryId)) {
				$resultset = $this->getResultSet($query['query']);
				$results = $this->renderResults($resultset);
			}
		}

		return [
			'title' => $query['label'] ?: tr('Saved Searches'),
			'priorities' => $lib->getPriorities(),
			'queries' => $lib->getUserQueries(),
			'queryId' => $queryId,
			'description' => $query['description'],
			'results' => $results,
			'url' => TikiLib::lib('service')->getUrl(['controller' => 'search_stored', 'action' => 'list']),
		];
	}

	function action_delete($input)
	{
		if (! $input->queryId->int()) {
			return [
				'FORWARD' => ['action' => 'list'],
			];
		}

		$lib = TikiLib::lib('storedsearch');
		if (! $data = $lib->getEditableQuery($input->queryId->int())) {
			throw new Services_Exception_NotFound('User query not found.');
		}

		$out = [
			'title' => tr('Delete Saved Search'),
			'success' => false,
			'queryId' => $data['queryId'],
			'label' => $data['label'],
			'lastModif' => $data['lastModif'],
		];

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$lib->deleteQuery($data);

			$out['success'] = true;
		}

		return $out;
	}

	function action_edit($input)
	{
		if (! $input->queryId->int()) {
			return [
				'FORWARD' => ['action' => 'list'],
			];
		}

		$lib = TikiLib::lib('storedsearch');
		if (! $data = $lib->getEditableQuery($input->queryId->int())) {
			throw new Services_Exception_NotFound('User query not found.');
		}

		$out = [
			'title' => tr('Edit Saved Search'),
			'success' => false,
			'queryId' => $data['queryId'],
			'label' => $data['label'],
			'description' => $data['description'],
			'priority' => $data['priority'],
			'priorities' => $lib->getPriorities(),
		];

		$label = $input->label->text();
		$priority = $input->priority->word();
		$description = $input->description->wikicontent();

		if ($_SERVER['REQUEST_METHOD'] == 'POST' && $label && $priority) {
			$out['success'] = true;

			try {
				$lib->updateQuery($data['queryId'], $label, $priority, $description);
			} catch (TikiDb_Exception_DuplicateEntry $e) {
				throw new Services_Exception_FieldError('label', tr('A query could not be created because the name is already in use'));
			} catch (TikiDb_Exception $e) {
				throw new Services_Exception($e->getMessage(), 500);
			}
		}

		return $out;
	}

	private function getResultSet($query)
	{
		try {
			$unifiedsearchlib = TikiLib::lib('unifiedsearch');

			return $query->search($unifiedsearchlib->getIndex());
		} catch (Search_Elastic_TransportException $e) {
			Feedback::error(tr('Search functionality currently unavailable.'), 'session');
		} catch (Exception $e) {
			Feedback::error($e->getMessage(), 'session');
		}
	}

	private function renderResults($resultset)
	{
		global $prefs;

		$unifiedsearchlib = TikiLib::lib('unifiedsearch');
		$dataSource = $unifiedsearchlib->getDataSource('formatting');

		$plugin = new Search_Formatter_Plugin_SmartyTemplate('searchresults-plain.tpl');
		$plugin->setData(
			[
				'prefs' => $prefs,
			]
		);
		$fields = [
			'title' => null,
			'url' => null,
			'modification_date' => null,
			'highlight' => null,
		];
		if ($prefs['feature_search_show_visit_count'] === 'y') {
			$fields['visits'] = null;
		}
		$plugin->setFields($fields);

		$formatter = Search_Formatter_Factory::newFormatter($plugin);

		$wiki = $formatter->format($resultset);
		$tikilib = TikiLib::lib('tiki');
		$results = TikiLib::lib('parser')->parse_data(
			$wiki,
			[
				'is_html' => true,
			]
		);

		return $results;
	}
}
