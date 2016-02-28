<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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

		return array(
			'title' => tr('Save Search'),
			'priorities' => $lib->getPriorities(),
			'queries' => $lib->getUserQueries(),
			'queryId' => $queryId,
		);
	}

	function action_list($input)
	{
		$lib = TikiLib::lib('storedsearch');
		$results = null;
		$query = array(
			'query' => null,
			'label' => null,
			'description' => null,
		);

		if ($queryId = $input->queryId->int()) {
			if ($query = $lib->getPresentedQuery($queryId)) {
				$resultset = $this->getResultSet($query['query']);
				$results = $this->renderResults($resultset);
			}
		}

		return array(
			'title' => $query['label'] ?: tr('Saved Searches'),
			'priorities' => $lib->getPriorities(),
			'queries' => $lib->getUserQueries(),
			'queryId' => $queryId,
			'description' => $query['description'],
			'results' => $results,
			'url' => TikiLib::lib('service')->getUrl(['controller' => 'search_stored', 'action' => 'list']),
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
			'title' => tr('Delete Saved Search'),
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

	function action_edit($input)
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
			'title' => tr('Edit Saved Search'),
			'success' => false,
			'queryId' => $data['queryId'],
			'label' => $data['label'],
			'description' => $data['description'],
			'priority' => $data['priority'],
			'priorities' => $lib->getPriorities(),
		);

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
			TikiLib::lib('errorreport')->report('Search functionality currently unavailable.');
		} catch (Exception $e) {
			TikiLib::lib('errorreport')->report($e->getMessage());
		}
	}
	
	private function renderResults($resultset)
	{
		global $prefs;

		$unifiedsearchlib = TikiLib::lib('unifiedsearch');
		$dataSource = $unifiedsearchlib->getDataSource('formatting');

		$plugin = new Search_Formatter_Plugin_SmartyTemplate(realpath('templates/searchresults-plain.tpl'));
		$plugin->setData(
			array(
				'prefs' => $prefs,
			)
		);
		$fields = array(
			'title' => null,
			'url' => null,
			'modification_date' => null,
			'highlight' => null,
		);
		if ($prefs['feature_search_show_visit_count'] === 'y') {
			$fields['visits'] = null;
		}
		$plugin->setFields($fields);

		$formatter = new Search_Formatter($plugin);

		$wiki = $formatter->format($resultset);
		$tikilib = TikiLib::lib('tiki');
		$results = $tikilib->parse_data(
			$wiki,
			array(
				'is_html' => true,
			)
		);

		return $results;
	}
}
