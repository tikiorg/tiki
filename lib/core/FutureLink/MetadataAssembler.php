<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// File name: Metadata.php
// Required path: /lib/core/FutureLink
//
// Programmer: Robert Plummer
//
// Purpose: Generates list of pages which have FutureLink connections to a given page.

class FutureLink_MetadataAssembler
{
	public $page;
	public $lang;
	public $lastModif;
	public $href;
	public $websiteTitle;
	public $moderatorData;
	public $authorData;
	public $raw;
	public $minimumStatisticsNeeded;
	public $minimumMathNeeded;
	public $scientificField;
	public $categories = array();
	public $keywords;
	public $questions;
	public $datePageOriginated;
	public $countAll;
	public $language = '';
    public $findDatePageOriginated;

	static $acceptableKeys = array(
		'websiteTitle' =>               true,
		'websiteSubtitle' =>            true,
		'moderator' =>                  true,
		'moderatorInstitution' =>       true,
		'moderatorProfession' =>        true,
		'hash' =>                       true,
		'author' =>                     true,
		'authorInstitution' =>          true,
		'authorProfession' =>           true,
		"href" =>                       true,
		'answers' =>                    true,
		'dateLastUpdated' =>            true,
		'dateOriginated' =>             true,
		'language' =>                   true,
		'count' =>                      true,
		'keywords' =>                   true,
		'categories' =>                 true,
		'scientificField' =>            true,
		'minimumMathNeeded' =>          true,
		'minimumStatisticsNeeded' =>    true,
		'text' =>                       true
	);

	function __construct($page)
	{
		global $tikilib, $prefs;

		$this->page = $page;

        //TODO: abstract
		$details = $tikilib->fetchAll("SELECT lang, lastModif FROM tiki_pages WHERE pageName = ?", $page);
		$detail = end($details);

		$this->lang = (empty($detail['lang']) ? $prefs['site_language'] : $detail['lang']);
		$this->lastModif = $detail['lastModif'];
		$this->websiteTitle = $prefs['browsertitle'];
		$this->href = TikiLib::tikiUrl() . 'tiki-index.php?page=' . $page;
	}

    static function fromRawToMetaData($raw)
    {
        $me = new FutureLink_Metadata();
        $me->websiteTitle =             $raw->websiteTitle;
        $me->websiteSubtitle =          $raw->websiteSubtitle;
        $me->moderator =                $raw->moderator;
        $me->moderatorInstitution=      $raw->moderatorInstitution;
        $me->moderatorProfession=       $raw->moderatorProfession;
        $me->hash=                      $raw->hash;
        $me->author=                    $raw->author;
        $me->dateLastUpdated=           $raw->dateLastUpdated;
        $me->authorProfession=          $raw->authorProfession;
        $me->href= 	                    $raw->href;
        $me->answers=                   $raw->answers;
        $me->dateLastUpdated=           $raw->dateLastUpdated;
        $me->dateOriginated=            $raw->dateOriginated;
        $me->language=                  $raw->language;
        $me->count=                     $raw->countAll;
        $me->keywords=                  $raw->keywords;
        $me->categories=                $raw->categories;
        $me->scientificField=           $raw->scientificField;
        $me->minimumMathNeeded=         $raw->minimumMathNeeded;
        $me->minimumStatisticsNeeded=   $raw->minimumStatisticsNeeded;
        $me->text= 	                    $raw->text;

        return $me;
    }

	static function pagePastLink($page, $data)
	{
		$me = new FutureLink_MetadataAssembler($page);

        $me->raw = new FutureLink_Metadata();
		$me->raw->websiteTitle =            $me->websiteTitle;
        $me->raw->websiteSubtitle =         $me->page;
		$me->raw->moderator =               $me->moderatorName();
		$me->raw->moderatorInstitution=     $me->moderatorBusinessName();
	    $me->raw->moderatorProfession=      $me->moderatorProfession();
		$me->raw->hash=                     hash_hmac("md5", JisonParser_Phraser_Handler::superSanitize(
                                                    $me->raw->author .
                                                    $me->raw->authorInstitution .
                                                    $me->raw->authorProfession
                                                ),
                                                JisonParser_Phraser_Handler::superSanitize($data)
                                            );
	    $me->raw->author=                   $me->authorName();
	    $me->raw->authorInstitution=        $me->authorBusinessName();
	    $me->raw->authorProfession=         $me->authorProfession();
	    $me->raw->href= 	                $me->href;
	    $me->raw->answers=                  $me->answers();
		$me->raw->dateLastUpdated=          $me->lastModif;
		$me->raw->dateOriginated=           $me->findDatePageOriginated();
		$me->raw->language=                 $me->language();
		$me->raw->count=                    $me->countAll();
		$me->raw->keywords=                 $me->keywords();
		$me->raw->categories=               $me->categories();
		$me->raw->scientificField=          $me->scientificField();
		$me->raw->minimumMathNeeded=        $me->minimumMathNeeded();
		$me->raw->minimumStatisticsNeeded=  $me->minimumStatisticsNeeded();
		$me->raw->text= 	                $data;

		return $me;
	}

	static function pageFutureLink($page)
	{
		$me = new FutureLink_MetadataAssembler($page);

        $me->raw = new FutureLink_Metadata();
        $me->raw->websiteTitle =            $me->websiteTitle;
        $me->raw->websiteSubtitle =         $me->page;
        $me->raw->moderator =               $me->moderatorName();
        $me->raw->moderatorInstitution=     $me->moderatorBusinessName();
        $me->raw->moderatorProfession=      $me->moderatorProfession();
        $me->raw->hash=                     ''; //hash isn't yet known
        $me->raw->author=                   $me->authorName();
        $me->raw->authorInstitution=        $me->authorBusinessName();
        $me->raw->authorProfession=         $me->authorProfession();
        $me->raw->href= 	                $me->href;
        $me->raw->answers=                  $me->answers();
        $me->raw->dateLastUpdated=          $me->lastModif;
        $me->raw->dateOriginated=           $me->findDatePageOriginated();
        $me->raw->language=                 $me->language();
        $me->raw->count=                    $me->countAll();
        $me->raw->keywords=                 $me->keywords();
        $me->raw->categories=               $me->categories();
        $me->raw->scientificField=          $me->scientificField();
        $me->raw->minimumMathNeeded=        $me->minimumMathNeeded();
        $me->raw->minimumStatisticsNeeded=  $me->minimumStatisticsNeeded();
        $me->raw->text=                     ''; //text isn't yet known

		return $me;
	}

	public function answers()
	{
		$answers = array();
		foreach ($this->questions() as $question) {
			$answers[] = array(
				'question'=> strip_tags($question['Value']),
				'answer'=> '',
			);
		}

		return $answers;
	}

	public function questions()
	{
		if (empty($this->questions)) {
			$this->questions = (new Tracker_Query('Wiki Attributes'))
				->byName()
				->filterFieldByValue('Type', 'Question')
				->filterFieldByValue('Page', $this->page)
				->render(false)
				->query();
		}

		return $this->questions;
	}

	private function endValue($item, $implodeOn = '')
	{
		if (isset($item) && is_array($item)) {
			$item = end($item);
			$item = $item['Value'];
		}

		if (!empty($implodeOn)) {
			$item = implode(JisonParser_Phraser_Handler::sanitizeToWords($item), ',');
		}

		return $item;
	}

	public function keywords($out = true)
	{
		if (empty($this->keywords)) {
			$this->keywords = (new Tracker_Query('Wiki Attributes'))
				->byName()
				->filterFieldByValue('Type', 'Keywords')
				->filterFieldByValue('Page', $this->page)
				->render(false)
				->query();
		}

		if ($out == true) {
			return $this->endValue($this->keywords, true);
		}

		return $this->keywords;
	}

	public function author($version = -1)
	{
		global $tikilib;

        //TODO: abstract
		if (empty($this->authorData)) {
			if ($version < 0) {
				$user = TikiLib::lib('trk')->getOne("SELECT user FROM tiki_pages WHERE pageName = ?", array($this->page));
			} else {
				$user = TikiLib::lib('trk')->getOne("SELECT user FROM tiki_history WHERE pageName = ? AND version = ?", array($this->page, $version));
			}

			if (empty($user))  return array();

			$authorData = (new Tracker_Query("Users"))
				->byName()
				->filterFieldByValue('login', $user)
				->render(false)
				->getOne();
			$authorData = end($authorData);

			if (empty($authorData['Name'])) {
				$authorData['Name'] = $tikilib->get_user_preference($user, "realName");
			}

			$this->authorData = $authorData;
		}

		return $this->authorData;
	}

	public function authorName()
	{
		$author = $this->author();
		return (!empty($author['Name']) ? $author['Name'] : '');
	}

	public function authorBusinessName()
	{
		$author = $this->author();
		return (!empty($author['Business Name']) ? $author['Business Name'] : '');
	}

	public function authorProfession()
	{
		$author = $this->author();
		return (!empty($author['Profession']) ? $author['Profession'] : '');
	}

	public function moderator()
	{
		global $tikilib;

		if (empty($this->moderatorData)) {
            //TODO: abstract
			$moderatorData = (new Tracker_Query("Users"))
				->byName()
				->filterFieldByValue('login', 'admin') //admin is un-deletable
				->render(false)
				->getOne();
			$moderatorData = end($moderatorData);

			if (empty($authorData['Name'])) {
				$moderatorData['Name'] = $tikilib->get_user_preference('admin', "realName");
			}

			$this->moderatorData = $moderatorData;
		}

		return $this->moderatorData;
	}

	public function moderatorName()
	{
		$moderator = $this->moderator();
		return (!empty($moderator['Name']) ? $moderator['Name'] : '');
	}

	public function moderatorBusinessName()
	{
		$moderator = $this->moderator();
		return (!empty($moderator['Business Name']) ? $moderator['Business Name'] : '');
	}

	public function moderatorProfession()
	{
		$moderator = $this->moderator();
		return (!empty($moderator['Profession']) ? $moderator['Profession'] : '');
	}

	public function findDatePageOriginated()
	{
		if (empty($this->findDatePageOriginated)) {
			$this->findDatePageOriginated = TikiLib::lib('trk')->getOne('SELECT lastModif FROM tiki_history WHERE pageName = ? ORDER BY lastModif DESC', array($this->page));

			if (empty($date)) {
				//page doesn't yet have history
				$this->findDatePageOriginated = TikiLib::lib('trk')->getOne('SELECT lastModif FROM tiki_pages WHERE pageName = ?', array($this->page));
			}
		}

		return $this->findDatePageOriginated;
	}

	public function countAll()
	{
		if (empty($this->countAll)) {
			$this->countAll = count(
                (new Tracker_Query('Wiki Attributes'))
					->byName()
					->filterFieldByValue('Type', 'FutureLink Accepted')
					->render(false)
					->query()
			);
		}

        return $this->countAll;
	}

	public function categories()
	{
		if (empty($this->categories)) {
			foreach (TikiLib::lib('categ')->get_object_categories('wiki page', $this->page) as $categoryId) {
				$this->categories[] = TikiLib::lib('categ')->get_category_name($categoryId);
			}
		}

		return $this->categories;
	}

	public function scientificField($out = true)
	{
		if (empty($this->scientificField)) {
            //TODO: abstract
			$this->scientificField = (new Tracker_Query('Wiki Attributes'))
				->byName()
				->filterFieldByValue('Type', 'Scientific Field')
				->filterFieldByValue('Page', $this->page)
				->render(false)
				->query();
		}

		if ($out == true) {
			return $this->endValue($this->scientificField, true);
		}

		return $this->scientificField;
	}

	public function minimumMathNeeded($out = true)
	{
		if (empty($this->minimumMathNeeded)) {
            //TODO: abstract
			$this->minimumMathNeeded = (new Tracker_Query('Wiki Attributes'))
				->byName()
				->filterFieldByValue('Type', 'Minimum Math Needed')
				->filterFieldByValue('Page', $this->page)
				->render(false)
				->query();
		}

		if ($out == true) {
			return $this->endValue($this->minimumMathNeeded, true);
		}

		return $this->minimumMathNeeded;
	}

	public function minimumStatisticsNeeded($out = true)
	{
		if (empty($this->minimumStatisticsNeeded)) {
            //TODO: abstract
			$this->minimumStatisticsNeeded = (new Tracker_Query('Wiki Attributes'))
				->byName()
				->filterFieldByValue('Type', 'Minimum Statistics Needed')
				->filterFieldByValue('Page', $this->page)
				->render(false)
				->query();
		}

		if ($out == true) {
			return $this->endValue($this->minimumStatisticsNeeded, true);
		}

		return $this->minimumStatisticsNeeded;
	}

	public function language()
	{
		if (empty($this->language)) {
            //TODO: abstract
			$langLib = TikiLib::lib('language');
			$languages = $langLib->list_languages();
			foreach ($languages as $listLanguage) {
				if ($listLanguage['value'] == $this->lang) {
					$this->language = urlencode($listLanguage['name']);
					break;
				}
			}
		}

		return $this->language;
	}
}
