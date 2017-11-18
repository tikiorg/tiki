<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class IDS_Rule
{

	protected $id;
	protected $regex;
	protected $description;
	protected $tags;
	protected $impact;

	public function __construct($id)
	{
		$this->id = $id;
	}

	public static function getCustomFilePath()
	{

		global $prefs;

		$path = $prefs['ids_custom_rules_file'];

		if (empty($path)) {
			$path = 'temp/ids_custom_rules.json';
		}

		return $path;
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return mixed
	 */
	public function getRegex()
	{
		return $this->regex;
	}

	/**
	 * @return mixed
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @return mixed
	 */
	public function getTags()
	{
		return $this->tags;
	}

	/**
	 * @return mixed
	 */
	public function getImpact()
	{
		return $this->impact;
	}

	/**
	 * @param mixed $regex
	 */
	public function setRegex($regex)
	{
		$this->regex = $regex;
	}

	/**
	 * @param mixed $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}

	/**
	 * @param mixed $tags
	 */
	public function setTags($tags)
	{
		if (! is_array($tags)) {
			$tags = explode(',', $tags);
		}

		foreach ($tags as $key => $tag) {
			$tags[$key] = trim($tag);
		}

		$this->tags = $tags;
	}

	/**
	 * @param mixed $impact
	 */
	public function setImpact($impact)
	{
		$this->impact = $impact;
	}

	public function save()
	{

		$customRules = self::getAllRules();

		$updated = false;
		foreach ($customRules as $key => $rule) {
			if ($rule->id == $this->id) {
				$customRules[$key] = $this;
				$updated = true;
				break;
			}
		}

		if (! $updated) {
			$customRules[] = $this;
		}

		return $this->writeRules($customRules);
	}

	public function delete()
	{

		$customRules = self::getAllRules();

		foreach ($customRules as $key => $rule) {
			if ($rule->id == $this->id) {
				unset($customRules[$key]);
				break;
			}
		}

		return $this->writeRules($customRules);
	}

	private function writeRules($customRules)
	{
		$rules = [];
		foreach ($customRules as $customRule) {
			$rules[] = [
				'id' => $customRule->id,
				'rule' => $customRule->regex,
				'description' => $customRule->description,
				'tags' => [
					'tag' => $customRule->tags,
				],
				'impact' => $customRule->impact,
			];
		}

		$filter = [
			'filters' => $rules
		];

		$filename = self::getCustomFilePath();

		return file_put_contents($filename, json_encode($filter));
	}

	/**
	 * @return array
	 */
	public static function getAllRules()
	{

		$filename = self::getCustomFilePath();

		if (! file_exists($filename)) {
			return [];
		}

		$data = file_get_contents($filename);
		$customRules = json_decode($data, true);

		$rules = [];
		foreach ($customRules['filters'] as $customRule) {
			$rule = new self($customRule['id']);
			$rule->setRegex($customRule['rule']);
			$rule->setDescription($customRule['description']);
			$rule->setTags($customRule['tags']['tag']);
			$rule->setImpact($customRule['impact']);

			$rules[] = $rule;
		}

		return $rules;
	}

	/**
	 * @param $ruleID
	 * @return IDS_Rule | false
	 */
	public static function getRule($ruleID)
	{

		$customRules = self::getAllRules();

		foreach ($customRules as $rule) {
			if ($rule->id == $ruleID) {
				return $rule;
			}
		}

		return false;
	}
}
