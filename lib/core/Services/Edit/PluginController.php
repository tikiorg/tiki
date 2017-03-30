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
		$this->pluginList = [];
	}

	function setUp()
	{
		Services_Exception_Disabled::check('feature_wiki');

		$this->pluginList = TikiLib::lib('wiki')->list_plugins(true);
	}

	/**
	 * List all or some of theplugins for the textarea control panel
	 *
	 * @param JitFilter $input
	 * @return array
	 */
	function action_list($input)
	{
		$filter = $input->filter->text();
		$title = $input->title->text();
		$res = [];

		if ($filter) {
			$query = 'wikiplugin_* AND ' . $filter;
			$sort = '';
		} else {
			$query = 'wikiplugin_*';
			$sort = 'object_id_asc';
		}
		$filters = TikiLib::lib('prefs')->getFilters();
		$results = TikiLib::lib('prefs')->getMatchingPreferences($query, $filters, 500, $sort);

		foreach ($results as $result) {
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
			'pref_filters' => $filters,
		);
	}

	/**
	 * Display plugin edit form or process saving changes
	 *
	 * @param JitFilter $input
	 * @return array
	 * @throws Services_Exception_BadRequest
	 * @throws Services_Exception_Denied
	 */
	function action_edit($input)
	{
		global $prefs;

		$parserlib = TikiLib::lib('parser');

		$area_id = $input->area_id->alnumdash();
		$type = $input->type->word();
		$index = $input->index->int();
		$page = $input->page->pagename();
		$pluginArgs = $input->pluginArgs->array();
		$bodyContent = $input->bodyContent->wikicontent();
		$edit_icon = $input->edit_icon->int();
		$selectedMod = $input->selectedMod->text();

		$tikilib = TikiLib::lib('tiki');
		$pageInfo = $tikilib->get_page_info($page);
		if (! $pageInfo) {
			// in edit mode
		} else {
			$perms = $tikilib->get_perm_object($page, 'wiki page', $pageInfo, false);
			if ($perms['tiki_p_edit'] !== 'y') {
				throw new Services_Exception_Denied(tr('You do not have permission to edit "%0"', $page));
			}
		}

		Services_Exception_EditConflict::checkSemaphore($page);

		if ($edit_icon) {
			TikiLib::lib('service')->internal('semaphore', 'set', ['object_id' => $page]);
		}

		$util = new Services_Utilities();
		$util->checkTicket();
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && $util->access->ticketMatch()) {

			$this->action_replace($input);

			TikiLib::lib('service')->internal('semaphore', 'unset', ['object_id' => $page]);

			return [
				'redirect' => TikiLib::lib('wiki')->sefurl($page),
			];

		} elseif ($util->access->ticketSet()) {        // render the form

			$info = $parserlib->plugin_info($type);
			$info['advancedParams'] = [];
			$validationRules = [];
			$objectlib = TikiLib::lib('object');

			foreach ($info['params'] as $key => & $param) {
				if ($prefs['feature_jquery_validation'] === 'y') {
					// $("#insertItemForm4").validate({rules: { ins_11: { required: true}, ins_13: { remote: { url: "validate-ajax.php", type: "post", data: { validator: "distinct", parameter: "trackerId=4&fieldId=13&itemId=0", message: "", input: function() { return $("#ins_13").val(); } } } }, ins_18: { required: true, remote: { url: "validate-ajax.php", type: "post", data: { validator: "distinct", parameter: "trackerId=4&fieldId=18&itemId=0", message: "this is not distinct!", input: function() { return $("#ins_18").val(); } } } }}, messages: { ins_11: { required: "This field is required" }, ins_18: { required: "this is not distinct!" }},
					if ($param['required']) {
						if (empty($param['parent'])) {
							$validationRules["params[$key]"] = ['required' => true];
						} else {
							$validationRules["params[$key]"] = ['required_in_group' => [
								1,
								'.group-' . $param['parent']['name'],
								'other',
							]];
						}
					}
				}
				if (! empty($param['advanced']) && ! isset($pluginArgs[$key]) && empty($param['parent'])) {
					$info['advancedParams'][$key] = $param;
					unset($info['params'][$key]);
				}
				// set up object selectors - TODO refactor code with \PreferencesLib::getPreference and \Services_Tracker_Controller::action_edit_field
				if (isset($param['profile_reference'])) {
					$param['selector_type'] = $objectlib->getSelectorType($param['profile_reference']);
					if( isset($param['parent']) ) {
						if( !preg_match('/[\[\]#\.]/', $param['parent']) )
							$param['parent'] = "#option-{$param['parent']}";
					} else {
						$param['parent'] = null;
					}
					$param['parentkey'] = isset($param['parentkey']) ? $param['parentkey'] : null;
					$param['sort_order'] = isset($param['sort_order']) ? $param['sort_order'] : null;
				} else {
					$param['selector_type'] = null;
				}
			}

			if ($validationRules) {
				$rules = json_encode(['rules' => $validationRules]);
				TikiLib::lib('header')->add_jq_onready('$("#plugin_params > form").validate(' . $rules . ');');
			}

			if ($type === 'module' && isset($pluginArgs['module'])) {
				if ($selectedMod) {
					$pluginArgs['module'] = $selectedMod;
				}
				$file = 'modules/mod-func-' . $pluginArgs['module'] . '.php';
				if (file_exists($file)) {
					include_once($file);
					$info_func = "module_{$pluginArgs['module']}_info";
					if (function_exists($info_func)) {
						$moduleInfo = $info_func();
						if (isset($info['params']['max'])) {
							$max = $info['params']['max'];
							unset($info['params']['max']);    // move "max" to last
						}
						foreach ($moduleInfo['params'] as $key => $value) {
							$info['params'][$key] = $value;
						}
						if (! empty($max)) {
							$info['params']['max'] = $max;
						}
						// replace the module plugin description with the one from the select module
						$info['params']['module']['description'] = $moduleInfo['description'];
					}

				}
			}

			return [
				// pass back the input parameters
				'area_id' => $area_id,
				'type' => $type,
				'index' => $index,
				'pageName' => $page,
				'pluginArgs' => $pluginArgs,
				'pluginArgsJSON' => json_encode($pluginArgs),
				'bodyContent' => $bodyContent,
				'edit_icon' => $edit_icon,
				'selectedMod' => $selectedMod,

				'info' => $info,
				'title' => $info['name'],
				'ticket' => $util->access->getTicket(),
			];
		}
	}

	/**
	 * Replace plugin in wiki content
	 * Migrated from tiki-wikiplugin_edit.php
	 *
	 * @param JitFilter $input
	 * @return array
	 * @throws Services_Exception
	 * @throws Services_Exception_BadRequest
	 * @throws Services_Exception_Denied
	 */
	function action_replace($input)
	{
		global $user;

		$tikilib = TikiLib::lib('tiki');
		$parserlib = TikiLib::lib('parser');

		$page = $input->page->pagename();
		$type = $input->type->word();
		$message = $input->message->text();
		$content = $input->content->wikicontent();
		$index = $input->index->int();
		$params = $input->params->array();

		$referer = $_SERVER['HTTP_REFERER'];

		if (! $page || ! $type || ! $referer || $_SERVER['REQUEST_METHOD'] !== 'POST') {
			throw new Services_Exception(tr('Missing parameters'));
		}

		$plugin = strtolower($type);
		$meta = $parserlib->plugin_info($plugin);

		if (! $page || ! $type || ! $referer) {
			throw new Services_Exception(tr('Plugin "%0" not found', $plugin));
		}

		if (! $message) {
			$message = tr('%0 Plugin modified by editor.', $plugin);
		}

		$info = $tikilib->get_page_info($page);
		if (! $info) {
			throw new Services_Exception_BadRequest(tr('Page "%0" not found', $page));
		}

		$perms = $tikilib->get_perm_object($page, 'wiki page', $info, false);
		if ($perms['tiki_p_edit'] !== 'y') {
			throw new Services_Exception_Denied(tr('You do not have permission to edit "%0"', $page));
		}

		$current = $info['data'];

		$matches = WikiParser_PluginMatcher::match($current);
		$count = 0;
		foreach ($matches as $match) {
			if ($match->getName() !== $plugin) {
				continue;
			}

			++$count;

			if ($index === $count) {
				// by using content of "~same~", it will not replace the body that is there
				$content = ($content == "~same~" ? $match->getBody() : $content);

				if (! $params) {
					$params = $match->getArguments();
				}

				$match->replaceWithPlugin($plugin, $params, $content);

				$tikilib->update_page(
					$page,
					$matches->getText(),
					$message,
					$user,
					$tikilib->get_ip_address()
				);
				Feedback::success(tr('Plugin %0 on page %1 successfully edited.', $plugin, $page), 'session');

				break;
			}
		}

		return [];

	}

	/**
	 * Create the data for the list plugin GUI
	 *
	 * @param JitFilter $input
	 * @return array
	 */
	function action_list_edit($input)
	{

		$body = $input->body->wikicontent();
		$current = [];
		$done = [];	// to keep a track on whcih plugins have already been included

		$this->parsePlugins($body, $current, $done);


		$fields = TikiLib::lib('unifiedsearch')->getAvailableFields();

		return [
			'plugins' => Services_Edit_ListPluginHelper::getDefinition(),
			'fields' => $fields,
			'current' => $current,
		];
	}

	/**
	 * Recursively convert plugins to nested array
	 *
	 * @param string $body      wiki content to "parse"
	 * @param array $plugins    resulting nested array of plugins
	 * @param array $done       flat array to track plugins already added to $plugins
	 */
	private function parsePlugins($body, & $plugins, & $done) {
		$matches = WikiParser_PluginMatcher::match($body);
		$argumentParser = new WikiParser_PluginArgumentParser;

		/** @var WikiParser_PluginMatcher_Match $match */
		foreach($matches as $match) {

			$thisPlugin = [
				'name' => $match->getName(),
				'params' => $argumentParser->parse($match->getArguments()),
				'body' => $match->getBody(),
				'plugins' => []
			];

			$this->parsePlugins($match->getBody(), $thisPlugin['plugins'], $done);

			if (! in_array($thisPlugin, $done)) {
				$plugins[] = $thisPlugin;
				$done[] = $thisPlugin;
			}
		}

	}


}


