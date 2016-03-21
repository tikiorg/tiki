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
//		'selflinks' => true,				//if smarty self_links need to be removed
		//overall sort settings for the table - individual column settings are under columns below
		'sorts' => array(
			'type' => 'reset',				//choices: boolean true, boolean false, save, reset, savereset.
			'group' => true,				//overall switch to allow or disallow group headings
//			'multisort' => false,			//multisort on by default - set to false to disable
//			'initial' => [                  //to set initial sort icons in standard tables since sortList doesn't use selectors yet
//				'id' => 'abc',              //id of the th element that the table is sorted by on server side
//				'dir' => 'asc'              //direction of the default initial server-side sort
//			]
		),
		//overall filter settings for the table or external filters - individual column settings are under columns below
		'filters' => array(
			'type' => 'reset',						//choices: boolean true, boolean false, reset
/*			'external' => false,
			'hide' => false,					//to hide filters. choices: true, false (default)
			//for filters external to the table
			'external' => array(
				0 => array(
					'type' => 'dropdown',
					'options' => array(
						//label => url parameter value
						'Email not confirmed' => 'filterEmailNotConfirmed=on',
						'User not validated' => 'filterNotValidated=on',
						'Never logged in' => 'filterNeverLoggedIn=on',
					),
				),
			),
*/
		),
/*
		//to add pagination controls
		'pager' => array(
			'type' => true,					//choices: boolean true, boolean false
			'max' => 25,
			'expand' => array(50, 100, 250, 500),
		),
		//set whether filtering and sorting will be done server-side or client side
		'ajax' => array(
			'type' => false,
			'url' => 'tiki-adminusers.php?{sort:sort}&{filter:filter}',
			'offset' => 'offset'
			//url sort and filter params manipulated on the server side if set to false
			'custom' => false,
			//total count of all records - needs to be a hidden input returned by ajax in order for pager to work
			'servercount' => array(
				'id' => $ts_countid,
			),
			//record offset - needs to be a hidden input returned by ajax in order for pager to work properly
			'serveroffset' => array(
				'id' => $ts_offsetid,
			),
		),
		//set whether column, page or subtotals will be added
		'math' => array(
			'format' => '$(#,###.00)'       //see choices at http://mottie.github.io/tablesorter/docs/example-widget-math.html#mask_examples
			//add a grand total or amount related to all numbers in the table on the page
			'page' => 'sum'                 //see choices at http://mottie.github.io/tablesorter/docs/example-widget-math.html#attribute_settings
			'pagelabel' => 'Grand total'    //custom label
			'columnlabel' => 'Column total' //custon label for column totals
		),
		//determine whether the code uses columns selectors (e.g., th id) or indexes. With selectors the logic
		//for which columns are shown doesn't need to be recreated for tables with smarty templates where some
		//columns aren't shown based on logic. For plugins, indexes will generally need to be used since users set
		// the columns
*/
		'usecolselector' => false,
		'colselect' => array(
			'type' => false,
		),
/*
		//Set individual sort and filter settings for each column
		//No need to set if overall sorts and filters settings for the table are set to false above
		'columns' => array(				//zero-based column index or th selector, used only if column-specific settings
			0 => array(
				//sort settings for the 1st column
				'sort' => array(
					'type' => true,			//choices: boolean true, boolean false, text, digit, currency, percent,
											//usLongDate, shortDate, isoDate, dateFormat-ddmmyyyy, ipAddress, url, time
											//also string-min (sort strings in a numeric column as large negative number)
											//empty-top (sorts empty cells to the top)
					'dir' => 'asc',			//asc for ascending and desc for descending
					'ajax' =>'email',		//parameter name that is used when querying the database for this field value
											//when ajax is used
					'group' => 'letter'		//choices: letter (first letter), word (first word), number, date, date-year,
											//date-month, date-day, date-week, date-time. letter and word can be
											//extended, e.g., word-2 shows first 2 words. number-10 will group rows
											//in blocks of ten.
				),
				//filter settings for the 1st column
				'filter' => array(
					//for a text input box where user can type to filter
					'type' => 'text',							//choices: text, dropdown, date, range, non
					'placeholder' => 'Enter valid email...',	//override default placeholder text
					'ajax' => 'filterEmail',
				),
				//math settings for the 1st column
				'math' => 'col-sum'         //choices: see http://mottie.github.io/tablesorter/docs/example-widget-math.html#attribute_settings
			),
			1 => array(
				//sort settings for the 2nd column
				'sort' => array(
					'type' => false,
				),
				//filter settings for the 2nd column
				'filter' => array(
					//for a dropdown list for filtering
					'type' => 'dropdown',
					//options are optional -  automatically generated from column values if not set
					//but automatic values will only reflect rows returned from server if ajax is used
					'options' => array(
						'first filter',
						'second filter'
					)
				),
			),
			2 => array(
				//sort settings for the 3rd column
				'sort' => array(
					'type' => false,
				),
				//filter settings for the 3rd column
				'filter' => array(
					//a sliding range filter for numeric fields
					'type' => 'range',
					'from' => 10,
					'to' => 100,
					'style' => 'popup'				//choices: popup or inline
				),
			),
			3 => array(
				//sort settings for the 4th column
				'sort' => array(
					'type' => false,
				),
				//filter settings for the 4th column
				'filter' => array(
					//produces from and to date fields for filtering
					'type' => 'date',
					'from' => '2013-12-15',
					'to' => '2013-12-16',
					'format' => 'yy-mm-dd'
				),
			),
		),
*/
	);

	/**
	 * Default placeholder text for the different types of filters
	 * @var array
	 */
	protected $defaultFilters = array(
		'text' => array(
			'type' => 'text',
			'placeholder' => ''
		),
		//tra('Select a value')
		'dropdown' => array(
			'type' => 'dropdown',
			'placeholder' => ''
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
		'sorts' => array(
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
		'colselect' => array(
			'button' => array(
				'id' => '-colselectbtn',
				//tra('Show/hide columns')
				'text' => 'Show/hide columns',
			),
			'div' => array(
				'id' => '-colselectdiv',
			)
		),
	);

	/**
	 * Used by a second level of abstract classes extending this class to set different
	 * defaults for plugins vs standard tables
	 * @var null
	 */
	protected $default2 = null;
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

		//override second level of default settings
		$this->ts = $this->overrideSettings($this->default2, $this->ts);

		//get table-specific settings
		$ts = $this->getTableSettings();

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
	protected function overrideSettings($default, $settings)
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
		//TODO try array_column here
		if (isset($this->s['columns'])) {
			foreach ($this->s['columns'] as $col => $colinfo) {
				if (isset($colinfo['filter'])) {
					$ft = $colinfo['filter']['type'];
					//add default placeholder text
					if (isset($this->defaultFilters[$ft])) {
						$this->s['columns'][$col]['filter'] =
							array_replace_recursive($this->defaultFilters[$ft], $colinfo['filter']);
					}
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
	 * Correlate Tablesorter sort and filter url parameters to those used in Tiki for database calls
	 * This information will be passed to the jQuery code so that the url parameters generated by
	 * Tablesorter can be changed to their Tiki equivalents for the specific table
	 */
	private function setAjax()
	{
		if (!empty($this->s['ajax'])) {
			//sort and filter url parameters
			if (isset($this->s['columns']) && is_array($this->s['columns'])) {
				foreach ($this->s['columns'] as $col => $colinfo) {
					$colpointer =  $this->s['usecolselector'] ? substr($col,1)  : $col;
					if (isset($colinfo['sort']['ajax'])) {
						//tablesorter url param pattern is sort[0]=0 for ascending sort of first column
						$this->s['ajax']['sort']['sort-' . $colpointer] = $colinfo['sort']['ajax'];
					}
					if (isset($colinfo['filter']['ajax'])) {
						//tablesorter url param pattern is filter[0]=text for filter on first column
						$this->s['ajax']['colfilters']['filter-' . $colpointer] = $colinfo['filter']['ajax'];
					}  elseif (isset($colinfo['filter']['options'])) {
						foreach ($colinfo['filter']['options'] as $label => $value) {
							$label = rawurlencode($label);
							$this->s['ajax']['colfilters']['filter-' . $colpointer][$label] = $value;
						}
					}
				}
			}
			//external filter params
			if (is_array($this->s['filters']['external'])) {
				foreach($this->s['filters']['external'] as $key => $info) {
					if (isset($info['options']) && is_array($info['options'])) {
						foreach($info['options'] as $opt => $value) {
							$this->s['ajax']['extfilters'][] = rawurlencode($value);
						}
					}
				}
			}
		}
	}
}
