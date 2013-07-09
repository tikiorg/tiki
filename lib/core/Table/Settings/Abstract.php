<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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
 * Class Table_Settings_Abstract
 *
 * Abstract class to produce default settings for applying the jQuery Tablesorter plugin to a table
 *
 * @package Tiki
 * @subpackage Table
 */
abstract class Table_Settings_Abstract
{
	/**
	 * Generic default settings - portions commented out are to illustrate options
	 * Values here are overriden by table-specific defaults and any user settings
	 * @var array
	 */
	protected $default = array(
		//this is the id of the table
		'id' => 'tsTable',
		//TODO choosing themes is not yet implemented
		'theme' => 'tiki',
		'total' => '',
		'sort' => array(
			'type' => 'reset',						//choices: boolean true, boolean false, save, reset, savereset.
/*			'columns' => array(					//zero-based column index, used only if column-specific settings
				0 => true, false, asc, desc,
			),
*/
			'multisort' => true,
		),
		'filters' => array(
			'type' => 'reset',						//choices: boolean true, boolean false, reset
			'hide' => false,					//to hide filters. choices: true, false
/*			'columns' => array(
				0 => array(
					'type' => 'text',					//choices: text, dropdown, date, range, none
					'placeholder' => 'Enter a value'	//override default placeholder text
				),
				4 => array(
					'type' => 'dropdown',
					'options' => array(		//options optional; automatically generated from column values if not set
						'first filter',
						'second filter'
					)
				),
				2 => array(
					'type' => 'range',
					'min' => 10,
					'max' => 100,
					'style' => popup				//choices: popup or inline
				),
				3 => array(
					'type' => 'date',
					'from' => '2013-12-15',
					'to' => '2013-12-16',
					'format' => 'yy-mm-dd'
				)
			),
*/
		),
/*		'pager' => array(
			'type' => 'disable',					//choices: true, false, disable
			'max' => 25,
			'expand' => array(50, 100, 250, 500),
			'ajax' => array(						//can also be set to false
				'url' => 'tiki-adminusers.php?{sort:sort}&{filter:filter}',
			)
		)*/
	);

	/**
	 * Default placeholder text for the different types of filters
	 * @var array
	 */
	protected $defaultFilters = array(
		'text' => array(
			'type' => 'text',
			//tra('Type to filter...')
			'placeholder' => 'Type to filter...'
		),
		//tra('Select a value')
		'dropdown' => array(
			'type' => 'dropdown',
			//tra('Select a value')
			'placeholder' => 'Select a value'
		),
		'date'	=> array(
			'type' => 'date',
			'format' => 'yy-mm-dd',
			'from' => 'from',
			'to'	=> 'to',
		),
		'range'	=> array(
			'type' => 'range',
			'style' => 'inline',		//other option is popup. from and to values can also be set
		),
	);

	/**
	 * Strings used to create button and pager control ids and default text
	 * @var array
	 */
	protected $ids = array(
		'sort' => array(
			'id' => '-sortreset',
			//tra('Reset Sort')
			'text' => 'Reset Sort',
		),
		'filters' => array(
			'id' => '-filterreset',
			//tra('Reset Filter')
			'text' => 'Reset Filter',
		),
		'pager' => array(
			'id' => '-pagerbutton',
			'text' => array(
				//tra('Enable Pager')
				'enable' => 'Enable Pager',
				//tra('Disable Pager')
				'disable' => 'Disable Pager',
			),
		),
		'pagercontrols' => array(
			'id' => '-pager',
		),
	);

	/**
	 * Used by table classes extending this class for table-specific default settings
	 * @var null
	 */
	protected $ts = null;
	/**
	 * Final code settings
	 * @var
	 */
	public $s;

	/**
	 * Constructs settings for the table
	 *
	 * @param array $settings		user-defined settings array
	 */
	public function __construct(array $settings)
	{
		//translate default text
		$this->translateDefault();

		//get table-specific settings
		$ts = $this->getTableSettings();

		//override generic defaults with any table-specific defaults
		$this->overrideSettings($this->default, $ts);

		//now override any settings with user settings
		$this->overrideSettings($this->s, $settings);

		//create id's for any buttons based on table id
		$this->setIds();

		//set pager row display levels
		$this->setMax();

		//create Ajax arrays for filtering and sorting
		$this->setAjax();
	}

	/**
	 * Translate and filter text
	 */
	private function translateDefault()
	{
		foreach($this->ids as $type => $settings) {
			if (isset($settings['text'])) {
				if (is_array($settings['text'])) {
					foreach($settings['text'] as $each => $text) {
						$this->default[$type]['text'][$each] = htmlspecialchars(tra($text));
					}
				} else {
					$this->default[$type]['text'] = htmlspecialchars(tra($settings['text']));
				}
			}
		}

		foreach($this->defaultFilters as $type => $settings) {
			foreach($settings as $each => $text) {
				if ($each == 'placeholder' || ($type == 'date' && ($each == 'from' || $each == 'to'))) {
					$this->defaultFilters[$type][$each] = htmlspecialchars(tra($text));
				}
			}
		}
	}

	/**
	 * Get table-specific settings
	 *
	 * @return null
	 */
	protected function getTableSettings()
	{

		return $this->ts;
	}

	/**
	 * Used to override generic default settings with table-specific settings
	 * and to override that result with user settings
	 *
	 * @param $default
	 * @param $settings
	 */
	private function overrideSettings($default, $settings)
	{
		$this->s = array_replace_recursive($default, $settings);
		if (isset($this->s['filters']['columns'])) {
			foreach ($this->s['filters']['columns'] as $col => $filterinfo) {
				$ft = $filterinfo['type'];
				//add default placeholder text
				if (isset($this->defaultFilters[$ft])) {
					$this->s['filters']['columns'][$col] =
						array_replace_recursive($this->defaultFilters[$ft], $filterinfo);
				}
			}
		}
	}

	/**
	 * Automatically set the HTML ids for buttons and pager controls based on the overall table id
	 */
	private function setIds()
	{
		if ($this->s['id'] == $this->default['id']) {
			static $i = 0;
			++$i;
			$this->s['id'] .= $i;
		}
		foreach ($this->ids as $type => $settings) {
			if ($type == 'pagercontrols' || ($this->s[$type]['type'] !== false
				&& $this->s[$type]['type'] != 'save' && !isset($this->s[$type]['id'])))
			{
				$this->s[$type]['id'] = $this->s['id'] . htmlspecialchars($settings['id']);
			}
		}
	}

	/**
	 * Set levels for pager dropdown that allows for displaying various numbers of rows
	 */
	private function setMax()
	{
		if (isset($this->s['pager']) && $this->s['pager']['type'] !== false) {
			if (isset($GLOBALS['maxRecords']) && !isset($this->s['pager']['max']))
			{
				$this->s['pager']['max'] = $GLOBALS['maxRecords'];
			} elseif (!isset($this->s['pager']['max'])) {
				$this->s['pager']['max'] = 25;
			}
			if (!isset($this->s['pager']['expand']) && isset($this->s['pager']['max'])) {
				$this->s['pager']['expand'] = array(
					$this->s['pager']['max'],
					2 * $this->s['pager']['max'],
					4 * $this->s['pager']['max'],
					12 * $this->s['pager']['max'],
				);
			}
		}
	}

	/**
	 * Correlate Tablesorter sort and filter params to those used in Tiki for the table
	 * This information will be passed to the jQuery code so that the url paramters generated by
	 * Tablesorter can be changed to their Tiki equivalents for the specific table
	 */
	private function setAjax() {
		foreach ($this->s['sort']['columns'] as $col => $info) {
			if (isset($info['ajax'])) {
				$this->s['ajax']['sort']['sort[' . $col . ']'] = $info['ajax'];
			}
		}
		foreach ($this->s['filters']['columns'] as $col => $info) {
			if (isset($info['ajax'])) {
				$this->s['ajax']['filters']['filter[' . $col . ']'] = $info['ajax'];
			} elseif (isset($info['options'])) {
				foreach ($info['options'] as $label => $value) {
					$label = rawurlencode($label);
					$this->s['ajax']['filters']['filter[' . $col . ']'][$label] = $value;
				}
			}
		}
	}
}