<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Controller.php 61553 2017-03-07 16:50:53Z jonnybradley $

/**
 * Class Services_Edit_PluginController
 *
 * Controller for editing and listing wiki plugins
 *
 */
class Services_Edit_PluginController
{
	private $pluginList;

	function __construct()
	{
		$this->parserlib = TikiLib::lib('parser');
	}

	function setUp()
	{
		Services_Exception_Disabled::check('feature_wiki');

		$this->pluginList = TikiLib::lib('wiki')->list_plugins(true);
	}


	function action_list($input)
	{
		$filter = $input->filter->text();
		$title = $input->title->text();
		$res = [];

		if ($filter) {
			$query = 'wikiplugin_* AND ' . $filter;
		} else {
			$query = 'wikiplugin_*';
		}
		$results = TikiLib::lib('prefs')->getMatchingPreferences($query);

		foreach($results as $result) {
			if (strpos($result, 'wikiplugin_') === 0) {
				$key = strtoupper(substr($result, 11));
				$arr = array_filter($this->pluginList, function ($plugin) use ($key) {
					return $plugin['name'] === $key;
				});

				foreach ($this->pluginList as $plugin) {
					if ($plugin['name'] === $key) {
						$res[strtolower($key)] = array_shift($arr);
						break;
					}
				}
			}
		}

		if (! $title) {
			if ($res) {
				$title = tr('Plugins found containing: %0', $filter);
			} else {
				$title = tr('No plugins found containing: %0', $filter);
			}
		}

		return array(
			'plugins' => $res,
			'title' => $title,
			'pref_filters' => TikiLib::lib('prefs')->getFilters(),
		);
	}

}


