<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
		if (parent::$pager) {
			$bindtr = [
				//re-binding in some cases since ajax tbody refresh disconnects binding
				'$(\'' . parent::$tid . '\').tiki_popover();',
				'if (this.config.pager.endRow - this.config.pager.startRow > 15) {',
				'	$(\'div#' . parent::$s['pager']['controls']['id']
				. '.ts-pager-bottom\').css(\'display\', \'block\');',
				'} else {',
				'	$(\'div#' . parent::$s['pager']['controls']['id']
				. '.ts-pager-bottom\').css(\'display\', \'none\');',
				'}',
			];
		}
		//workaround since the processing formatting is not being applied upon sort (reported as bug #769)
		if (parent::$ajax) {
			$bindss = ['$(\'' . parent::$tid . ' tbody tr td\').css(\'opacity\', 0.25);'];
			$jq[] = $this->iterate($bindss, '.bind(\'sortStart\', function(e, c){', $this->nt . '})', $this->nt2, '', '');

			global $prefs;
			if ($prefs['jquery_timeago'] === 'y') {	// re-attach timeago for ajax calls
				$bindtr[] = '$(\'time.timeago\', \'' . parent::$tid . '\').timeago();';
			}
		}
		$bindtr[] = '$(\'div#' . parent::$id . '\').css(\'visibility\', \'visible\');';
		$jq[] = $this->iterate($bindtr, '.bind(\'tablesorter-ready\', function(){', $this->nt . '})', $this->nt2, '', '');

		if (count($jq) > 0) {
			$code = $this->iterate($jq, '', ';', $this->nt, '', '');
			parent::$code[self::$level1] = $code;
		}
	}
}
