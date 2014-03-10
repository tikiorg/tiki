<?php

use RedBean_Facade as R;

class WikiMetadataLookup
{
	public $page;

	public function __construct($page)
	{
		$this->page = $page;
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
		return (new Tracker_Query('Wiki Attributes'))
			->byName()
			->filterFieldByValue('Type', 'Question')
			->filterFieldByValue('Page', $this->page)
			->render(false)
			->query();
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

	public function keywords()
	{
		return (new Tracker_Query('Wiki Attributes'))
				->byName()
				->filterFieldByValue('Type', 'Keywords')
				->filterFieldByValue('Page', $this->page)
				->render(false)
				->query();
	}

	public function author($version = -1)
	{
		global $tikilib;

		//TODO: abstract
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

		return $authorData;
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
			foreach (TikiLib::lib("tiki")->list_languages() as $listLanguage) {
				if ($listLanguage['value'] == $this->lang) {
					$this->language = urlencode($listLanguage['name']);
					break;
				}
			}
		}

		return $this->language;
	}
}