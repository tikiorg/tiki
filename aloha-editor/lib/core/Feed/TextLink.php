<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

Class Feed_TextLink extends Feed_Abstract
{
	public $type = 'textlink';
	public $version = 0.1;
	public $isFileGal = false;
	public $debug = false;
	public $page = '';
	public $metadata = array();

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

		return count(self::$contributions->entry);
	}

	static function clearAll()
	{
		self::$contributions = (object)array();
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
		global $page, $headerlib;
		$me = new self();
		$phrase = (!empty($_REQUEST['phrase']) ? $_REQUEST['phrase'] : '');
		Feed_ForwardLink_Search::restoreTextLinkPhrasesInWikiPage($me->getItems(), $phrase);

		//if we have an awaiting textlink that needs sent, we do so here
		$result = Tracker_Query::tracker('Wiki Attributes')
			->byName()
			->render(false)
			->filterFieldByValue('Page', $page)
			->filterFieldByValue('Type', 'TextLink Send')
			->query();

		if (count($result) > 0) {
			foreach(Feed_ForwardLink_Send::sendAll() as $text => $received) {
				$receivedJSON = json_decode($received);
				if (isset($receivedJSON->feed) && $receivedJSON->feed == 'success') {
					Tracker_Query::tracker('Wiki Attributes')
						->byName()
						->render(false)
						->filterFieldByValue('Page', $page)
						->filterFieldByValue('Type', 'TextLink Send')
						->filterFieldByValue('Attribute', $text)
						->delete(true);

					$headerlib->add_jq_onready("$.notify('" . tr("TextLink and ForwardLink created...") . "');");
				}
			}
		}
	}

	static function wikiSave()
	{
		global $page;
		//We add these to a stack that needs to be sent, rather than just sending all with the view event
		$me = new self();

		foreach($me->getItems() as $item) {
			Tracker_Query::tracker('Wiki Attributes')
				->byName()
				->replaceItem(array(
					'Page' => $page,
					'Attribute' => $item->textlink->text,
					'Value' => 'true',
					'Type' => 'TextLink Send'
				));
		}
	}
}
