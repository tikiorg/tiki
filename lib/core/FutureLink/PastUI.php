<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// File name: PastUI.php
// Required path: /lib/core/FutureLink
//
// Programmer: Robert Plummer
//
// Purpose: Adds PastLink UI to page.  Makes it so that sentences wrapped in a PastLink are distinguished from the rest of the text in a page for the end user

Class FutureLink_PastUI extends Feed_Abstract
{
	public $type = 'futurelink';
	public $version = 0.1;
	public $isFileGal = false;
	public $debug = false;
	public $page = '';
	public $metadata = array();

	static $pairs;
	static $addedHashes;

	function __construct($page = "", $data = "")
	{
		$this->page = $page;

		if (!empty($page) && !empty($data)) {
			$this->metadata = FutureLink_MetadataAssembler::pagePastLink($page, $data);
		}

		return parent::__construct($page);
	}

	static function add($clipboarddata, $page, $data)
	{
		$me = new FutureLink_PastUI($page, $data);

		$item = new FutureLink_Pair( $me->metadata->raw, $clipboarddata );

		if (isset(self::$addedHashes[$item->pastlink->hash])) {
            return null;
        }

		self::$addedHashes[$item->pastlink->hash] = true;
		$item->futurelink->href = str_replace(' ', '+', $item->futurelink->href);

        Type::Pairs(FutureLink_PastUI::$pairs)->add($item);

		return FutureLink_PastUI::$pairs->length;
	}

	static function clearAll()
	{
        FutureLink_PastUI::$pairs = new FutureLink_Pairs();
	}

	public function getContents()
	{
		if (FutureLink_PastUI::$pairs->length > 0) {
			$this->setEncoding(TikiFilter_PrepareInput::delimiter('_')->toString(FutureLink_PastUI::$pairs));

			return FutureLink_PastUI::$pairs;
		}

		return array();
	}

	static function wikiView()
	{
		global $page;
		$headerlib = TikiLib::lib('header');
		$me = new self();
		$phrase = (!empty($_REQUEST['phrase']) ? $_REQUEST['phrase'] : '');
		FutureLink_Search::restorePastLinkPhrasesInWikiPage($me->getItems(), $phrase);

		//if we have an awaiting PastLink that needs sent, we do so here
		$result = (new Tracker_Query('Wiki Attributes'))
			->byName()
			->render(false)
			->filterFieldByValue('Page', $page)
			->filterFieldByValue('Type', 'PastLink Send')
			->query();

		if (count($result) > 0) {
			foreach (FutureLink_SendToFuture::sendAll() as $text => $received) {
				$receivedJSON = json_decode($received);
				if (isset($receivedJSON->feed) && $receivedJSON->feed == 'success') {
                    (new Tracker_Query('Wiki Attributes'))
						->byName()
						->render(false)
						->filterFieldByValue('Page', $page)
						->filterFieldByValue('Type', 'PastLink Send')
						->filterFieldByValue('Attribute', $text)
						->delete(true);

					$headerlib->add_jq_onready("$.notify('" . tr("PastLink and FutureLink created...") . "');");
				}
			}
		}
	}

	static function wikiSave()
	{
		global $page;
		//We add these to a stack that needs to be sent, rather than just sending all with the view event
		$me = new self();

		foreach ($me->getItems() as $item) {
            (new Tracker_Query('Wiki Attributes'))
				->byName()
				->replaceItem(
					array(
						'Page' => $page,
						'Attribute' => $item->pastlink->text,
						'Value' => 'true',
						'Type' => 'PastLink Send'
					)
				);
		}
	}
}

//define pairs
FutureLink_PastUI::$pairs = new FutureLink_Pairs();
