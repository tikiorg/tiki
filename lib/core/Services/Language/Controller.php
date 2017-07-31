<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

include(dirname(__FILE__) . '/TranslationReader.php');

class Services_Language_Controller
{
	private $utilities;

	function __construct()
	{
		$this->utilities = new Services_Language_Utilities;
		$this->transifex = new Services_Language_TransifexController;
	}

	function setUp()
	{
		Services_Exception_Denied::checkAuth();
		Services_Exception_Disabled::check('feature_multilingual');
	}
	
	/**
	 * Download database translations into a php file
	 *
	 * @param $input
	 *
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
		
		if($language){
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
		else {
			throw new Services_Exception_Denied(tr('No language provided'));
		}
	}
	
	/**
	 * Translations in the database will be merged with the other translations in language.php. Note that after writing translations to language.php they are removed from the database.
	 *
	 * @param $input
	 *
	 * @return array
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
		
		//prepare language list -> seems useless...
		$langLib = TikiLib::lib('language');
		$db_languages = $langLib->getDbTranslatedLanguages();
		$db_languages = $langLib->format_language_list($db_languages);
		
		//get count of available translations in the database
		$db_translation_count = $this->utilities->getDbTranslationCount($language);
		
		//check if lang directory is writable for the selected language
		$langIsWritable = $this->utilities->checkLangDirIsWritable($language);
		if ($langIsWritable === false){
			throw new Services_Exception_Denied(tr('lang/$language directory is not writable'));
		}
		
		//get the language file string so that it can be displayed
		$langDir = $this->utilities->getLanguageDirectory($language);
		$langFile = $langDir . 'language.php';
		
		$confirm = $input->confirm->int();
		if($confirm){
			//set export language
			$export_language = new LanguageTranslations($language);
			//write to language.php
			try {
				$stats = $export_language->writeLanguageFile();
			} catch (Exception $e) { //TODO: this is messy
				$smarty->assign('msg', $e->getMessage());
				$smarty->display('error.tpl');
				die;
			}
			//TODO: expose expmsg properly
			$expmsg = sprintf(tra('Wrote %d new strings and updated %d to lang/%s/language.php'), $stats['new'], $stats['modif'], $export_language->lang);
		}
		
		return array(
			'title' => tr('Write to language.php'),
			'language' => $language,
			'db_languages' => $db_languages,
			'db_translation_count' => $db_translation_count,
			'langIsWritable' => $langIsWritable,
			'langFile' => $langFile,
		);
	}
	
	/**
	 * Customized String Translation - create and edit custom translations
	 *
	 * @param JitFilter $input
	 *
	 * @return array
	 */
	function action_manage_custom_translations($input)
	{
		//check permissions
		$perms = Perms::get('tiki');
		if (! $perms->tiki_p_edit_languages) {
			throw new Services_Exception_Denied(tr('Permission denied'));
		}
		
		//get language
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
		
		//get list of languages
		$languages = $this->utilities->getLanguages();

		//get custom php file location
		$custom_php_file = $this->utilities->getLanguageDirectory($language);
		$custom_php_file .= 'custom.php';
		if(!file_exists($custom_php_file)){
			$custom_php_file = null;
		}

		$confirm = $input->confirm->int();
		if($confirm) {
			$language = $input->language->text();

			//get strings and translations
			$from = $input->asArray('from');
			$to = $input->asArray('to');

			if(count($from) > 1) {
				//prepare data
				foreach ($from as $fromKey => $source) {
					foreach ($to as $toKey => $translation) {
						if ($fromKey === $toKey) {
							$data[$source] = $translation;
						}
					}
				}

				//write custom php file content
				$this->utilities->writeCustomPhpTranslations($language, $data);

				//empty cache
				$cachelib = TikiLib::lib('cache');
				$cachelib->empty_cache();

				//TODO add success message

				//TODO refresh screen
			}
		}
		//get custom translation content
		$custom_translations = $this->utilities->getCustomPhpTranslations($language);

		//get count of custom translations
		$custom_translation_item_count = $this->utilities->getCustomPhpTranslationCount($language);

		//return
		return array(
			'title' => tr('Custom Translations'),
			'language' => $language,
			'languages' => $languages,
			'custom_file' => $custom_php_file,
			'custom_translations' => $custom_translations,
			'custom_translation_item_count' => $custom_translation_item_count,
		);
	}

	/**
	 * Download a language file
	 *
	 * @param $input
	 *
	 * @return file
	 */
	function action_download($input)
	{
		//get input
		$language = $input->language->text();
		$file_type = $input->file_type->text();
		
		//get language directory
		$file = $this->utilities->getLanguageDirectory($language);
		
		//add file name
		if($file_type === 'custom_php'){
			$file .= 'custom.php';
		}
		elseif($file_type === 'language_php'){
			$file .= 'language.php';
		}
		elseif($file_type === 'custom_json'){
			$file .= $this->utilities->getJsonCustomTranslationFileName();
			//check if an older version of custom.json exists and delete it
			if (file_exists($file)) {
				unlink($file);
				}
			//get custom translations from custom.php and convert it to json
			$custom_translations = json_encode($this->utilities->getCustomPhpTranslations($language), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
			//write it to the custom.json file
			$custom_json_file = fopen($file, 'w+');
			fwrite($custom_json_file, $custom_translations);
		}
		else {
			throw new Services_Exception_Denied(tr('Invalid file type'));
		}
		//get the file
		if (file_exists($file)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.basename($file).'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			readfile($file);
			//cleanup - remove the temporary json file
			if($file_type === 'custom_json') {
				unlink($file);
			}
			exit;
		}
		else {
			throw new Services_Exception_Denied(tr('File does not exist'));
		}
	}
	
	/**
	 * Upload a language file (Tiki language.php, Tiki custom.php or Transifex translation)
	 *
	 * @param $input
	 *
	 * @return int
	 */
	function action_upload($input)
	{
		//check permissions
		$perms = Perms::get('tiki');
		if (! $perms->tiki_p_edit_languages) {
			throw new Services_Exception_Denied(tr('Permission denied'));
		}
		
		//get list of languages
		$languages = $this->utilities->getLanguages();
		
		//get language
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

		//define upload types for the uploaded file
		$process_types = $this->utilities->getStringTranslationSetProcessTypes();

		//get $confirm from $input
		$confirm = $input->confirm->int();
		if($confirm){
			//check if lang directory is writable
			$this->utilities->checkLangDirIsWritable($language);

			//verify file type and deny php
			if( preg_match('/^\w+\.php$/', $_FILES['language_file']['name'])){
				throw new Services_Exception_Denied(tr('Invalid file type: php not allowed'));
			}

			//check if a custom.json already exist in temp/ directory and delete it
			if(file_exists('temp/custom.json')){
				unlink('temp/custom.json');
			}
			//move the uploaded file to /temp directory
			$tempFilePath = 'temp/' . $_FILES['language_file']['name'];
			move_uploaded_file($_FILES['language_file']['tmp_name'], $tempFilePath);

			//read the uploaded file
			$translationReader = new TranslationReader($tempFilePath);

			//decode it into an associative array
			$uploadCustomTranslations = $translationReader->getArray();

			//delete the uploaded file
			unlink($tempFilePath);
			unset($tempFilePath, $translationReader);

			//get process_type
			$process_type = $input->process_type->text();

			//get target string translation set
			$existingCustomPhpTranslations = $this->utilities->getCustomPhpTranslations($language);

			//Process uploaded translations
			$translationProcessResult = $this->utilities->processStringTranslationSets($uploadCustomTranslations, $existingCustomPhpTranslations, $process_type);

			//write the new custom.php file to the lang/$language folder
			$this->utilities->writeCustomPhpTranslations($language, $translationProcessResult);

			//empty cache
			$cachelib = TikiLib::lib('cache');
			$cachelib->empty_cache();

			//TODO: add a success message

			//TODO: refresh screen
			/*return array(
				'FORWARD' => array(
					'controller' => 'language',
					'action' => 'manage_custom_translations',
					'language' => $language,
				)
			);*/

		}

		return array(
			'title' => tr('Upload Translations'),
			'languages' => $languages,
			'language' => $language,
			'process_types' => $process_types,
		);
	}
}
