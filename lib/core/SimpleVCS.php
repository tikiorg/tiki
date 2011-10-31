<?php
Class SimpleVCS
{
	var $file = array();
	var $fileName = "";
	
	static function fileName($fileName = "")
	{
		$me = new self();
		
		$me->fileName = "simplevcs_" . $fileName;
		$file = TikiLib::lib("filegal")->get_files(null, 1, null, $me->fileName, null);
		$me->file = $file['data'][0];
		return $me;	
	}
	
	private function createRevision($contents)
	{
		global $user;
		
		include_once ('lib/mime/mimetypes.php');
		
		return $id = TikiLib::lib("filegal")->insert_file(
			($this->file['galleryId'] || 0), //zero makes it not show by default
			$this->fileName,
			tr("An automatic htmlfeed from ") . $this->feedUrl,
			$this->fileName.".vcs",
			$contents,
			strlen($contents),
			$mimetypes["txt"],
			$user,
			date()
		);
	}
	
	function getData()
	{
		return TikiLib::lib("filegal")
			->get_file_info($file['id'])
			->fileInfo['data'];
	}
	
	function exists()
	{
		return !empty($this->file['id']);
	}
	
	function addRevision($contents)
	{
		global $user;
		include_once ('lib/mime/mimetypes.php');
		
		if (!$this->exists()) return $this->createRevision($contents);
		
		return TikiLib::lib("filegal")->save_archive(
			$this->file['id'],
			$this->file['galleryId'],
			0,
			$this->fileName,
			tr("An automatic htmlfeed from ") . $this->feedUrl,
			$this->fileName.".vcs",
			$contents,
			strlen($contents),
			$mimetypes["txt"],
			$user,
			date()
		);
	}
}
