<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Controller.php 47279 2013-08-26 14:48:36Z changi67 $

class Services_Menu_Controller
{

	function action_get_menu($input)
	{
		$menuId = $input->menuId->int();
		$menuLib = TikiLib::lib('menu');
		return array(
			'title' => $res,
		);
	}

	function action_manage ($input)
	{
		$menuId = $input->menuId->int();
		$info = TikiLib::lib('menu')->get_menu($menuId);
		$symbol = Tiki_Profile::getObjectSymbolDetails('menu', $menuId);
		$confirm = $input->confirm->int();
				
		$perms = Perms::get('menu');
		if (! $perms->tiki_p_edit_menu) {
			throw new Services_Exception_Denied(tr('Permission denied (tiki_p_edit_menu)'));
		}
		
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
		
		return array(
			'title' => $info['menuId'] ? tr('Edit Menu') : tr('Create Menu'),
			'info' => $info,
			'symbol' => $symbol,
		);
	}
	
	function action_clone ($input)
	{
		$menuId = $input->menuId->int();
		$info = TikiLib::lib('menu')->get_menu($menuId);

		$symbol = Tiki_Profile::getObjectSymbolDetails('menu', $menuId);
		$confirm = $input->confirm->int();
		
		if ($confirm) {
			TikiLib::lib('menu')->clone_menu($menuId);
		}
		
		return array(
			'title' => tr('Clone this menu and its options'),
			'info' => $info,
			'symbol' => $symbol,
		);
	}
}

