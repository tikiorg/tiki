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
	 * Download database translations into a php file
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
			header("Content-Disposition: inline; filename=language.php");
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

		//get language from input
		$language = $input->language->text();
	
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

		$confirm = $input->confirm->int();		
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

	/**
	 * Customized String Translation - create and edit custom.php language file
	 * @param $input (at least value for "language" is expected)
	 * @return custom.php file
	 */
	function action_manage_custom_php_translations($input)
	{
		//check permissions
		$perms = Perms::get('tiki');
		if (! $perms->tiki_p_edit_languages) {
			throw new Services_Exception_Denied(tr('Permission denied'));
		}
		
		//get input
		if($input->language->text()){
			$language = $input->language->text();
		}
		elseif (isset($user) && isset($user_preferences[$user]['language'])) {
			$language = $user_preferences[$user]['language'];
		} 
		else {
			global $prefs;
			$language = $prefs['language'];
		}

		//get language name
		$languages = array();
		$langLib = TikiLib::lib('language');
		$language_details = $langLib->format_language_list(array('0' => $language), null, false);
		$language_name = $language_details[0]['name'];

		//get custom php file location and content
		$custom_php_location = $this->getCustomPhpLocation($language);
		$custom_php_translations = $this->getCustomPhpTranslations($language);

		//get count of custom translations
		$custom_translation_item_count = $this->getCustomTranslationItemCount($language);
		
		$confirm = $input->confirm->int();
		if($confirm){
			//get strings and translations
			$from = $input->from->array();
			$to = $input->to->array();

			//prepare data
			foreach($from as $fromKey => $source){
				foreach($to as $toKey => $translation){
					if($fromKey === $toKey){
						$data[$source] = $translation;
					}
				}
			}

			//write custom php file content
			$this->writeCustomPhpTranslations($language, $data);
			
			//refresh screen
			return array(
				'FORWARD' => array(
					'controller' => 'language',
					'action' => 'manage_custom_php_translations',
					'language' => $language,
				)
			);
		}
		
		return array(
			'title' => tr('Customized String Translation'),
			'language' => $language,
			'language_name' => $language_name,
			'custom_translations' => $custom_php_translations,
			'custom_php_location' => $custom_php_location,
			'custom_translation_item_count' => $custom_translation_item_count,
		);
	}

	/**
	 * Select a language for custom php file management
	 * @param 
	 * @return 
	 */
	function action_select_language($input)
	{	
		$confirm = $input->confirm->int();
		$language = $input->language->text();
		
		//get languages
		$languages = array();
		$langLib = TikiLib::lib('language');
		$languages = $langLib->list_languages(false, null, true);
		
		return array(
			'title' => tr('Select language'),
			'languages' => $languages,
			'custom_lang' => $language,
		);
	}
	
	/**
	 * Get custom php file location (if exists)
	 * @param $language
	 * @return $custom_php_location
	 */
	private function getCustomPhpLocation($language)
	{	
		$custom_file = 'lang/' . $language . '/';
		if (!empty($tikidomain)) {
			$custom_file .= "$tikidomain/";
		}

		$custom_file .= 'custom.php';

		if (file_exists($custom_file)) {
			return $custom_file;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Get translations from the custom.php file for a language
	 * @param $language
	 * @return array
	 */
	private function getCustomPhpTranslations($language)
	{	
		$custom_file = $this->getCustomPhpLocation($language);

		if (!empty($custom_file)) {
			$lang = array();
			include ($custom_file);
			return $lang_custom;
		}
		else {
			return null;
		}
	}
	
	/**
	 * Write translations to the custom.php file for a language TODO: error handling and error display
	 * @param $input
	 * @return custom.php file
	 */
	private function writeCustomPhpTranslations($language, $data)	
	{
		//prepare custom file path
		$custom_file = 'lang/' . $language . '/';
		if (!empty($tikidomain)) {
			$custom_file.= "$tikidomain/";
		}
		$custom_file.= 'custom.php';

		//prepare php file
		$custom_code = "<?php\r\n\$lang_custom = array(\r\n";
		
		//add translations
		foreach ($data as $from => $to) {
			if (!empty($from)) {
				$custom_code .= '"' . str_replace('"', '\\"', $from) . '" => "' . str_replace('"', '\\"', $to) . "\",\r\n";
			}
		}
		
		//finish php file
		$custom_code .= ");\r\n";
		$custom_code .= '$lang = $lang_custom + $lang;';
	
		//write the strings to custom.php file
		if (!($fp = fopen($custom_file, 'w+'))) {
			$ok = false;
			$smarty->assign('custom_error', 'file');
		} else {
			if (!fwrite($fp, $custom_code)) {
				$ok = false;
				$smarty->assign('custom_error', 'file');
			}
			fclose($fp);
			//empty cache
			$cachelib = TikiLib::lib('cache');
			$cachelib->empty_cache('templates_c');
		}
	}
	
	/**
	 * Count the items in the custom.php translation file for a language
	 * @param $language
	 * @return integer
	 */
	private function getCustomTranslationItemCount($language)	
	{
		$lang_array = $this->getCustomPhpTranslations($language);
		if(is_null($lang_array)){
			return 0;
		}
		else {
			$item_count = count($lang_array);
			return $item_count;
		}
	}
}
