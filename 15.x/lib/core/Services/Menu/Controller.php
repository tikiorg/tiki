<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Controller.php 47279 2013-08-26 14:48:36Z changi67 $

class Services_Menu_Controller
{

	function action_get_menu($input)
	{
		$menuId = $input->menuId->int();
		return TikiLib::lib('menu')->get_menu($menuId);
	}

	function action_manage_menu ($input)
	{
		//check permissions
		$perms = Perms::get('menu');
		if (! $perms->tiki_p_edit_menu) {
			throw new Services_Exception_Denied(tr("You don't have permission to edit menus (tiki_p_edit_menu)"));
		}
		
		//get menu details
		$menuId = $input->menuId->int();
		$info = TikiLib::lib('menu')->get_menu($menuId);
		$symbol = Tiki_Profile::getObjectSymbolDetails('menu', $menuId);
			
		//execute menu insert/update
		$confirm = $input->confirm->int();
		if ($confirm) {
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
		return array(
			'title' => $info['menuId'] ? tr('Edit Menu') : tr('Create Menu'),
			'info' => $info,
			'symbol' => $symbol,
		);
	}
	
	function action_clone_menu ($input)
	{
		//check permissions
		$perms = Perms::get('menu');
		if (! $perms->tiki_p_edit_menu_options) {
			throw new Services_Exception_Denied(tr('Permission denied'));
		}
		
		//prepare basic data
		$menuId = $input->menuId->int();
		$info = TikiLib::lib('menu')->get_menu($menuId);
		$symbol = Tiki_Profile::getObjectSymbolDetails('menu', $menuId);
	
		//execute menu cloning
		$confirm = $input->confirm->int();
		if ($confirm) {
			TikiLib::lib('menu')->clone_menu($menuId);
		}
		
		//information for the clone menu screen
		return array(
			'title' => tr('Clone this menu and its options'),
			'info' => $info,
			'symbol' => $symbol,
		);
	}
	
	function action_list_menu_options ($input) //TODO finish, not used yet
	{
		//check permissions
		$perms = Perms::get('menu');
		if (! $perms->tiki_p_edit_menu_options) {
			throw new Services_Exception_Denied(tr('Permission denied'));
		}
		
		//prepare basic data
		$menuId = $input->menuId->int();
		$menuLib = TikiLib::lib('menu');
		$data = $menuLib->list_menu_options($menuId);
		$channels = $menuLib->describe_menu_types($data);
			
		//information for the list menu options screen
		return array(
			'menuId' => $menuId,
			'channels' => $channels["data"],
			'optionCount' => $channels["cant"],
		);
	}
	
	function action_manage_menu_option ($input) //TODO finish, not used yet
	{
		//check permissions
		$perms = Perms::get('menu');
		if (! $perms->tiki_p_edit_menu_options) {
			throw new Services_Exception_Denied(tr('Permission denied'));
		}
		
		//prepare basic data
		$optionId = $input->optionId->int();
		$menuLib = TikiLib::lib('menu');
		$optionInfo = $menuLib->get_menu_option($optionId);
		
		//get menu information
		if ($optionId) {
			$menuId = $menuLib->get_menuId_from_optionId($optionId);
		}
		else {
			$menuId = $input->menuId->int();
		}	
		$menuDetails = $this->get_menu_details($menuId);
		
		//get usergroup information
		if (isset($optionInfo['groupname']) && !is_array($optionInfo['groupname'])) $optionInfo['groupname'] = explode(',', $optionInfo['groupname']);
		$userLib = TikiLib::lib('user');
		$all_groups = $userLib->list_all_groups();
		if (is_array($all_groups)) foreach ($all_groups as $g) $option_groups[$g] = (is_array($optionInfo['groupname']) && in_array($g, $optionInfo['groupname'])) ? 'selected="selected"' : '';
		
		//get preference information
		$headerlib = TikiLib::lib('header');
		$feature_prefs = array();
		global $prefs;
		foreach ($prefs as $k => $v) {	// attempt to filter out non-feature prefs (still finds 133!)
			if (strpos($k, 'feature') !== false && preg_match_all('/_/m', $k, $m) === 1) {
				$feature_prefs[] = $k;
			}
		}
		$headerlib->add_js('var prefNames = ' . json_encode($feature_prefs) . ';');

		//get permission information		
		$headerlib->add_js('var permNames = ' . json_encode($userLib->get_permission_names_for('all')) . ';');		
		unset($userLib);
		
		//perform insert or update
		$confirm = $input->confirm->int();
		if ($confirm) {
			//check necessary permissions
			if (! $perms = Perms::get()->tiki_p_edit_menu_option) {
				throw new Services_Exception_Denied(tr("You don't have permission to edit menu options (tiki_p_edit_menu_option)"));
			}
		
			//prepare data and check conditions
			$menuId = $input->menuId->int();
			if (! $menuId) {
				throw new Services_Exception_MissingValue('menuId');
			}
			
			$optionId = $input->optionId->int();
			if (! $optionId) {
				throw new Services_Exception_MissingValue('optionId');
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
			$groupname = $input->groupname->array();
			$groupname = implode(',', $groupname);
			$level = $input->userlevel->text();
			$icon = $input->icon->text();
			
			//execute insert/update
			$menuLib = TikiLib::lib('menu');
			$menuLib->replace_menu_option($menuId, $optionId, $name, $url, $type, $position, $section, $perm, $groupname, $level, $icon);
			unset($menuLib);
			
			//post functions
				//$modlib->clear_cache();
			//fire event
		}
		
		//information for the menu option screen
		return array(
			'title' => $optionId ? tr('Menu Option %0', $optionInfo["optionId"]) : tr('Create Menu Option'),
			'optionId' => $optionId,
			'menuId' => $menuId,
			'menuInfo' => $menuDetails["info"],
			'menuSymbol' => $menuDetails["symbol"],
			'data' => $optionInfo,
			'name' => $optionInfo["name"],
			'url' => $optionInfo["url"],
			'type' => $optionInfo["type"],
			'position' => $optionInfo["position"],
			'section' => $optionInfo["section"],
			'perm' => $optionInfo["perm"],
			'option_groups' => $option_groups,
			'level' => $optionInfo["userlevel"],
			'icon' => $optionInfo["icon"],
			'perms' => $perms,
		);
	}
	
	function action_export_menu_options ($input)
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
			return array(
				'confirm' => $confirm,
			);
		}
		
		//information for the export menu screen
		return array(
			'title' => tr('Export Menu Options'),
			'menuId' => $menuId,
			'menuInfo' => $menuDetails["info"],
			'menuSymbol' => $menuDetails["symbol"],
		);
	}
	
	function action_import_menu_options ($input)
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
		return array(
			'title' => tr('Import Menu Options'),
			'menuId' => $menuId,
			'menuInfo' => $menuDetails["info"],
			'menuSymbol' => $menuDetails["symbol"],
		);
	}
	
	function action_preview_menu ($input)
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
		}
		else {
			$preview_type = 'vert';
			$preview_css = 'n';
			$preview_bootstrap = 'n';
		}

		//information for the preview menu screen
		return array(
			'title' => tr('Menu Preview'),
			'menuId' => $menuId,
			'menuInfo' => $menuDetails["info"],
			'menuSymbol' => $menuDetails["symbol"],
			'preview_type' => $preview_type,
			'preview_css' => $preview_css,
			'preview_bootstrap' => $preview_bootstrap,
		);
	}
		
	private function get_menu_details ($menuId)
	{
		//get menu information
		$menuLib = TikiLib::lib('menu');
		$menuInfo = $menuLib->get_menu($menuId);
		
		//get related symbol information
		$menuSymbol = Tiki_Profile::getObjectSymbolDetails('menu', $menuId);
		
		//return menu details
		return array(
			'info' => $menuInfo,
			'symbol' => $menuSymbol,
		);
	}
}
