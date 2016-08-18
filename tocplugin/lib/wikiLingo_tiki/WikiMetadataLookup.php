<?php

use RedBean_Facade as R;

class WikiMetadataLookup
{
	public $page;
    public $language;
    public $moderatorData;
    public $findDatePageOriginated;
    public $countAll;
    public $categories = array();
    public $scientificField;
    public $minimumMathNeeded;
    public $minimumStatisticsNeeded;

    private $_language;


	public function __construct($page)
	{
        global $prefs;
		$this->page = $page;
        $this->_language = $prefs['site_language'];
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

		return (isset($this->categories) ? $this->categories : array());
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
				if ($listLanguage['value'] == $this->_language) {
					$this->language = urlencode($listLanguage['name']);
					break;
				}
			}
		}

		return $this->language;
	}

    public function getPartial()
    {
        $metadata = new FLP\Metadata();

        $metadata->answers = $this->answers();
        $metadata->author = $this->authorName();
        $metadata->authorInstitution = $this->authorBusinessName();
        $metadata->authorProfession = $this->authorProfession();
        $metadata->categories = $this->categories();
        $metadata->count = $this->countAll(); // is this the correct count for use here? (LDG)
        $metadata->dateLastUpdated = '';
        $metadata->dateOriginated = $this->findDatePageOriginated();
        $metadata->hash = '';
        $metadata->href = '';
        $metadata->keywords = $this->keywords();
        $metadata->language = $this->language();
        $metadata->minimumMathNeeded = $this->minimumMathNeeded();
        $metadata->minimumStatisticsNeeded = $this->minimumStatisticsNeeded();
        $metadata->moderator = $this->moderatorName();
        $metadata->moderatorInstitution = $this->moderatorBusinessName();
        $metadata->moderatorProfession = $this->moderatorProfession();
        $metadata->scientificField = $this->scientificField();
        $metadata->text = '';
        $metadata->websiteSubtitle = '';
        $metadata->websiteTitle = $this->page;
        return $metadata;
    }
}