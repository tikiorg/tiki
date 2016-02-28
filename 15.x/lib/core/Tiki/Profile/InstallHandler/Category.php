<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_InstallHandler_Category extends Tiki_Profile_InstallHandler
{
	private $name;
	private $description = '';
	private $parent = 0;
	private $migrateparent = 0;
	private $items = array();

	function fetchData()
	{
		if ( $this->name )
			return;

		$data = $this->obj->getData();

		if ( array_key_exists('name', $data) ) {
			$this->name = $data['name'];
		}
		if ( array_key_exists('description', $data) ) {
			$this->description = $data['description'];
		}
		if ( array_key_exists('parent', $data) ) {
			$this->parent = $data['parent'];
		}
		if ( array_key_exists('migrateparent', $data) ) {
			$this->migrateparent = $data['migrateparent'];
		}
		if ( array_key_exists('items', $data) && is_array($data['items']) ) {
			foreach ( $data['items'] as $pair ) {
				if ( is_array($pair) && count($pair) == 2 ) {
					$this->items[] = $pair;
				}
			}
		}
	}

	function canInstall()
	{
		$this->fetchData();

		if ( empty( $this->name ) ) {
			return false;
		}

		return true;
	}

	function _install()
	{
		global $tikilib;
		$this->fetchData();
		$this->replaceReferences($this->name);
		$this->replaceReferences($this->description);
		$this->replaceReferences($this->parent);
		$this->replaceReferences($this->migrateparent);
		$this->replaceReferences($this->items);
		
		$categlib = TikiLib::lib('categ');
		if ($id = $categlib->exist_child_category($this->parent, $this->name)) {
			$categlib->update_category($id, $this->name, $this->description, $this->parent);
		} else {
			$id = $categlib->add_category($this->parent, $this->name, $this->description);
		}

		if ($this->migrateparent && $from = $categlib->exist_child_category($this->migrateparent, $this->name)) {
			$categlib->move_all_objects($from, $id);
		}

		foreach ( $this->items as $item ) {
			list( $type, $object ) = $item;

			$type = Tiki_Profile_Installer::convertType($type);
			$object = Tiki_Profile_Installer::convertObject($type, $object);
			$categlib->categorize_any($type, $object, $id);
		}

		return $id;
	}

	public static function export(Tiki_Profile_Writer $writer, $categId, $deep, $includeObjectCallback)
	{
		$categlib = TikiLib::lib('categ');
		if (! $info = $categlib->get_category($categId)) {
			return false;
		}

		$items = array();
		foreach ($categlib->get_category_objects($categId) as $row) {
			if ($includeObjectCallback($row['type'], $row['itemId'])) {
				$items[] = array($row['type'], $writer->getReference($row['type'], $row['itemId']));
			}
		}

		$data = array(
			'name' => $info['name'],
		);

		if (! empty($info['parentId'])) {
			$data['parent'] = $writer->getReference('category', $info['parentId']);
		}

		if (! empty($items)) {
			$data['items'] = $items;
		}

		$writer->addObject('category', $categId, $data);

		if ($deep) {
			$descendants = $categlib->get_category_descendants($categId);
			array_shift($descendants);
			foreach ($descendants as $children) {
				self::export($writer, $children, $deep, $includeObjectCallback);
			}
		}
		
		return true;
	}
}
