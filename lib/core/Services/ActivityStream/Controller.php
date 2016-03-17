<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_ActivityStream_Controller
{
	private $lib;

	function setUp()
	{
		$this->lib = TikiLib::lib('unifiedsearch');
		Services_Exception_Disabled::check('wikiplugin_activitystream');
	}

	function action_render(JitFilter $request)
	{
		$encoded = $request->stream->none();
		$page = $request->page->int() ?: 1;

		if (! $baseQuery = Tiki_Security::get()->decode($encoded)) {
			throw new Services_Exception_Denied('Invalid request performed.');
		}

		$query = new Search_Query;
		$this->lib->initQuery($query);
		$query->filterType('activity');

		$matches = WikiParser_PluginMatcher::match($baseQuery['body']);

		$builder = new Search_Query_WikiBuilder($query);
		$builder->enableAggregate();
		$builder->apply($matches);

		if ($builder->isNextPossible()) {
			$query->setPage($page);
		}

		$query->setOrder('modification_date_desc');

		if (! $index = $this->lib->getIndex()) {
			throw new Services_Exception_NotAvailable(tr('Activity stream currently unavailable.'));
		}

		$result = $query->search($index);

		$paginationArguments = $builder->getPaginationArguments();

		$resultBuilder = new Search_ResultSet_WikiBuilder($result);
		$resultBuilder->setPaginationArguments($paginationArguments);
		$resultBuilder->apply($matches);

		try {
			$plugin = new Search_Formatter_Plugin_SmartyTemplate('activity/activitystream.tpl');
			$plugin->setFields(array(
				'like_list' => true,
				'user_groups' => true,
				'contributors' => true,
			));
			$formatter = new Search_Formatter($plugin);
			$out = $formatter->format($result);
		} catch (SmartyException $e) {
			throw new Services_Exception_NotAvailable($e->getMessage());
		}

		return array(
			'autoScroll' => $request->autoscroll->int(),
			'pageNumber' => $page,
			'nextPossible' => $builder->isNextPossible(),
			'stream' => $encoded,
			'body' => TikiLib::lib('parser')->parse_data($out, array('is_html' => true)),
		);
	}
}

