<?php
Class SimpleVCS
{
	var $file = array();
	var $fileName = "";
	var $revision = 0;
	
	static function fileName($fileName = "")
	{
		$me = new self();
		
		$me->fileName = "simplevcs_" . $fileName;
		$file = TikiLib::lib("filegal")->get_files(null, 1, null, $me->fileName, null);
		$me->file = $file['data'][0];
		$me->revision = $revision;
		
		return $me;
	}
	
	private function createRevision($contents)
	{
		global $user;
		
		include_once ('lib/mime/mimetypes.php');

		return TikiLib::lib("filegal")->insert_file(
			0, //zero makes it not show by default
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
	
	function revision($revision = 0)
	{
		$this->revision = $revision;
		$revisions = $this->listRevisions();
		$this->file = $revisions[$revision];
		return $this;
	}
	
	function getData()
	{
		$fileInfo = TikiLib::lib("filegal")->get_file_info($this->file['id']);
		return $fileInfo['data'];
	}
	
	function exists()
	{
		return (empty($this->file['id']) ? false : true);
	}
	
	function listRevisions()
	{
		$archives = TikiLib::lib("filegal")->get_archives($this->file['id']);
		$archives = array_reverse( $archives['data'] );
		return $archives;
	}
	
	function addRevision($contents)
	{
		global $user;
		include_once ('lib/mime/mimetypes.php');
		
		if (!$this->exists()) return $this->createRevision($contents);

		return TikiLib::lib("filegal")->save_archive(
			$this->file['id'],
			0,
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
	
	function diffLatestWithRevision($revision = 0)
	{
		include_once ( "lib/diff/Diff.php" );
		
		$textDiff =  new Text_Diff(
			SimpleVCS::fileName($this->fileName)
				->revision($revision)
				->getData(),
			$this->getData()
		);
		
		return $textDiff->getDiff();
	}
}
