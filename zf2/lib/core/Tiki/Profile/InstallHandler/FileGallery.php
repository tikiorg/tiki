<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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
			'parent' => -1,
			'visible' => 'n',		// fgal default is y so set here so it gets set only if specified in flags[]
		);

		$conversions = array(
			'owner' => 'user',
			'max_rows' => 'maxRows',
			'parent' => 'parentId',
		);

		$data = $this->obj->getData();

		$data = Tiki_Profile::convertLists($data, array('flags' => 'y'));

		$column = isset( $data['column'] ) ? $data['column'] : array();
		$popup = isset( $data['popup'] ) ? $data['popup'] : array();

		$both = array_intersect($column, $popup);
		$column = array_diff($column, $both);
		$popup = array_diff($popup, $both);

		foreach ( $both as $value )
			$data["show_$value"] = 'a';
		foreach ( $column as $value )
			$data["show_$value"] = 'y';
		foreach ( $popup as $value )
			$data["show_$value"] = 'o';

		unset( $data['popup'] );
		unset( $data['column'] );

		$data = array_merge($defaults, $data);

		foreach ( $conversions as $old => $new )
			if ( array_key_exists($old, $data) ) {
				$data[$new] = $data[$old];
				unset( $data[$old] );
			}

		unset( $data['galleryId'] );
		$this->replaceReferences($data);

		if (!empty($data['name'])) {
			global $filegallib; require_once 'lib/filegals/filegallib.php';
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
}
