<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Menu_Controller
{

	function action_get_menu($input)
	{
		$menuId = $input->menuId->int();
		return TikiLib::lib('menu')->get_menu($menuId);
	}

	function action_manage($input)
	{
		//check permissions
		$perms = Perms::get('menu');
		if (! $perms->tiki_p_edit_menu) {
			throw new Services_Exception_Denied(tr("You don't have permission to edit menus (tiki_p_edit_menu)"));
		}
		$util = new Services_Utilities();
		$util->checkTicket();

		//get menu details
		$menuId = $input->menuId->int();
		$info = TikiLib::lib('menu')->get_menu($menuId);
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
			$success = TikiLib::lib('menu')->replace_menu($menuId, $name, $description, $type, $icon, $use_items_icons, $parse);
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
		//check permissions
		$perms = Perms::get('menu');
		if (! $perms->tiki_p_edit_menu_options) {
			throw new Services_Exception_Denied(tr('Permission denied'));
		}

		$menuId = $input->menuId->int();

		//execute menu cloning
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && $input->confirm->int()) {
			TikiLib::lib('menu')->clone_menu($menuId, $input->name->text(), $input->description->text());
			return [];
		}

		//prepare basic data
		$info = TikiLib::lib('menu')->get_menu($menuId);
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

		$menuLib = TikiLib::lib('menu');
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

		//check permissions
		$perms = Perms::get('menu', $menuId);
		if (! $perms->tiki_p_edit_menu_options) {
			throw new Services_Exception_Denied(tr('Permission denied'));
		}

		$menuDetails = $this->get_menu_details($menuId);

		if (! $menuDetails) {
			throw new Services_Exception_NotFound(tr('Menu %0 notfound', $menuId));
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
			if (! Perms::get('menu', $menuId)->tiki_p_edit_menu_option) {
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
			$menuLib = TikiLib::lib('menu');
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

	function action_export_menu_options($input)
	{
		//check permissions
		$perms = Perms::get('menu');
		if (! $perms->tiki_p_edit_menu_options) {
			throw new Services_Exception_Denied(tr('Permission denied'));
		}

		//get basic input
		$menuId = $input->menuId->int();

		//get menu details
		$menuDetails = $this->get_menu_details($menuId);

		//perform menu export
		$confirm = $input->confirm->int();
		if ($confirm) {
			$menuId = $input->menuId->int();
			$encoding = $input->encoding->text();
			$menuLib = TikiLib::lib('menu');
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

	function action_import_menu_options($input)
	{
		//check permissions
		$perms = Perms::get('menu');
		if (! $perms->tiki_p_edit_menu_options) {
			throw new Services_Exception_Denied(tr('Permission denied'));
		}

		//get menu details
		$menuId = $input->menuId->int();
		$menuDetails = $this->get_menu_details($menuId);

		//execute import
		$confirm = $input->confirm->int();
		if ($confirm) {
			$menuId = $input->menuId->int();
			$menuLib = TikiLib::lib('menu');
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

	function action_preview_menu($input)
	{
		//check permissions
		$perms = Perms::get('menu');
		if (! $perms->tiki_p_edit_menu_option) {
			throw new Services_Exception_Denied(tr("You don't have permission to edit menu options (tiki_p_edit_menu_option)"));
		}

		//get menu details
		$menuId = $input->menuId->int();
		$menuDetails = $this->get_menu_details($menuId);

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

	private function get_menu_details($menuId)
	{
		//get menu information
		$menuLib = TikiLib::lib('menu');
		$menuInfo = $menuLib->get_menu($menuId);

		//get related symbol information
		$menuSymbol = Tiki_Profile::getObjectSymbolDetails('menu', $menuId);

		//return menu details
		return [
			'info' => $menuInfo,
			'symbol' => $menuSymbol,
		];
	}
}
