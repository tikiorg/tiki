<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

use \Symfony\Component\Console\Helper\FormatterHelper;

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
		global $num_queries;

		Services_Exception_Denied::checkGlobal('admin');

		$timer = new \timer();
		$timer->start();

		$memory_peak_usage_before = memory_get_peak_usage();

		$num_queries_before = $num_queries;

		$unifiedsearchlib = TikiLib::lib('unifiedsearch');
		$stat = null;

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$stat = $unifiedsearchlib->rebuild(isset($_REQUEST['loggit']));

			TikiLib::lib('cache')->empty_type_cache('search_valueformatter');

			// Also rebuild admin index
			TikiLib::lib('prefs')->rebuildIndex();
		}

		$num_queries_after = $num_queries;

		list($engine, $version) = $unifiedsearchlib->getEngineAndVersion();

		return [
			'title' => tr('Rebuild Index'),
			'stat' => $stat,
			'search_engine' => $engine,
			'search_version' => $version,
			'queue_count' => $unifiedsearchlib->getQueueCount(),
			'execution_time' => FormatterHelper::formatTime($timer->stop()),
			'memory_usage' => FormatterHelper::formatMemory(memory_get_usage()),
			'memory_peak_usage_before' => FormatterHelper::formatMemory($memory_peak_usage_before),
			'memory_peak_usage_after' => FormatterHelper::formatMemory(memory_get_peak_usage()),
			'num_queries' => ($num_queries_after - $num_queries_before),
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
		global $prefs;

		try {
			$filter = $input->filter->none() ?: [];
			$format = $input->format->text() ?: '{title}';

			$lib = TikiLib::lib('unifiedsearch');

			if (! empty($filter['title']) && preg_match_all('/\{(\w+)\}/', $format, $matches)) {
				// formatted object_selector search results should also search in formatted fields besides the title
				$titleFilter = $filter['title'];
				unset($filter['title']);
				$query = $lib->buildQuery($filter);
				$query->filterContent($titleFilter, $matches[1]);
			} else {
				$query = $lib->buildQuery($filter);
			}

			$query->setOrder($input->sort_order->text() ?: 'title_asc');
			$query->setRange($input->offset->int(), $input->maxRecords->int() ?: $prefs['maxRecords']);

			$result = $query->search($lib->getIndex());

			$result->applyTransform(function ($item) use ($format) {
				return [
					'object_type' => $item['object_type'],
					'object_id' => $item['object_id'],
					'title' => preg_replace_callback('/\{(\w+)\}/', function ($matches) use ($item) {
						$key = $matches[1];
						if (isset($item[$key])) {
							// if this is a trackeritem we do not want only the name but also the trackerid listed when setting up a field
							// otherwise its hard to distingish which field that is if multiple tracker use the same fieldname
							// example: setup of trackerfield item-link: choose some fields from a list. currently this list show all fields of all trackers
							if ($item['object_type'] == 'trackerfield') {
								return $item[$key] . ' (Tracker-' . $item['tracker_id'] . ')';
							} else {
								return $item[$key];
							}
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
