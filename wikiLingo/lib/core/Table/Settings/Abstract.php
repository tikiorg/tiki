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
//		'selflinks' => true,				//if smarty self_links need to be removed
		'sort' => array(
			'group' => true,
			'type' => 'reset',				//choices: boolean true, boolean false, save, reset, savereset.
/*			'columns' => array(				//zero-based column index, used only if column-specific settings
				0 => array(
					'type' => true,			//choices: boolean true, boolean false, text, digit, currency, percent,
											//usLongDate, shortDate, isoDate, dateFormat-ddmmyyyy, ipAddress, url, time
											//also string-min (sort strings in a numeric column as large negative number)
											//empty-top (sorts empty cells to the top)
					'dir' => 'asc',			//asc for ascending and desc for descending
					'ajax' => 'login',
					'group' => 'letter'		//choices: letter (first letter), word (first word), number, date, date-year,
											//date-month, date-day, date-week, date-time. letter and word can be
											//extended, e.g., word-2 shows first 2 words. number-10 will group rows
											//in blocks of ten.
				),
			),
			'multisort' => false,				//multisort on by default - set to false to disable
*/
		),
		'filters' => array(
			'type' => 'reset',						//choices: boolean true, boolean false, reset
			'external' => false,
/*			'hide' => false,					//to hide filters. choices: true, false (default)
			'columns' => array(
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
					'from' => 10,
					'to' => 100,
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
			'type' => true,					//choices: true, false
			'max' => 25,
			'expand' => array(50, 100, 250, 500),
		),*/
		'ajax' => array(
			'type' => false,
//			'url' => 'tiki-adminusers.php?{sort:sort}&{filter:filter}',
		)
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
			'from' => '',
			'to'	=> '',
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
			'reset' => array(
				'id' => '-sortreset',
				//tra('Unsort')
				'text' => 'Unsort',
			),
		),
		'filters' => array(
			'reset' => array(
				'id' => '-filterreset',
				//tra('Clear Filters')
				'text' => 'Clear Filters',
			),
			'external' => array(
				'id' => '-ext',
			),
		),
		'pager' => array(
			'disable' => array(
				'id' => '-pagerbutton',
				'text' => array(
					//tra('Enable Pager')
					'enable' => 'Enable Pager',
					//tra('Disable Pager')
					'disable' => 'Disable Pager',
				),
			),
			'controls' => array(
				'id' => '-pager',
			),
		),
		'ajax' => array(
			'servercount' => array(
				'id' => '-count',
			),
			'serveroffset' => array(
				'id' => '-offset',
			),
		),
	);

	/**
	 * Used by table classes extending this class for table-specific default settings
	 * @var null
	 */
	protected $ts = null;
	/**
	 * Used by table classes extending this class to manipulate user-specific settings
	 * @var null
	 */
	protected $us = null;
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

		$this->setUserSettings($settings);

		//override any table settings with user settings
		$this->ts = $this->overrideSettings($this->ts, $this->us);

		//get table-specific settings
		$ts = $this->getTableSettings();
		if (isset($ts['sort']['columns'])) {
			$ts['sort']['columns'] = array_values($ts['sort']['columns']);
		}
		if (isset($ts['filters']['columns'])) {
			$ts['filters']['columns'] = array_values($ts['filters']['columns']);
		}

		//override generic defaults with any table-specific defaults
		$this->s = $this->overrideSettings($this->default, $ts);

		//set placeholders for filters
		$this->setPlaceholders();

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
		foreach ($this->ids as $type => $elements) {
			foreach($elements as $element => $info) {
				if (isset($elements[$element]['text'])) {
					if (is_array($elements[$element]['text'])) {
						foreach ($elements[$element]['text'] as $each => $text) {
							$this->default[$type][$element]['text'][$each] = htmlspecialchars(tra($text));
						}
					} else {
						$this->default[$type][$element]['text'] = htmlspecialchars(tra($elements[$element]['text']));
					}
				}
			}
		}

		foreach ($this->defaultFilters as $type => $settings) {
			foreach ($settings as $each => $text) {
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
	 * Get user-specific settings
	 *
	 * @return null
	 */
	protected function setUserSettings($settings)
	{
		$this->us = $settings;
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
		if (is_array($default) && is_array($settings)) {
			$ret = array_replace_recursive($default, $settings);
		} elseif (is_array($settings)) {
			$ret = $settings;
		} elseif (is_array($default)) {
			$ret = $default;
		}
		return $ret;
	}

	/**
	 * Set placeholders for filters
	 */
	private function setPlaceholders() {
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
		if (isset($this->s['id']) && isset($this->default['id']) && $this->s['id'] == $this->default['id']) {
			static $i = 0;
			++$i;
			$this->s['id'] .= $i;
		}
		foreach ($this->ids as $type => $elements) {
			if (isset($this->s[$type]['type'])
				&& $this->s[$type]['type'] !== false
				&& $this->s[$type]['type'] !== 'save') {
				foreach($elements as $element => $info) {
					//for multiple elements
					if (isset($this->s[$type][$element][0])) {
						foreach ($this->s[$type][$element] as $key => $info) {
							if (!isset($this->s[$type][$element][$key]['id'])) {
								$this->s[$type][$element][$key]['id'] = $this->s['id']
									. htmlspecialchars($elements[$element]['id'] . $key);
							}
						}
					} elseif ((!isset($this->s[$type][$element]) || (isset($this->s[$type][$element])
						&& $this->s[$type][$element] !== false)) && !isset($this->s[$type][$element]['id']))
					{
						$this->s[$type][$element]['id'] = $this->s['id'] . htmlspecialchars($elements[$element]['id']);
					}
				}
			}
		}
	}

	/**
	 * Set levels for pager dropdown that allows for displaying various numbers of rows
	 */
	private function setMax()
	{
		if (isset($this->s['pager']['type']) && $this->s['pager']['type'] !== false) {
			if (isset($GLOBALS['maxRecords']) && !isset($this->s['pager']['max'])) {
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
	private function setAjax()
	{
		if (!empty($this->s['ajax'])) {
			//sort params
			if (isset($this->s['sort']['columns']) && is_array($this->s['sort']['columns'])) {
				foreach ($this->s['sort']['columns'] as $col => $info) {
					if (isset($info['ajax'])) {
						//tablesorter url param pattern is sort[0]=0 for ascending sort of first column
						$this->s['ajax']['sort']['sort[' . $col . ']'] = $info['ajax'];
					}
				}
			}
			//column filter params
			if (isset($this->s['filters']['columns']) && is_array($this->s['filters']['columns'])) {
				foreach ($this->s['filters']['columns'] as $col => $info) {
					if (isset($info['ajax'])) {
						//tablesorter url param pattern is filter[0]=text for filter on first column
						$this->s['ajax']['colfilters']['filter[' . $col . ']'] = $info['ajax'];
					} elseif (isset($info['options'])) {
						foreach ($info['options'] as $label => $value) {
							$label = rawurlencode($label);
							$this->s['ajax']['colfilters']['filter[' . $col . ']'][$label] = $value;
						}
					}
				}
			}
			//external filter params
			if (is_array($this->s['filters']['external'])) {
				foreach($this->s['filters']['external'] as $key => $info) {
					if (is_array($info['options'])) {
						foreach($info['options'] as $opt => $value) {
							$this->s['ajax']['extfilters'][] = rawurlencode($value);
						}
					}
				}
			}
		}
	}
}
