<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class FileGallery_File
{
	var $param = array();
	var $exists = false;
	
	static function filename($filename = "")
	{
		global $tikilib;
		
		$id = $tikilib->getOne("select fileId from tiki_files where name = ?", array($filename));
		if (!empty($id)) {
			return FileGallery_File::id($id);
		}
		
		//always use ->exists() to check if the file was found, if the above is returned, a file was found, if below, there wasent
		$me = new self();
		$me->setParam('filename', $filename);
		return $me;
	}
	
	static function id($id = 0)
	{
		$me = new self();
		
		$me->param = TikiLib::lib("filegal")->get_file((int)$id);
		
		if ($me->getParam('created') > 0) {
			$me->exists = true;
		}
		
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
	
	function archive($archive = 0)
	{
		$archives = $this->listArchives();
		return FileGallery_File::id($archives[$archive]['id']);
	}
	
	function archiveFromLastModif($lastModif)
	{
		foreach ($this->listArchives() as $archive) {
			if ($archive['lastModif'] == $lastModif) {
				return $archive;
			}
		}
	}
	
	function data()
	{	
		$fileInfo = TikiLib::lib("filegal")->get_file_info((int)$this->getParam('id'));	
		return $fileInfo['data'];
	}
	
	function exists()
	{
		return $this->exists;
	}
	
	function listArchives()
	{
		$archives = TikiLib::lib("filegal")->get_archives((int)$this->param['id']);
		$archives = array_reverse($archives['data']);
		return $archives;
	}
	
	function replace($data)
	{
		global $user;
		include_once ('lib/mime/mimetypes.php');
		if ($this->exists() == false) {
			$id = TikiLib::lib("filegal")->insert_file(
				1, //zero makes it not show by default
				$this->getParam('filename'),
				$this->getParam('description'),
				$this->getParam('filename'),
				$data,
				strlen($data),
				$mimetypes["txt"],
				$user,
				date()
			);
		} else {
			$id = TikiLib::lib("filegal")->save_archive(
				$this->getParam('id'),
				$this->getParam('galleryId'),
				0,
				$this->getParam('filename'),
				$this->getParam('description'),
				$this->getParam('filename'),
				$data,
				strlen($data),
				$mimetypes["txt"],
				$user,
				date()
			);
		}
		
		return $id;
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
