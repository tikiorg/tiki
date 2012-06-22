<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

Class Feed_TextLink extends Feed_Abstract
{
	var $type = 'textlink';
	var $version = 0.1;
	var $isFileGal = false;
	var $debug = false;
	var $page = '';
	var $metadata = array();

	static $contributions;
	static $addedHashes;

	function __construct($page = "", $data = "")
	{
		$this->page = $page;

		if (!empty($page) && !empty($data)) {
			$this->metadata = Feed_ForwardLink_Metadata::pageTextLink($page, $data);
		}

		return parent::__construct($page);
	}

	static function add($clipboarddata, $page, $data)
	{
		$me = new self($page, $data);

		$item = (object)array(
			"forwardlink"=> $clipboarddata,
			"textlink"=> (object)$me->metadata->raw
		);

		if (empty(self::$contributions)) {
			self::$contributions = (object)array();
			self::$contributions->entry = array();
		}

		$item = (object)$item;
		if (isset(self::$addedHashes[$item->textlink->hash])) return;
		self::$addedHashes[$item->textlink->hash] = true;
		$item->forwardlink->href = str_replace(' ', '+', $item->forwardlink->href);

		self::$contributions->entry[] = $item;
	}

	public function getContents()
	{
		if (!empty(self::$contributions)) {
			$this->setEncoding(TikiFilter_PrepareInput::delimiter('_')->toString(self::$contributions));

			return self::$contributions;
		}

		return array();
	}

	static function wikiView()
	{
		$me = new self();
		$phrase = (!empty($_REQUEST['phrase']) ? $_REQUEST['phrase'] : '');
		Feed_ForwardLink_Search::restoreTextLinkPhrasesInWikiPage($me->getItems(), $phrase);
	}
}