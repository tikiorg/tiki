<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Other.php 53186 2014-11-23 23:16:30Z lindonb $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

/**
 * Class Table_Code_Bind
 *
 *Creates code for functions that will be bound to the end of the main tablesorter function
 *
 * @package Tiki
 * @subpackage Table
 * @uses Table_Code_Manager
 */
class Table_Code_Bind extends Table_Code_Manager
{

	public function setCode()
	{
		//make pager controls at bottom of table visible when number of rows is greater than 15
		$bind = [
			'if (c.pager.endRow - c.pager.startRow > 15) {',
			'	$(\'div.' . parent::$s['pager']['controls']['id']
				. '.ts-pager-bottom\').css(\'visibility\', \'visible\');',
			'} else {',
			'	$(\'div.' . parent::$s['pager']['controls']['id']
				. '.ts-pager-bottom\').css(\'visibility\', \'hidden\');',
			'}',
		];

		$jq[] = $this->iterate(
			$bind,
			'.bind(\'pagerComplete\', function(e, c){',
			$this->nt . '})',
			$this->nt2,
			'',
			'');

		if (count($jq) > 0) {
			$code = $this->iterate($jq, '', ';', $this->nt, '', '');
			parent::$code[self::$level1] = $code;
		}
	}
}
