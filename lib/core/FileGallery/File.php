<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class FileGallery_File
{
	var $param = array(
		'filename' => 'New File',
	);
	
	static function filename($filename = "")
	{
		$me = new self();
		
		$file = TikiLib::lib("filegal")->get_files(null, 1, null, $filename, null);
		$me->param = $file['data'][0];
		
		return $me;
	}
	
	static function id($id = 0)
	{
		$me = new self();
		
		$me->param = TikiLib::lib("filegal")->get_file($id);
		
		return $me;
	}
	
	function setParam($param = "", $value)
	{
		$this->param[$param] = $value;
		return $this;
	}
	
	function getParam($param = "")
	{
		return $this->param[$param];
	}
	
	function create($data)
	{
		global $user;
		
		include_once ('lib/mime/mimetypes.php');

		return TikiLib::lib("filegal")->insert_file(
			1, //zero makes it not show by default
			$this->param['filename'],
			$this->param['description'],
			$this->param['filename'],
			$data,
			strlen($data),
			$mimetypes["txt"],
			$user,
			date()
		);
	}
	
	function archive($archive = 0)
	{
		$archives = $this->listArchives();
		return FileGallery_File::id($archives[$archive]['id']);
	}
	
	function data()
	{
		$fileInfo = TikiLib::lib("filegal")->get_file_info($this->param['id']);
		return $fileInfo['data'];
	}
	
	function exists()
	{
		return (empty($this->param['id']) ? false : true);
	}
	
	function listArchives()
	{
		$archives = TikiLib::lib("filegal")->get_archives($this->param['id']);
		$archives = array_reverse( $archives['data'] );
		return $archives;
	}
	
	function replace($data)
	{
		global $user;
		include_once ('lib/mime/mimetypes.php');
		
		if (!$this->exists()) return $this->create($data);

		return TikiLib::lib("filegal")->save_archive(
			$this->param['id'],
			$this->param['galleryId'],
			0,
			$this->param['filename'],
			$this->param['description'],
			$this->param['filename'],
			$data,
			strlen($data),
			$mimetypes["txt"],
			$user,
			date()
		);
	}
	
	function diffLatestWithArchive($archive = 0)
	{
		include_once ( "lib/diff/Diff.php" );
		
		$textDiff =  new Text_Diff(
			FileGallery_File::filename($this->param['filename'])
				->archive($archive)
				->data(),
				
			$this->data()
		);
		
		return $textDiff->getDiff();
	}
}
