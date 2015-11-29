<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Controller.php 53941 2015-02-15 09:52:55Z gezzzan $

class Services_Language_Controller
{
	private $utilities;

	function __construct()
	{
		$this->utilities = new Services_Language_Utilities;
	}

	function setUp()
	{
		global $prefs;

		if ($prefs['feature_multilingual'] != 'y') {
			throw new Services_Exception(tr('Feature Disabled'), 403);
		}
	}

	/**
	 * Download database translations into a language.php file
	 * @param $input
	 * @return language.php file
	 */
	function action_download_db_translations($input)
	{
		$lang = $input->language->text();

		//extract data from database
		$langTransLib = TikiLib::lib('languageTranslations');
		$data = $langTransLib->createCustomFile();
		
		//create file for download
		header("Content-type: application/unknown");
		header("Content-Disposition: inline; filename=language.php");
		header("Content-encoding: UTF-8");
		echo $data;
		exit (0);
	}

	/**
	 * Translations in the database will be merged with the other translations in language.php. Note that after writing translations to language.php they are removed from the database.
	 * @param $input (at least value for "language" is expected)
	 * @return language.php file
	 */
	function action_write_to_language_php($input)
	{
		$lang = $input->language->text();
		
		//TODO: move this to check if this action is available
		$langLib = TikiLib::lib('language');
		$db_languages = $langLib->getDbTranslatedLanguages();
		$db_languages = $langLib->format_language_list($db_languages);
		$confirm = $input->confirm->int();
		
		// check if is possible to write to lang/,TODO: throw exception
		// TODO: check if each language file is writable instead of the whole lang/ dir
		if (is_writable('lang/')) {
			$langIsWritable = true;
		} else {
			$langIsWritable = false;
		}
		
		// Write to language.php
		if (isset($_REQUEST['exportToLanguage']) && $tiki_p_admin == 'y') {
			try {
				$stats = $export_language->writeLanguageFile();
			} catch (Exception $e) {
				$smarty->assign('msg', $e->getMessage());
				$smarty->display('error.tpl');
				die;
			}

			$expmsg = sprintf(tra('Wrote %d new strings and updated %d to lang/%s/language.php'), $stats['new'], $stats['modif'], $export_language->lang);
			$smarty->assign('expmsg', $expmsg);
		}
	}
}

