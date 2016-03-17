<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

/**
 * SerializedList manages lists of objects in prefs
 *
 * Should be extended in your own class to use
 * TODO refactor toolbars and plugin aliasses to use this
 */
abstract class SerializedList
{
	protected $name = '';
	protected $data;
	protected $prefPrefix;

	/**
	 * Constructor
	 * poss add the prefPrefix and data init as params
	 * also getting a named item should be a separate step?
	 *
	 * @param string $name
	 */
	public function __construct($name)
	{
		global $prefs;

		$this->initPrefPrefix();

		$this->name = strtolower(TikiLib::remove_non_word_characters_and_accents($name));
		if (!empty($this->name) && !empty($prefs[$this->getPrefName()])) {
			$this->loadPref();
		} else {
			$this->initData();
		}
	}

	abstract public function initPrefPrefix();	// to be declared to set $this->prefPrefix = 'your_pref_prefix_'
	abstract public function initData();		// func to set $this->data as you need it
    /**
     * @param $params
     * @return mixed
     */
    abstract public function setData($params);	// func to set the date

	public function getData()
	{
		return $this->data;
	}

    /**
     * @return string
     */
    public function getName()
	{
		return $this->name;
	}

    /**
     * @return string
     */
    public function getPrefName()
	{
		return $this->prefPrefix . $this->name;
	}

    /**
     * @return string
     */
    public function getListName()
	{
		return $this->prefPrefix . 'list';
	}

    /**
     * @return array|mixed
     */
    public function getPrefList()
	{
		global $prefs;

		if ( isset($prefs[$this->getListName()]) ) {
			$custom = @unserialize($prefs[$this->getListName()]);
			sort($custom);
		} else {
			$custom = array();
		}

		return $custom;
	}

    /**
     * @return mixed
     */
    public function loadPref()
	{
		global $prefs, $tikilib;

		$this->data = unserialize($prefs[$this->getPrefName()]);
		return $this->data;
	}

	public function savePref()
	{
		global $prefs, $tikilib;

		$list = $this->getPrefList();

		$tikilib->set_preference($this->getPrefName(), serialize($this->data));

		if ( !in_array($this->name, $list) ) {
			$list[] = $this->name;
			$tikilib->set_preference($this->getListName(), serialize($list));
		}
	}

	public function deletePref()
	{
		global $prefs, $tikilib;

		$prefName = $this->getPrefName();
		if ( isset($prefs[$prefName]) ) {
			$tikilib->delete_preference($prefName);
		}
		$list = $this->getPrefList();

		if ( in_array($this->name, $list) ) {
			$list = array_diff($list, array($this->name));
			$tikilib->set_preference($this->getListName(), serialize($list));
		}
	}

}
