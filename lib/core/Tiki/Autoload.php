<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Tiki internal autoload, to enable a cleaner autoload process.
 *
 * This is specially useful when depending on preferences to decide what files to load.
 */
class Tiki_Autoload
{
	/**
	 * Entry point to the autoload
	 *
	 * @param string $class the name of the class to be autoloaded
	 */
	static public function autoload($class)
	{
		switch ($class){
			case 'mPDF':
				self::loadMpdf($class);
			break;
		}
	}

	/**
	 * Handle mPDF autoload process based on tiki preferences
	 *
	 * @param string $class the name of the class to be autoloaded
	 */
	static protected function loadMpdf($class)
	{
		global $prefs;

		if ($class == 'mPDF'
			&& ! empty($prefs['print_pdf_from_url']) && $prefs['print_pdf_from_url'] === 'mpdf'
			&& ! empty($prefs['print_pdf_mpdf_path'])) {

			$path = $prefs['print_pdf_mpdf_path'];
			if (substr($prefs['print_pdf_mpdf_path'], -1) != '/'){
				$path .= '/';
			}
			$path .= 'mpdf.php';

			if (file_exists($path)) {
				include_once($path);
			}
		}
	}
}