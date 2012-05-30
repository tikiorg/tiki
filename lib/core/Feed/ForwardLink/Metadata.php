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

	function __construct($page)
	{
		global $tikilib;

		$this->page = $page;

		$details = $tikilib->fetchAll("SELECT lang, lastModif FROM tiki_pages WHERE pageName = ?", $page);

		$this->lang = $details['lang'];
		$this->lastModif = $details['lastModif'];
	}

	static function pageFromTextLink($page, $data, $hash)
	{
		global $prefs, $tikilib;

		$me = new self($page);

		//setup clipboard data
		$page = urlencode($page);
		$href = TikiLib::tikiUrl() . 'tiki-index.php?page=' . $page;
		$websiteTitle = urlencode($prefs['browsertitle']);
		$userData = $me->findAuthorData();
		$moderatorData = $me->findModeratorData();
		$phraser = new JisonParser_Phraser_Handler();
		$id = implode("", $phraser->sanitizeToWords($data));

		return array(
			'websiteTitle'=>            $websiteTitle,
			'websiteSubtitle'=>         $page,
			'moderator'=>               (isset($moderatorData['Name']) ? $moderatorData['Name'] : ''),
			'moderatorInstitution'=>    (isset($moderatorData['Business Name']) ? $moderatorData['Business Name'] : ''),
			'moderatorProfession'=>     (isset($moderatorData['Profession']) ? $moderatorData['Profession'] : ''),
			'hash'=>                    '', //hash isn't yet known
			'author'=>                  (isset($userData['Name']) ? $userData['Name'] : ''),
			'authorInstitution' =>      (isset($userData['Business Name']) ? $userData['Business Name'] : ''),
			'authorProfession'=>        (isset($userData['Profession']) ? $userData['Profession'] : ''),
			'href'=>                    $href,
			'answers'=>                 $me->answers(),
			'dateLastUpdated'=>         $me->lastModif(),
			'dateLastUpdated'=>         $me->findDatePageOriginated($page),
			'language'=>                $me->language(),
			'count'=>                   $me->countAll(),
			'keywords'=>                implode(JisonParser_Phraser_Handler::sanitizeToWords($me->keywords()), ','),
			'categories'=>              $me->categories($page),
			"text"=> 	                $data,
			"href"=> 	                $tikilib->tikiUrl() . "tiki-index.php?page=$page#" . $id,
			"id"=>		                $hash. "_" . $page . "_" . $id
		);
	}

	static function pageFromForwardLink($page)
	{
		global $prefs, $tikilib;
		$me = new self($page);

		//setup clipboard data
		$page = urlencode($page);
		$href = TikiLib::tikiUrl() . 'tiki-index.php?page=' . $page;
		$websiteTitle = urlencode($prefs['browsertitle']);
		$userData = $me->findAuthorData();
		$moderatorData = $me->findModeratorData();

		return array(
			'websiteTitle'=>            $websiteTitle,
			'websiteSubtitle'=>         $page,
			'moderator'=>               (isset($moderatorData['Name']) ? $moderatorData['Name'] : ''),
			'moderatorInstitution'=>    (isset($moderatorData['Business Name']) ? $moderatorData['Business Name'] : ''),
			'moderatorProfession'=>     (isset($moderatorData['Profession']) ? $moderatorData['Profession'] : ''),
			'hash'=>                    '', //hash isn't yet known
			'author'=>                  (isset($userData['Name']) ? $userData['Name'] : ''),
			'authorInstitution' =>      (isset($userData['Business Name']) ? $userData['Business Name'] : ''),
			'authorProfession'=>        (isset($userData['Profession']) ? $userData['Profession'] : ''),
			'href'=>                    $href,
			'answers'=>                 $me->answers(),
			'dateLastUpdated'=>         $me->lastModif(),
			'dateLastUpdated'=>         $me->findDatePageOriginated($page),
			'language'=>                $me->language(),
			'count'=>                   $me->countAll(),
			'keywords'=>                implode(JisonParser_Phraser_Handler::sanitizeToWords($me->keywords()), ','),
			'categories'=>              $me->categories($page)
		);
	}

	function answers()
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

	function questions()
	{
		return Tracker_Query::tracker('Wiki Attributes')
			->byName()
			->filterFieldByValue('Type', 'Question')
			->filterFieldByValue('Page', $this->page)
			->query();
	}

	function keywords()
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

		return $keywords;
	}

	public function findAuthorData($version = -1)
	{
		global $tikilib;

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


		return $authorData;
	}

	public function findModeratorData()
	{
		global $tikilib;

		$moderatorData = Tracker_Query::tracker("Users")
			->byName()
			->filterFieldByValue('login', 'admin') //admin is un-deletable
			->getOne();
		$moderatorData = end($moderatorData);

		if (empty($authorData['Name'])) {
			$moderatorData['Name'] = $tikilib->get_user_preference('admin', "realName");
		}

		return $moderatorData;
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

	function scientificField()
	{
		return Tracker_Query::tracker('Wiki Attributes')
			->byName()
			->filterFieldByValue('Type', 'Scientific Field')
			->filterFieldByValue('Page', $this->page)
			->query();
	}

	function minimumMathNeeded()
	{
		return Tracker_Query::tracker('Wiki Attributes')
			->byName()
			->filterFieldByValue('Type', 'Scientific Field')
			->filterFieldByValue('Page', $this->page)
			->query();
	}

	function minimumStatisticsNeeded() {
		return Tracker_Query::tracker('Wiki Attributes')
			->byName()
			->filterFieldByValue('Type', 'Scientific Field')
			->filterFieldByValue('Page', $this->page)
			->query();
	}

	function language()
	{
		foreach(TikiLib::lib("tiki")->list_languages() as $listLanguage) {
			if ($listLanguage['value'] == $this->lang) {
				$language = $listLanguage['name'];
			}
		}
	}

	function lastModif()
	{
		return $this->lastModif;
	}
}