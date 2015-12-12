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
		Services_Exception_Denied::checkAuth();
		Services_Exception_Disabled::check('feature_multilingual');

	}

	/**
	 * Download database translations into a custom.php file
	 * @param $input
	 * @return language.php file
	 */
	function action_download_db_translations($input)
	{
		//check preference
		Services_Exception_Disabled::check('lang_use_db');
		
		//check permissions
		$perms = Perms::get('tiki');
		if (! $perms->tiki_p_edit_languages) {
			throw new Services_Exception_Denied(tr('Permission denied'));
		}
		
		//get input
		$language = $input->language->text();
		$confirm = $input->confirm->int();

		//get languages
		$langLib = TikiLib::lib('language');
		$db_languages = $langLib->getDbTranslatedLanguages();
		$db_languages = $langLib->format_language_list($db_languages);
		
		if($confirm){
			//set export language
			$export_language = new LanguageTranslations($language);
			
			//get translation data from database
			$data = $export_language->createCustomFile();
			
			//create file for download
			header("Content-type: application/unknown");
			header("Content-Disposition: inline; filename=custom.php");
			header("Content-encoding: UTF-8");
			echo $data;
			die;
		}
		
		return array(
			'title' => tr('Download Translations'),
			'db_languages' => $db_languages,
		);
	}

	/**
	 * Translations in the database will be merged with the other translations in language.php. Note that after writing translations to language.php they are removed from the database.
	 * @param $input (at least value for "language" is expected)
	 * @return language.php file
	 */
	function action_write_to_language_php($input)
	{
		//check preference
		Services_Exception_Disabled::check('lang_use_db');
		
		//check permissions
		$perms = Perms::get('tiki');
		if (! $perms->tiki_p_edit_languages) {
			throw new Services_Exception_Denied(tr('Permission denied'));
		}
		
		//get input
		$language = $input->language->text();
		$confirm = $input->confirm->int();
				
		//prepare language list
		$langLib = TikiLib::lib('language');
		$db_languages = $langLib->getDbTranslatedLanguages();
		$db_languages = $langLib->format_language_list($db_languages);
		
		//TODO: check if each language file is writable instead of the whole lang/ dir
		if (is_writable('lang/')) {
			$langIsWritable = true;
		} else {
			$langIsWritable = false;
			throw new Services_Exception_Denied(tr('lang/ folder is not writable'));
		}
		
		if($confirm){
			//set export language
			$export_language = new LanguageTranslations($language);
			// Write to language.php
			try {
				$stats = $export_language->writeLanguageFile();
			} catch (Exception $e) {
				$smarty->assign('msg', $e->getMessage());
				$smarty->display('error.tpl');
				die;
			}
			$expmsg = sprintf(tra('Wrote %d new strings and updated %d to lang/%s/language.php'), $stats['new'], $stats['modif'], $export_language->lang);
			//$smarty->assign('expmsg', $expmsg);
		}

		return array(
			'title' => tr('Write to language.php'),
			'db_languages' => $db_languages,
			'langIsWritable' => $langIsWritable,
		);
	}
}

