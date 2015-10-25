<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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
 * Class Table_Code_Manager
 *
 * Determines the order and nesting of the sections of code by calling the class for each section in the
 *appropriate order
 *
 * @package Tiki
 * @subpackage Table
 * @uses Table_Code_Abstract
 */
class Table_Code_Manager extends Table_Code_Abstract
{
	/**
	 * Order and nesting of code generation
	 *
	 * @var array
	 */
	protected $subclasses = array(
		//other is jQuery needed before the tablesorter function call
		'other' => '',
		//this is the jQuery tablesorter function call
		'main' => array(
			'mainOptions' => '',
			'widgetOptions' => '',
		),
		//events to bind to the tablesorter function call
		'bind' => ''
	);

	/**
	 * Call the classes for each section in the order determined by the $subclasses property
	 * and put sections together into final overall jQuery code
	 */
	public function setCode()
	{
		foreach ($this->subclasses as $key => $val) {
			if (empty($val)) {
				$instance = Table_Factory::build($key, parent::$s, 'code');
				$instance::$level1 = $key;
				$instance::$level2 = null;
				$instance->setCode();
			} elseif (is_array($val)) {
				foreach ($val as $key2 => $val2) {
					if (empty($val2)) {
						$instance = Table_Factory::build($key2, parent::$s, 'code');
						$instance::$level1 = $key;
						$instance::$level2 = $key2;
						$instance->setCode();
					}
				}
			}
		}
		//put sections together into final overall code
		self::$code['main'] = $this->iterate(self::$code['main'], $this->nt . '$(\''. self::$tid
			. '\').tablesorter({', $this->nt . '})', '', '');
		if (empty(self::$code['bind'])) {
			self::$code['main'] .= ';';
		}

		$parts = '';
		foreach (self::$code as $section) {
			$parts .= $section;
		}
		//unhide div holding the table
		$parts .= $this->nt . '$(\'div#' . self::$id . '\').css(\'visibility\', \'visible\');';

		self::$code = $parts;
	}
}
