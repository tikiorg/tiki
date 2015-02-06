<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\FileGallery;

use TikiLib;

class File
{
	public $param = array(
		"fileId" 	=> 0,
		"galleryId" 	=> 1,
		"name"		=> "",
		"description"	=> "",
		"created" 	=> 0,
		"filename" 	=> "",
		"filesize" 	=> 0,
		"filetype" 	=> "",
		"data" 		=> "",
		"user"	 	=> "",
		"author" 	=> "",
		"hits" 		=> 0,
		"maxhits"	=> 0,
		"lastDownload" 	=> "",
		"votes" 	=> 0,
		"points" 	=> 0,
		"path" 		=> "",
		"reference_url" => "",
		"is_reference" 	=> false,
		"hash" 		=> "",
		"search_data" 	=> "",
		"lastModif" 	=> 0,
		"lastModifUser" => "",
		"lockedby" 	=> "",
		"comment"	=> "",
		"archiveId"	=> 0,
		"deleteAfter" 	=> 0,
		"backlinkPerms"	=> "",
	);
	public $exists = false;

	function __construct()
	{
		global $mimetypes; include_once ('lib/mime/mimetypes.php');

		$this->setParam('filetype', $mimetypes["txt"]);
		$this->setParam('name', tr("New File"));
		$this->setParam('description', tr("New File"));
		$this->setParam('filename', tr("New File"));
	}

	static function filename($filename = "")
	{
		$tikilib = TikiLib::lib('tiki');

		$id = $tikilib->getOne("SELECT fileId FROM tiki_files WHERE filename = ? AND archiveId  < 1", array($filename));

		if (!empty($id)) {
			return self::id($id);
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
		return self::id($archives[$archive]['id']);
	}

	function archiveFromLastModif ($lastModif)
	{
		foreach ($this->listArchives() as $archive) {
			if ($archive['lastModif'] == $lastModif) {
				return $archive;
			}
		}
	}

	function data()
	{
		return $this->getParam('data');
	}

	function exists()
	{
		return $this->exists;
	}

	function listArchives()
	{
		$archives = TikiLib::lib("filegal")->get_archives((int)$this->getParam('fileId'));
		$archives = \array_reverse($archives['data']);
		return $archives;
	}

	function replace($data)
	{
		global $user;

		$user = (!empty($user) ? $user : 'Anonymous');

		if ($this->exists() == false) {
			$id = TikiLib::lib("filegal")->insert_file(
				($this->getParam('galleryId') || 1), //zero makes it not show by default
				$this->getParam('filename'),
				$this->getParam('description'),
				$this->getParam('filename'),
				$data,
				strlen($data),
				$this->getParam('filetype'),
				$user
			);
		} else {
			$id = TikiLib::lib("filegal")->save_archive(
				$this->getParam('fileId'),
				$this->getParam('galleryId'),
				0,
				$this->getParam('filename'),
				$this->getParam('description'),
				$this->getParam('filename'),
				$data,
				strlen($data),
				$this->getParam('filetype'),
				$user
			);
		}

		return $id;
	}

	function delete()
	{
		TikiLib::lib("filegal")->remove_file($this->param);
	}

	function diffLatestWithArchive($archive = 0)
	{
		include_once ( "lib/diff/Diff.php" );

		$textDiff =  new \Text_Diff(
			self::id($this->getParam('fileId'))
			->archive($archive)
			->data(),
			$this->data()
		);

		return $textDiff->getDiff();
	}
}
