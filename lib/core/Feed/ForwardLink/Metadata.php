<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$


class Feed_ForwardLink_Metadata
{
	var $page;
	var $lang;
	var $lastModif;
	var $href;
	var $websiteTitle;
	var $moderatorData;
	var $authorData;
	var $raw;

	function __construct($page)
	{
		global $tikilib, $prefs;

		$this->page = $page;

		$details = $tikilib->fetchAll("SELECT lang, lastModif FROM tiki_pages WHERE pageName = ?", $page);
		$detail = end($details);

		$this->lang = $detail['lang'];
		$this->lastModif = $detail['lastModif'];
		$this->websiteTitle = $prefs['browsertitle'];
		$this->href = TikiLib::tikiUrl() . 'tiki-index.php?page=' . $page;
	}

	static function pageTextLink($page, $data, $hash)
	{
		$me = new self($page);

		$phraser = new JisonParser_Phraser_Handler();
		$id = implode("", $phraser->sanitizeToWords($data));

		$me->raw = array(
			'websiteTitle'=>            $me->websiteTitle,
			'websiteSubtitle'=>         $me->page,
			'moderator'=>               $me->moderatorName(),
			'moderatorInstitution'=>    $me->moderatorBusinessName(),
			'moderatorProfession'=>     $me->moderatorProfession(),
			'author'=>                  $me->authorName(),
			'authorInstitution' =>      $me->authorBusinessName(),
			'authorProfession'=>        $me->authorProfession(),
			"href"=> 	                $me->href . "#" . $id, //the id is composed of the words of the data the textlink surrounds
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
			'text'=> 	                $data,
			'id'=>		                $hash. "_" . $me->page . "_" . $id //the id of the textlink is different than that of the href, this is sort of a unique identifier so that we can later find it without having an href
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
		return Tracker_Query::tracker('Wiki Attributes')
			->byName()
			->filterFieldByValue('Type', 'Question')
			->filterFieldByValue('Page', $this->page)
			->query();
	}

	public function keywords()
	{
		$keywords = Tracker_Query::tracker('Wiki Attributes')
			->byName()
			->filterFieldByValue('Type', 'Keywords')
			->filterFieldByValue('Page', $this->page)
			->query();

		if (isset($keywords) && is_array($keywords)) {
			$keywords = end($keywords);
			$keywords = $keywords['Value'];
		}

		$keywords = implode(JisonParser_Phraser_Handler::sanitizeToWords($keywords), ',');

		return $keywords;
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
		$date = TikiLib::lib('trk')->getOne('SELECT lastModif FROM tiki_history WHERE pageName = ? ORDER BY lastModif DESC', array($this->page));

		if (empty($date)) {
			//page doesn't yet have history
			$date = TikiLib::lib('trk')->getOne('SELECT lastModif FROM tiki_pages WHERE pageName = ?', array($this->page));
		}

		return $date;
	}

	public function countAll()
	{
		return count(
			Tracker_Query::tracker('Wiki Attributes')
				->byName()
				->filterFieldByValue('Type', 'ForwardLink')
				->render(false)
				->query()
		);
	}

	public function categories()
	{
		$categories = array();
		foreach(TikiLib::lib('categ')->get_object_categories('wiki page', $this->page) as $categoryId) {
			$categories[] = TikiLib::lib('categ')->get_category_name($categoryId);
		}

		return $categories;
	}

	public function scientificField()
	{
		return Tracker_Query::tracker('Wiki Attributes')
			->byName()
			->filterFieldByValue('Type', 'Scientific Field')
			->filterFieldByValue('Page', $this->page)
			->query();
	}

	public function minimumMathNeeded()
	{
		return Tracker_Query::tracker('Wiki Attributes')
			->byName()
			->filterFieldByValue('Type', 'Scientific Field')
			->filterFieldByValue('Page', $this->page)
			->query();
	}

	public function minimumStatisticsNeeded() {
		return Tracker_Query::tracker('Wiki Attributes')
			->byName()
			->filterFieldByValue('Type', 'Scientific Field')
			->filterFieldByValue('Page', $this->page)
			->query();
	}

	public function language()
	{
		foreach(TikiLib::lib("tiki")->list_languages() as $listLanguage) {
			if ($listLanguage['value'] == $this->lang) {
				$language = $listLanguage['name'];
			}
		}
	}
}