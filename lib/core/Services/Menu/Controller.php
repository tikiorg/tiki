<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Menu_Controller
{
	/** @var  MenuLib */
	private $menulib;

	function setUp() {
		$this->menulib = TikiLib::lib('menu');
	}

	/**
	 * @param JitFilter $input
	 * @return mixed
	 */
	function action_get_menu($input)
	{
		$menuId = $input->menuId->int();
		return $this->menulib->get_menu($menuId);
	}

	/**
	 * @param JitFilter $input
	 * @return array
	 * @throws Services_Exception_Denied
	 * @throws Services_Exception_MissingValue
	 * @throws Services_Exception_NotFound
	 */
	function action_manage($input)
	{
		$util = new Services_Utilities();
		$util->checkTicket();

		//get menu details
		$menuId = $input->menuId->int();
		$info = $this->menulib->get_menu($menuId);

		if (! $info) {
			throw new Services_Exception_NotFound(tr('Menu %0 notfound', $menuId));
		}

		//check permissions
		$perms = Perms::get('menu', $menuId);
		if (! $perms->tiki_p_edit_menu) {
			throw new Services_Exception_Denied(tr("You don't have permission to edit menus (tiki_p_edit_menu)"));
		}

		$symbol = Tiki_Profile::getObjectSymbolDetails('menu', $menuId);

		//execute menu insert/update
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && $input->confirm->int() && $util->access->ticketMatch()) {
			$menuId = $input->menuId->int();
			$name = $input->name->text();
			$description = $input->description->text();
			$type = $input->type->text();
			$icon = $input->icon->text();
			$use_items_icons = $input->use_items_icons->int() ? 'y' : 'n';
			$parse = $input->parse->int() ? 'y' : 'n';

			if (! $name) {
				throw new Services_Exception_MissingValue('name');
			}
			$success = $this->menulib->replace_menu($menuId, $name, $description, $type, $icon, $use_items_icons, $parse);
		}

		//information for the menu management screen
		return [
			'title' => $info['menuId'] ? tr('Edit Menu') : tr('Create Menu'),
			'info' => $info,
			'symbol' => $symbol,
		];
	}

	function action_clone_menu($input)
	{
		$menuId = $input->menuId->int();
		$menuDetails = $this->get_menu_details($menuId);

		if (! $menuDetails) {
			throw new Services_Exception_NotFound(tr('Menu %0 notfound', $menuId));
		}

		//check permissions
		$perms = Perms::get('menu', $menuId);
		if (! $perms->tiki_p_edit_menu_options) {
			throw new Services_Exception_Denied(tr('Permission denied'));
		}


		//execute menu cloning
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && $input->confirm->int()) {
			$this->menulib->clone_menu($menuId, $input->name->text(), $input->description->text());
			return [];
		}

		//prepare basic data
		$info = $this->menulib->get_menu($menuId);
		$symbol = Tiki_Profile::getObjectSymbolDetails('menu', $menuId);

		//information for the clone menu screen
		return [
			'title' => tr('Clone this menu and its options'),
			'info' => $info,
			'symbol' => $symbol,
		];
	}

	/**
	 * Display menu option edit form and process replace
	 *
	 * @param JitFilter $input
	 * @return array
	 * @throws Services_Exception_Denied
	 * @throws Services_Exception_MissingValue
	 * @throws Services_Exception_NotFound
	 */
	function action_manage_option($input)
	{
		global $prefs;

		$menuLib = $this->menulib;
		$userLib = TikiLib::lib('user');
		$headerlib = TikiLib::lib('header');

		//prepare basic data
		$optionId = $input->optionId->int();
		if ($optionId) {
			$optionInfo = $menuLib->get_menu_option($optionId);

			if (! $optionInfo) {
				throw new Services_Exception_NotFound(tr('Menu option %0 not found', $optionId));
			}
		} else {
			$optionInfo = [];
		}

		//get menu information
		$menuId = $input->menuId->int();

		if (! $menuId && isset($optionInfo['menuId'])) {
			$menuId = $optionInfo['menuId'];
		}

		$menuDetails = $this->get_menu_details($menuId);

		if (! $menuDetails) {
			throw new Services_Exception_NotFound(tr('Menu %0 notfound', $menuId));
		}

		//check permissions
		$perms = Perms::get('menu', $menuId);
		if (! $perms->tiki_p_edit_menu_options) {
			throw new Services_Exception_Denied(tr('Permission denied'));
		}

		//get usergroup information
		if (! empty($optionInfo['groupname'])) {
			if (! is_array($optionInfo['groupname'])) {
				$optionInfo['groupname'] = explode(',', $optionInfo['groupname']);
			}
		} else {
			$optionInfo['groupname'] = [];
		}


		// groups info
		$all_groups = $userLib->list_all_groups();
		$option_groups = [];

		if (is_array($all_groups)) {
			foreach ($all_groups as $g) {
				if (in_array($g, $optionInfo['groupname'])) {
					$option_groups[$g] = 'selected="selected"';
				} else {
					$option_groups[$g] = '';
				}
			}
		}

		//get preference information
		$feature_prefs = [];
		foreach ($prefs as $k => $v) {	// attempt to filter out non-feature prefs (still finds 133!)
			if (strpos($k, 'feature') !== false && preg_match_all('/_/m', $k, $m) === 1) {
				$feature_prefs[] = $k;
			}
		}
		$headerlib->add_js('var prefNames = ' . json_encode($feature_prefs) . ';');

		//get permission information
		$headerlib->add_js('var permNames = ' . json_encode($userLib->get_permission_names_for('all')) . ';');

		$util = new Services_Utilities();
		$util->checkTicket();

		//perform insert or update
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && $input->confirm->int() && $util->access->ticketMatch()) {
			//check necessary permissions
			if (! Perms::get('menu', $menuId)->tiki_p_edit_menu_options) {
				throw new Services_Exception_Denied(tr("You don't have permission to edit menu options (tiki_p_edit_menu_option)"));
			}

			$name = $input->name->text();
			if (! $name) {
				throw new Services_Exception_MissingValue('name');
			}

			$url = $input->url->text();
			$type = $input->type->text();
			$position = $input->position->int();
			$section = $input->section->text();
			$perm = $input->perm->text();
			$groupname = $input->asArray('groupname');
			$groupname = implode(',', $groupname);
			$level = $input->level->text();
			$icon = $input->icon->text();
			$class = $input->class->text();

			//execute insert/update
			$menuLib = $this->menulib;
			$menuLib->replace_menu_option($menuId, $optionId, $name, $url, $type, $position, $section, $perm, $groupname, $level, $icon, $class);
		}

		//information for the menu option screen
		return [
			'title' => $optionId ? tr('Menu Option %0', $optionInfo["optionId"]) : tr('Create Menu Option'),
			'optionId' => $optionId,
			'menuId' => $menuId,
			'menuInfo' => $menuDetails["info"],
			'menuSymbol' => $menuDetails["symbol"],
			'info' => $optionInfo,
			'option_groups' => $option_groups,
		];
	}

	/**
	 * @param JitFilter $input
	 * @return array
	 * @throws Services_Exception_Denied
	 * @throws Services_Exception_NotFound
	 */
	function action_export_menu_options($input)
	{
		//get basic input
		$menuId = $input->menuId->int();
		$menuDetails = $this->get_menu_details($menuId);

		if (! $menuDetails) {
			throw new Services_Exception_NotFound(tr('Menu %0 notfound', $menuId));
		}

		//check permissions
		$perms = Perms::get('menu', $menuId);
		if (! $perms->tiki_p_edit_menu_options) {
			throw new Services_Exception_Denied(tr('Permission denied'));
		}


		//perform menu export
		$confirm = $input->confirm->int();
		if ($confirm) {
			$menuId = $input->menuId->int();
			$encoding = $input->encoding->text();
			$menuLib = $this->menulib;
			$menuLib->export_menu_options($menuId, $encoding);
			return [
				'confirm' => $confirm,
			];
		}

		//information for the export menu screen
		return [
			'title' => tr('Export Menu Options'),
			'menuId' => $menuId,
			'menuInfo' => $menuDetails["info"],
			'menuSymbol' => $menuDetails["symbol"],
		];
	}

	/**
	 * @param JitFilter $input
	 * @return array
	 * @throws Services_Exception_Denied
	 * @throws Services_Exception_NotFound
	 */
	function action_import_menu_options($input)
	{
		//get menu details
		$menuId = $input->menuId->int();
		$menuDetails = $this->get_menu_details($menuId);

		if (! $menuDetails) {
			throw new Services_Exception_NotFound(tr('Menu %0 notfound', $menuId));
		}

		//check permissions
		$perms = Perms::get('menu', $menuId);
		if (! $perms->tiki_p_edit_menu_options) {
			throw new Services_Exception_Denied(tr('Permission denied'));
		}

		//execute import
		$confirm = $input->confirm->int();
		if ($confirm) {
			$menuId = $input->menuId->int();
			$menuLib = $this->menulib;
			$menuLib->import_menu_options($menuId);
		}

		//information for the import menu screen
		return [
			'title' => tr('Import Menu Options'),
			'menuId' => $menuId,
			'menuInfo' => $menuDetails["info"],
			'menuSymbol' => $menuDetails["symbol"],
		];
	}

	/**
	 *
	 * @param JitFilter $input
	 * @return array
	 * @throws Services_Exception_Denied
	 * @throws Services_Exception_NotFound
	 */
	function action_preview_menu($input)
	{
		//get menu details
		$menuId = $input->menuId->int();
		$menuDetails = $this->get_menu_details($menuId);

		if (! $menuDetails) {
			throw new Services_Exception_NotFound(tr('Menu %0 notfound', $menuId));
		}

		//check permissions
		$perms = Perms::get('menu', $menuId);
		if (! $perms->tiki_p_edit_menu_option) {
			throw new Services_Exception_Denied(tr("You don't have permission to edit menu options (tiki_p_edit_menu_option)"));
		}

		//preview options, see function.menu.php
		$refresh = $input->refresh->int();
		if ($refresh) {
			$preview_type = $input->preview_type->text();
			$preview_css = $input->preview_css->text();
			$preview_bootstrap = $input->preview_bootstrap->text();
		} else {
			$preview_type = 'vert';
			$preview_css = 'n';
			$preview_bootstrap = 'n';
		}

		//information for the preview menu screen
		return [
			'title' => tr('Menu Preview'),
			'menuId' => $menuId,
			'menuInfo' => $menuDetails["info"],
			'menuSymbol' => $menuDetails["symbol"],
			'preview_type' => $preview_type,
			'preview_css' => $preview_css,
			'preview_bootstrap' => $preview_bootstrap,
		];
	}

	/**
	 * Saves all options in a menu
	 *
	 * @param JitFilter $input
	 * @return array
	 * @throws Services_Exception_Denied
	 * @throws Services_Exception_NotFound
	 */
	function action_save_menu($input)
	{
		$util = new Services_Utilities();
		$util->checkTicket();

		//get menu details
		$menuId = $input->menuId->int();
		$menuDetails = $this->get_menu_details($menuId);

		if (! $menuDetails) {
			throw new Services_Exception_NotFound(tr('Menu %0 notfound', $menuId));
		}

		//check permissions
		$perms = Perms::get('menu', $menuId);
		if (! $perms->tiki_p_edit_menu_options) {
			throw new Services_Exception_Denied(tr("You don't have permission to edit menu options (tiki_p_edit_menu_option)"));
		}

		if ($_SERVER['REQUEST_METHOD'] === 'POST' && $util->access->ticketMatch()) {

			$oldOptions = $this->menulib->list_menu_options($menuId);
			$options = json_decode($input->data->striptags(), true);

			foreach ($options as $option) {
				$optionId = $option['optionId'];
				if ($optionId) {
					$oldOption = $this->menulib->get_menu_option($optionId);
				} else {
					$optionId = 0;
					$oldOption = [
						'name' => '',
						'url' => '',
						'type' => 'o',
						'position' => 1,
						'section' => '',
						'perm' => '',
						'groupname' => '',
						'userlevel' => 0,
						'icon' => '',
						'class' => ''
					];
				}

				$option = array_merge($oldOption, $option);

				$this->menulib->replace_menu_option(
					$menuId,
					$optionId,
					$option['name'],
					$option['url'],
					$option['type'],
					$option['position'],
					$option['section'],
					$option['perm'],
					$option['groupname'],
					$option['userlevel'],
					$option['icon'],
					$option['class']
				);
			}

			$optionsToRemove = array_filter($oldOptions['data'], function ($item) use ($options) {
				foreach ($options as $option) {
					if ($option['optionId'] == $item['optionId']) {
						return false;    // still here
					}
				}
				return true;    // gone
			});

			foreach ($optionsToRemove as $item) {
				$this->menulib->remove_menu_option($item['optionId']);
			}
		}

		return ['menuId' => $menuId];
	}

	private function get_menu_details($menuId)
	{
		//get menu information
		$menuInfo = $this->menulib->get_menu($menuId);

		if ($menuInfo) {

			//get related symbol information
			$menuSymbol = Tiki_Profile::getObjectSymbolDetails('menu', $menuId);

			//return menu details
			return [
				'info' => $menuInfo,
				'symbol' => $menuSymbol,
			];

		} else {
			return [];
		}
	}
}
