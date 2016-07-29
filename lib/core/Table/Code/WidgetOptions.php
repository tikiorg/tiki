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
 * Class Table_Code_WidgetOptions
 *
 * Creates the code for the widget options portion of the Tablesorter jQuery code
 *
 * @package Tiki
 * @subpackage Table
 * @uses Table_Code_Manager
 */
class Table_Code_WidgetOptions extends Table_Code_Manager
{

	public function setCode()
	{
		$wo[] = 'stickyHeaders : \'ts-stickyHeader\'';
		$wo[] = 'resizable : true';
		$wo[] = 'columnSelector_mediaqueryHidden : true';
		//sort
		if (parent::$sorts) {
			//row grouping
			if (parent::$group) {
				$gc = ['$(table).find(\'.group-header\').addClass(\'info\');'];
				$wo[] = $this->iterate(
					$gc,
					'group_callback : function($cell, $rows, column, table){',
					$this->nt3 . '}',
					$this->nt4,
					''
				);
				$wo[] = 'group_collapsible : true';
				if (parent::$ajax) {
					$wo[] = 'group_count : false';
				} else {
					$wo[] = 'group_count : \' ({num})\'';
				}
			}
			//saveSort
			if (isset(parent::$s['sorts']['type']) && strpos(parent::$s['sorts']['type'], 'save') !== false) {
				$wo[] = 'saveSort : true';
			}
		}

		//now incorporate options which are handled in child classes
		$classes = ['Filter', 'Pager', 'Math'];
		foreach ($classes as $option) {
			$optarray = Table_Factory::build('WidgetOptions' . $option, parent::$s, 'code')->getOptionArray();
			$wo = $optarray === false ? $wo : array_merge($wo, $optarray);
		}

		if (count($wo) > 0) {
			$code = $this->iterate($wo, $this->nt2 . 'widgetOptions : {', $this->nt2 . '}', $this->nt3, '');
			parent::$code[self::$level1][self::$level2] = $code;
		}
	}

}