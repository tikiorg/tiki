<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Controller.php 50732 2014-04-10 12:42:56Z lphuberdeau $

class Services_Search_Controller
{
	function action_help($input)
	{
		return [
			'title' => tr('Help'),
		];
	}

	function action_rebuild($input)
	{
		Services_Exception_Denied::checkGlobal('admin');

		$unifiedsearchlib = TikiLib::lib('unifiedsearch');
		$stat = null;

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$stat = $unifiedsearchlib->rebuild(isset($_REQUEST['loggit']));

			TikiLib::lib('cache')->empty_type_cache('search_valueformatter');
		}

		return [
			'title' => tr('Rebuild Index'),
			'stat' => $stat,
			'queue_count' => $unifiedsearchlib->getQueueCount(),
		];
	}

	function action_process_queue($input)
	{
		Services_Exception_Denied::checkGlobal('admin');

		$batch = $input->batch->int() ?: 50;

		$unifiedsearchlib = TikiLib::lib('unifiedsearch');
		$stat = null;

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			@ini_set('max_execution_time', 0);
			@ini_set('memory_limit', -1);
			$stat = $unifiedsearchlib->processUpdateQueue($batch);
		}

		return [
			'title' => tr('Process Update Queue'),
			'stat' => $stat,
			'queue_count' => $unifiedsearchlib->getQueueCount(),
			'batch' => $batch,
		];
	}

	function action_lookup($input)
	{
		try {
			$lib = TikiLib::lib('unifiedsearch');
			$query = $lib->buildQuery($input->filter->none());
			$query->setOrder('title_asc');
			$query->setRange($input->offset->int(), $input->maxRecords->int() ?: $prefs['maxRecords']);
			$result = $query->search($lib->getIndex());

			$format = $input->format->text() ?: '{title}';

			$result->applyTransform(function ($item) use ($format) {
				return [
					'object_type' => $item['object_type'],
					'object_id' => $item['object_id'],
					'title' => preg_replace_callback('/\{(\w+)\}/', function ($matches) use ($item) {
						$key = $matches[1];
						if (isset($item[$key])) {
							return $item[$key];
						} else {
							return tr('empty');
						}
					}, $format),
				];
			});

			return [
				'title' => tr('Lookup Result'),
				'resultset' => $result,
			];
		} catch (Search_Elastic_TransportException $e) {
			throw new Services_Exception_NotAvailable('Search functionality currently unavailable.');
		} catch (Exception $e) {
			throw new Services_Exception_NotAvailable($e->getMessage());
		}
	}
}

