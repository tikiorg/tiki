<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once "lib/wiki/pluginslib.php";

function wikiplugin_titlesearch_info()
{
	return [
		'name' => tra('Title Search'),
		'documentation' => 'PluginTitleSearch',
		'description' => tra('Search page titles'),
		'prefs' => [ 'feature_wiki', 'wikiplugin_titlesearch' ],
		'iconname' => 'search',
		'introduced' => 1,
		'params' => [
			'search' => [
				'required' => true,
				'name' => tra('Search Criteria'),
				'description' => tra('Portion of a page name.'),
				'since' => '1',
				'filter' => 'text',
				'default' => '',
			],
			'info' => [
				'required' => false,
				'name' => tra('Information'),
				'description' => tra('Also show page hits or user'),
				'since' => '1',
				'default' => '',
				'filter' => 'alpha',
				'separator' => '|',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Hits'), 'value' => 'hits'],
					['text' => tra('User'), 'value' => 'user'],
					['text' => tra('Hits and user'), 'value' => 'hits|user'],
					['text' => tra('User and hits'), 'value' => 'user|hits']
				]
			],
			'exclude' => [
				'required' => false,
				'name' => tra('Exclude'),
				'description' => tra('Pipe-separated list of page names to exclude from results.'),
				'since' => '1',
				'default' => '',
				'filter' => 'text',
				'separator' => '|',
				'profile_reference' => 'wiki_page',
			],
			'noheader' => [
				'required' => false,
				'name' => tra('No Header'),
				'description' => tr('Set to Yes (%0) to have no header for the search results.', '<code>1</code>'),
				'since' => '1',
				'filter' => 'digits',
				'default' => 0,
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Yes'), 'value' => 1],
					['text' => tra('No'), 'value' => 0]
				]
			],
		],
	];
}
class WikiPluginTitleSearch extends PluginsLib
{
	var $expanded_params = ["exclude", "info"];
	function getDescription()
	{
		return wikiplugin_titlesearch_help();
	}
	function getDefaultArguments()
	{
		return ['exclude' => '',
			'noheader' => 0,
			'info' => false,
			'search' => false,
				'style' => 'table'
		];
	}
	function getName()
	{
		return "TitleSearch";
	}
	function getVersion()
	{
		return preg_replace("/[Revision: $]/", '', "\$Revision: 1.25 $");
	}
	function run($data, $params)
	{
		$wikilib = TikiLib::lib('wiki');
		$tikilib = TikiLib::lib('tiki');
		$aInfoPreset = array_keys($this->aInfoPresetNames);
		$exclude = ! empty($params['exclude']) ? $params['exclude'] : '';
		$params = $this->getParams($params, true);
		extract($params, EXTR_SKIP);
		if (! $search) {
			return $this->error("You have to define a search");
		}

		// no additional infos in list output
		if (isset($style) && $style == 'list') {
			$info = false;
		}

		//
		/////////////////////////////////
		// Create a valid list for $info
		/////////////////////////////////
		//
		if ($info) {
			$info_temp = [];
			foreach ($info as $sInfo) {
				if (in_array(trim($sInfo), $aInfoPreset)) {
					$info_temp[] = trim($sInfo);
				}
				$info = $info_temp ? $info_temp :
				false;
			}
		} else {
			$info = false;
		}
		//
		/////////////////////////////////
		// Process pages
		/////////////////////////////////
		//
		$sOutput = "";
		$aPages = $tikilib->list_pages(0, -1, 'pageName_desc', $search, null, false);
		foreach ($aPages["data"] as $idPage => $aPage) {
			if (! empty($exclude)) {
				if (in_array($aPage["pageName"], $exclude)) {
					unset($aPages["data"][$idPage]);
					$aPages["cant"]--;
				}
			}
		}
		//
		/////////////////////////////////
		// Start of Output
		/////////////////////////////////
		//
		if (isset($noheader) && ! $noheader) {
			// Create header
			$count = $aPages["cant"];
			if (! $count) {
				$sOutput  .= tra("No pages found for title search") . " '__" . $search . "__'";
			} elseif ($count == 1) {
				$sOutput  .= tra("One page found for title search") . " '__" . $search . "__'";
			} else {
				$sOutput = "$count " . tra("pages found for title search") . " '__" . $search . "__'";
			}
			$sOutput  .= "\n";
		}
		if (isset($style) && $style == 'list') {
			$sOutput .= PluginsLibUtil::createList($aPages["data"]);
		} else {
			$sOutput .= PluginsLibUtil::createTable($aPages["data"], $info);
		}
		return $sOutput;
	}
}
function wikiplugin_titlesearch($data, $params)
{
	$plugin = new WikiPluginTitleSearch();
	return $plugin->run($data, $params);
}
