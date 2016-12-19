<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once "lib/wiki/pluginslib.php";

class WikiPluginBackLinks extends PluginsLib
{
	var $expanded_params = array("exclude", "info");
	function getDefaultArguments()
	{
		return array('exclude' => [],
				'include_self' => 0,
				'noheader' => 0,
				'page' => '[pagename]',
				'info' => false );
	}

	function getName()
	{
		return "BackLinks";
	}

	function getDescription()
	{
		return wikiplugin_backlinks_help();
	}

	function getVersion()
	{
		return preg_replace("/[Revision: $]/", '', "\$Revision: 1.17 $");
	}

	function run ($data, $params)
	{
		$wikilib = TikiLib::lib('wiki');
		$exclude = isset($params['exclude']) ? $params['exclude'] : [];
		$params = $this->getParams($params, true);
		$aInfoPreset = array_keys($this->aInfoPresetNames);
		extract($params, EXTR_SKIP);

		if (! isset($page)) {
			$page = null;
		}

		/////////////////////////////////
		// Create a valid list for $info
		/////////////////////////////////
		//
		if ($info) {
			$info_temp = array();
			foreach ($info as $sInfo) {
				if (in_array(trim($sInfo), $aInfoPreset)) {
					$info_temp[] = trim($sInfo);
				}
				$info = $info_temp?$info_temp:
					false;
			}
		}
		$sOutput = "";
		// Verify if the page exists
		if (!$wikilib->page_exists($page)) {
			return $this->error(tra("Page cannot be found")." : <b>$page</b>");
		}
		//
		/////////////////////////////////
		// Process backlinks
		/////////////////////////////////
		//

		$aBackRequest = array();
		$aBackLinks = $wikilib->get_backlinks($page);
		foreach ($aBackLinks as $backlink) {
			if (!in_array($backlink["fromPage"], $exclude)) {
				$aBackRequest[] = $backlink["fromPage"];
			}
		}
		if (isset($include_self) && $include_self) {
			$aBackRequest[] = $page;
		}
		if (!$aBackRequest) {
			return tra("No pages link to")." (($page))";
		} else {
			$aPages = $this->list_pages(0, -1, 'pageName_asc', $aBackRequest);
		}
		//
		/////////////////////////////////
		// Start of Output
		/////////////////////////////////
		//
		if (!isset($noheader) || !$noheader) {
			// Create header
			$count = $aPages["cant"];
			if ($count == 1) {
				$sOutput  .= tra("One page links to")." (($page))";
			} else {
				$sOutput = "$count ".tra("pages link to")." (($page))";
			}
			$sOutput  .= "\n";
		}
		$sOutput  .= PluginsLibUtil::createTable($aPages["data"], $info);
		return $sOutput;
	}
}

function wikiplugin_backlinks_info()
{
	return array(
		'name' => tra('Backlinks'),
		'documentation' => 'PluginBacklinks',
		'description' => tra('List all pages that link to a particular page'),
		'prefs' => array( 'feature_wiki', 'wikiplugin_backlinks' ),
		'iconname' => 'backlink',
		'introduced' => 1,
		'params' => array(
			'page' => array(
				'required' => false,
				'name' => tra('Page'),
				'description' => tra('The page links will point to. Default value is the current page.'),
				'since' => '1',
				'advanced' => true,
				'filter' => 'pagename',
				'default' => '[pagename]',
				'profile_reference' => 'wiki_page',
			),
			'info' => array(
				'required' => false,
				'name' => tra('Displayed Information'),
				'description' => tra('Pipe separated list of fields to display. ex: hits|user'),
				'since' => '1',
				'advanced' => true,
				'separator' => '|',
				'filter' => 'text',
				'default' => false,
			),
			'exclude' => array(
				'required' => false,
				'name' => tra('Excluded pages'),
				'description' => tr('Pipe-separated list of pages to be excluded from the listing, for example:
					%0HomePage|Sandbox%1', '<code>', '</code>'),
				'since' => '1',
				'advanced' => true,
				'default' => '',
				'separator' => '|',
				'filter' => 'pagename',
				'profile_reference' => 'wiki_page',
			),
			'include_self' => array(
				'required' => false,
				'name' => tra('Include Self'),
				'description' => tra('With or without self-link (default is without)'),
				'since' => '1',
				'advanced' => true,
				'filter' => 'digits',
				'default' => 0,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => '1'), 
					array('text' => tra('No'), 'value' => '0'), 
				),
			),
			'noheader' => array(
				'required' => false,
				'name' => tra('Header'),
				'description' => tra('With or without header (default is with header)'),
				'since' => '1',
				'filter' => 'digits',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('With header'), 'value' => '0'), 
					array('text' => tra('Without header'), 'value' => '1'), 
				),
			),
		),
	);
}

function wikiplugin_backlinks($data, $params)
{
    $plugin = new wikipluginbacklinks();
    return $plugin->run($data, $params);
}
