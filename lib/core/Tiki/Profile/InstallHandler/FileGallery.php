<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_InstallHandler_FileGallery extends Tiki_Profile_InstallHandler
{
	function getData()
	{
		if ( $this->data )
			return $this->data;

		$defaults = array(
			'owner' => 'admin',
			'public' => 'n',
			'galleryId' => null,
			'parent' => 1,
			'visible' => 'n',		// fgal default is y so set here so it gets set only if specified in flags[]
		);

		$conversions = array(
			'owner' => 'user',
			'max_rows' => 'maxRows',
			'parent' => 'parentId',
		);

		$columns = array(
			'id',
			'icon',
			'name',
			'size',
			'description',
			'created',
			'hits',
			'lastDownload',
			'lockedby',
			'modified',
			'author',
			'last_user',
			'comment',
			'files',
			'backlinks',
			'deleteAfter',
			'checked',
			'share',
			'source',
			'explorer',		// not really a column, but follows the same pattern
			'path',			// also
			'slideshow',	// also
		);

		$data = $this->obj->getData();

		$data = Tiki_Profile::convertLists($data, array('flags' => 'y'));

		$column = isset( $data['column'] ) ? $data['column'] : array();
		$popup = isset( $data['popup'] ) ? $data['popup'] : array();

		if (in_array('name', $column) && in_array('filename', $column)) {
			$data['show_name'] = 'a';
			unset($column[array_search('name', $column)], $column[array_search('filename', $column)]);
			unset($columns[array_search('name', $columns)]);
		} else if (in_array('name', $column)) {
			$data['show_name'] = 'n';
			unset($column[array_search('name', $column)]);
			unset($columns[array_search('name', $columns)]);
		} else if (in_array('filename', $column)) {
			$data['show_name'] = 'f';
			unset($column[array_search('filename', $column)]);
			unset($columns[array_search('name', $columns)]);
		}
		$both = array_intersect($column, $popup);
		if ($column || $popup) {
			$hide = array_diff($columns, array_merge($column, $popup));
		} else {
			$hide = array();			// use defaults if nothing set
		}

		$column = array_diff($column, $both);
		$popup = array_diff($popup, $both);

		foreach ( $both as $value )
			$data["show_$value"] = 'a';
		foreach ( $column as $value )
			$data["show_$value"] = 'y';
		foreach ( $popup as $value )
			$data["show_$value"] = 'o';
		foreach ( $hide as $value )
			$data["show_$value"] = 'n';

		unset( $data['popup'] );
		unset( $data['column'] );

		$data = array_merge($defaults, $data);

		foreach ( $conversions as $old => $new ) {
			if ( array_key_exists($old, $data) ) {
				$data[$new] = $data[$old];
				unset( $data[$old] );
			}
		}

		unset( $data['galleryId'] );
		$this->replaceReferences($data);

		if (!empty($data['name'])) {
			$filegallib = TikiLib::lib('filegal');
			$data['galleryId'] = $filegallib->getGalleryId($data['name'], $data['parentId']);
		}
		return $this->data = $data;
	}

	function canInstall()
	{
		$data = $this->getData();
		if ( ! isset( $data['name'] ) )
			return false;
		return $this->convertMode($data);
	}

	private function convertMode($data)
	{
		if (!isset($data['mode'])) {
			return true; // will duplicate if already exists
		}
		switch ($data['mode']) {
		case 'update':
			if (empty($data['galleryId'])) {
				throw new Exception(tra('File gallery does not exist').' '.$data['name']);
			}
		case 'create':
			if (!empty($data['galleryId'])) {
				throw new Exception(tra('File gallery already exists').' '.$data['name']);
			}
		}
		return true;
	}

	function _install()
	{
		$filegallib = TikiLib::lib('filegal');

		$input = $this->getData();

		$files = array();
		if (! empty($input['init_files'])) {
			$files = (array) $input['init_files'];
			unset($input['init_files']);
		}

		$galleryId = $filegallib->replace_file_gallery($input);

		if (empty($input['galleryId']) && count($files)) {
			$gal_info = $filegallib->get_file_gallery_info($galleryId);

			foreach ($files as $url) {
				$this->upload($gal_info, $url);
			}
		}

		return $galleryId;
	}

	function upload($gal_info, $url)
	{
		$filegallib = TikiLib::lib('filegal');
		if ($filegallib->lookup_source($url)) {
			return;
		}
		
		$info = $filegallib->get_info_from_url($url);

		if (! $info) {
			return;
		}

		$fileId = $filegallib->upload_single_file($gal_info, $info['name'], $info['size'], $info['type'], $info['data']);

		if ($fileId === false) {
			return;
		}

		$filegallib->attach_file_source($fileId, $url, $info);
	}

	public static function export(Tiki_Profile_Writer $writer, $galId, $withParents = false, $deep = false)
	{
		$filegallib = TikiLib::lib('filegal');
		$info = $filegallib->get_file_gallery_info($galId);
		$default = $filegallib->default_file_gallery();

		if (! $info) {
			return false;
		}

		$out = array(
			'name' => $info['name'],
			'visible' => $info['visible'],
		);

		if ($info['parentId'] > 3) { // up to 3, standard/default galleries
			$out['parent'] = $writer->getReference('file_gallery', $info['parentId']);
		} else {
			$out['parent'] = $info['parentId'];
		}

		// Include any simple field whose value is different from the default
		$simple = array('description', 'public', 'type', 'lockable', 'archives', 'quota', 'image_max_size_x', 'image_max_size_y', 'backlinkPerms', 'wiki_syntax', 'sort_mode', 'maxRows', 'max_desc', 'subgal_conf', 'default_view', 'template');
		foreach ($simple as $field) {
			if ($info[$field] != $default[$field]) {
				$out[$field] = $info[$field];
			}
		}

		$popup = array();
		$column = array();
		foreach ($info as $field => $value) {
			if (isset($default[$field]) && $value == $default[$field]) {
				continue; // Skip default values
			}

			if (substr($field, 0, 5) == 'show_') {
				$short = substr($field, 5);
				if ($value == 'a' || $value == 'o') {
					$popup[] = $short;
				}
				if ($value == 'a' || $value == 'y') {
					$column[] = $short;
				}
			}
		}

		if (! empty($popup)) {
			$out['popup'] = $popup;
		}

		if (! empty($column)) {
			$out['column'] = $column;
		}

		$writer->addObject('file_gallery', $galId, $out);

		if ($deep) {
			$table = $filegallib->table('tiki_file_galleries');
			$children = $table->fetchColumn('galleryId', array(
				'parentId' => $galId,
			));

			foreach ($children as $id) {
				self::export($writer, $id, false, $deep);
			}
		}

		if ($withParents && $info['parentId'] > 3) {
			self::export($writer, $info['parentId'], $withParents, false);
		}


		return true;
	}
}
