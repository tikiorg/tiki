<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$


class Feed_ForwardLink_Metadata
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

		$details = $tikilib->fetchAll("SELECT lang, lastModif FROM tiki_pages WHERE pageName = ?", $page);
		$detail = end($details);

		$this->lang = (empty($detail['lang']) ? $prefs['site_language'] : $detail['lang']);
		$this->lastModif = $detail['lastModif'];
		$this->websiteTitle = $prefs['browsertitle'];
		$this->href = TikiLib::tikiUrl() . 'tiki-index.php?page=' . $page;
	}

	static function pageTextLink($page, $data)
	{
		$me = new self($page);

		$me->raw = array(
			'websiteTitle'=>            $me->websiteTitle,
			'websiteSubtitle'=>         $me->page,
			'moderator'=>               $me->moderatorName(),
			'moderatorInstitution'=>    $me->moderatorBusinessName(),
			'moderatorProfession'=>     $me->moderatorProfession(),
			'hash'=>                    '', //hash isn't yet known
			'author'=>                  $me->authorName(),
			'authorInstitution' =>      $me->authorBusinessName(),
			'authorProfession'=>        $me->authorProfession(),
			"href"=> 	                $me->href,
			'answers'=>                 $me->answers(),
			'dateLastUpdated'=>         $me->lastModif,
			'dateOriginated'=>          $me->findDatePageOriginated(),
			'language'=>                $me->language(),
			'count'=>                   $me->countAll(),
			'keywords'=>                $me->keywords(),
			'categories'=>              $me->categories(),
			'scientificField'=>         $me->scientificField(),
			'minimumMathNeeded'=>       $me->minimumMathNeeded(),
			'minimumStatisticsNeeded'=> $me->minimumStatisticsNeeded(),
			'text'=> 	                $data
		);

		$me->raw['hash'] =  hash_hmac(
			"md5",
			JisonParser_Phraser_Handler::superSanitize(
				$me->raw['author'] .
				$me->raw['authorInstitution'] .
				$me->raw['authorProfession']
			),
			JisonParser_Phraser_Handler::superSanitize($data)
		);

		return $me;
	}

	static function pageForwardLink($page)
	{
		$me = new self($page);

		$me->raw = array(
			'websiteTitle'=>            $me->websiteTitle,
			'websiteSubtitle'=>         $me->page,
			'moderator'=>               $me->moderatorName(),
			'moderatorInstitution'=>    $me->moderatorBusinessName(),
			'moderatorProfession'=>     $me->moderatorProfession(),
			'hash'=>                    '', //hash isn't yet known
			'author'=>                  $me->authorName(),
			'authorInstitution' =>      $me->authorBusinessName(),
			'authorProfession'=>        $me->authorProfession(),
			'href'=>                    $me->href,
			'answers'=>                 $me->answers(),
			'dateLastUpdated'=>         $me->lastModif,
			'dateOriginated'=>          $me->findDatePageOriginated(),
			'language'=>                $me->language(),
			'count'=>                   $me->countAll(),
			'keywords'=>                $me->keywords(),
			'categories'=>              $me->categories(),
			'scientificField'=>         $me->scientificField(),
			'minimumMathNeeded'=>       $me->minimumMathNeeded(),
			'minimumStatisticsNeeded'=> $me->minimumStatisticsNeeded(),
			'text'=>                    '',//text isn't yet known
		);

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
			$this->questions = Tracker_Query::tracker('Wiki Attributes')
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
			$this->keywords = Tracker_Query::tracker('Wiki Attributes')
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

		if (empty($this->authorData)) {
			if ($version < 0) {
				$user = TikiLib::lib('trk')->getOne("SELECT user FROM tiki_pages WHERE pageName = ?", array($this->page));
			} else {
				$user = TikiLib::lib('trk')->getOne("SELECT user FROM tiki_history WHERE pageName = ? AND version = ?", array($this->page, $version));
			}

			if (empty($user))  return array();

			$authorData = Tracker_Query::tracker("Users")
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
			$moderatorData = Tracker_Query::tracker("Users")
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
				Tracker_Query::tracker('Wiki Attributes')
					->byName()
					->filterFieldByValue('Type', 'ForwardLink Accepted')
					->render(false)
					->query()
			);
		}
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
			$this->scientificField = Tracker_Query::tracker('Wiki Attributes')
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
			$this->minimumMathNeeded = Tracker_Query::tracker('Wiki Attributes')
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
			$this->minimumStatisticsNeeded = Tracker_Query::tracker('Wiki Attributes')
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
			foreach (TikiLib::lib("tiki")->list_languages() as $listLanguage) {
				if ($listLanguage['value'] == $this->lang) {
					$this->language = $listLanguage['name'];
					break;
				}
			}
		}

		return $this->language;
	}
}
