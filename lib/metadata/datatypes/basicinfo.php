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
 * Manipulates basic metadata extracted from a file
 */
class BasicInfo
{
	/**
	 * Label and suffix information for each field
	 *
	 * @var array
	 */
	var	$specs = array(
		'size' => array(
			'label' => 'File Size',
			'suffix' => 'bytes',
		),
		'type' => array(
			'label' => 'File Type',
		),
		'charset' => array(
			'label' => 'Character Set',
		),
		'devices' => array(
			'label' => 'Devices',
		),
	);

	/**
	 * Process raw basic metadata to ready for table presentations by adding labels and suffixes and expected fields
	 *
	 * @param 		array		$basicraw			Basic file metadata in a simple array
	 *
	 * @return 		array|bool	$basic				Processed metadata with expected fields used in later functions
	 */
	function processRawData($basicraw)
	{
		if (is_array($basicraw)) {
			foreach ($basicraw as $name => $field) {
				$basic[$name]['newval'] = $field;
				$basic[$name]['label'] = $this->specs[$name]['label'];
				if (isset($this->specs[$name]['suffix'])) {
					$basic[$name]['suffix'] = $this->specs[$name]['suffix'];
				}
			}
			return $basic;
		} else {
			return false;
		}
	}
}