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
	 * @param $input
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
	
		//prepare language list -> seems useless...
		$langLib = TikiLib::lib('language');
		$db_languages = $langLib->getDbTranslatedLanguages();
		$db_languages = $langLib->format_language_list($db_languages);
		
		//TODO: get count of available translations in the database
		//$db_translation_count = $this->getDbTranslationCount($language);
		
		//check if lang directory is writable for the selected language
		$langIsWritable = $this->checkLangIsWritable($language);
		if ($langIsWritable === false){
			throw new Services_Exception_Denied(tr('lang/$language directory is not writable'));
		}
		
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
			'db_languages' => $db_languages,
			'db_translation_count' => $db_translation_count,
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
		$custom_php_file = $this->getLangDir($language);
		$custom_php_file .= 'custom.php';
		if(!file_exists($custom_php_file)){
			$custom_php_file = null;
		}
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
			'custom_file' => $custom_php_file,
			'custom_translation_item_count' => $custom_translation_item_count,
		);
	}

	/**
	 * Select a language
	 * @param 
	 * @return 
	 */
	function action_select_language($input)
	{	
		$confirm = $input->confirm->int();
		$language = $input->language->text();
		
		//get languages
		$languages = $this->getLanguages();
		
		return array(
			'title' => tr('Select language'),
			'languages' => $languages,
			'language' => $language,
		);
	}
	
	/**
	 * Download a language file (language.php or custom.php)
	 * @param $language
	 * @return $success
	 */
	function action_download($input)	
	{
		//get input
		$language = $input->language->text();
		$file_type = $input->file_type->text();
		
		//get language directory
		$file = $this->getLangDir($language);
		
		//add file name
		if($file_type === 'custom_php'){
			$file .= 'custom.php';
		}
		elseif($file_type === 'language_php'){
			$file .= 'language.php';
		}
		else {
			throw new Services_Exception_Denied(tr('Invalid file type'));
		}
		
		//get the file
		if (file_exists($file))	{
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.basename($file).'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			readfile($file);
			exit;
		}
		else {
			throw new Services_Exception_Denied(tr('File does not exist'));
		}
	}
	
	/**
	 * Upload a language file (language.php or custom.php)
	 * @param $input
	 * @return integer
	 */
	function action_upload($input)	
	{
		//check permissions
		$perms = Perms::get('tiki');
		if (! $perms->tiki_p_edit_languages) {
			throw new Services_Exception_Denied(tr('Permission denied'));
		}

		//get list of languages
		$languages = $this->getLanguages();
		
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
	
		//language file type array
		$fileTypes = array(
			'tiki_custom_php' => 'Tiki custom.php',
			'tiki_language_php' => 'Tiki language.php',
			'transifex_language_php' => 'Transifex language.php',
		);

		$confirm = $input->confirm->int();
		if($confirm){
			//check if lang directory is writable
			$langIsWritable = $this->checkLangIsWritable($language);
			if ($langIsWritable === false){
				throw new Services_Exception_Denied(tr('lang/$language directory is not writable'));
			}
			
			//process file type types
			$fileType = $input->file_type->text();

			if($fileType === 'tiki_custom_php'){
				//verify php error _FILES error count
				if($_FILES['language_file']['error'] > 0){
					throw new Services_Exception_Denied(tr('There was an error during upload'));
				}
				//verify file name
				elseif($_FILES['language_file']['name'] !== 'custom.php'){
					throw new Services_Exception_Denied(tr('Invalid file name (expected file name: custom.php)'));
				}
				//verify file type
				elseif($_FILES['language_file']['type'] !== 'application/x-httpd-php'){
					throw new Services_Exception_Denied(tr('Invalid file type (expected file type: php)'));
				}
				//move the file to temp/ folder
				else {
					//check if a custom.php already exist in temp/ folder and delete it
					if(file_exists('temp/custom.php')){
						unlink('temp/custom.php');
					}					
					//move the file 
					move_uploaded_file($_FILES['language_file']['tmp_name'], 'temp/' . $_FILES['language_file']['name']);
				}
				
				//read lang_custom array from the just uploaded custom.php file
				include('temp/custom.php');
				if (isset($lang_custom) && is_array($lang_custom)){
					$uploadCustomPhpTranslations = $lang_custom;
				}
				else {
					throw new Services_Exception_Denied(tr('Invalid file content (not a standard Tiki custom.php file)'));
				}
				
				//delete the file
				unlink('temp/custom.php');
				
				//get existing custom translations
				if(!is_null($this->getCustomPhpTranslations($language))){
					$existingCustomPhpTranslations = $this->getCustomPhpTranslations($language);	
				}

				//merge uploaded into existing, this way existing translations are preserved in case translation exist in both files
				$data = array_merge($uploadCustomPhpTranslations, $existingCustomPhpTranslations);
	
				//write the new custom.php file to the lang/$language folder
				$this->writeCustomPhpTranslations($language, $data);
				
				//TODO: add a success message
				return array(
					'FORWARD' => array(
						'controller' => 'language',
						'action' => 'manage_custom_php_translations',
						'language' => $language,
					),
				);
			}
			elseif($fileType === 'tiki_language_php'){
				//$success = $this->uploadTikiLanguagePhp($fileData);
			}
			elseif($fileType === 'transifex_language_php'){
				//$success = $this->uploadTransifexLanguagePhp($fileData);
			}
			else {
				throw new Services_Exception_Denied(tr('Invalid file type'));
			}
			
			/*return array(
				'FORWARD' => array(
					'controller' => 'language',
					'action' => 'action_upload_language_php',
					//'language' => $language,
					//'langIsWritable' => $langIsWritable,
					//'langDirectory' => $langDirectory,
				),
			);*/
		}

		return array(
			'title' => tr('Upload Translations'),
			'languages' => $languages,
			'language' => $language,
			'fileTypes' => $fileTypes,
		);
	}	
	
	/**
	 * Get translations from the custom.php file for a language
	 * @param $language
	 * @return array
	 */
	private function getCustomPhpTranslations($language)
	{
		$custom_file = $this->getLangDir($language);
		$custom_file .= 'custom.php';
		
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
		$custom_file = $this->getLangDir($language);
		
		//add file name
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
	
	/**
	 * Get formatted list of languages
	 * @param none
	 * @return array $languages
	 */
	private function getLanguages($language = '')	
	{	
		$languages = array();
		$langLib = TikiLib::lib('language');
		$languages = $langLib->list_languages(false, null, true);
		return $languages;
	}

	/**
	 * Get language directory generally and for a specific language too
	 * @param language
	 * @return true/false
	 */
	private function getLangDir($language = '')	
	{	
		$langDir = "lang/";
		
		if(!empty($language)){
			$langDir .= "$language/";
		}
		
		global $tikidomain;
		if (!empty($tikidomain)) {
			$langDir .= "$tikidomain/";
		}
			
		return $langDir;
	}	

	/**
	 * Check if lang/ directory is writeable generally and for a specific language too
	 * @param language
	 * @return true/false
	 */
	private function checkLangIsWritable($language = '')	
	{	
		$directory = $this->getLangDir($language);
		
		if ($language) {
			if(is_writable($directory)) {
				$langIsWritable = true;
			}
			else {
				$langIsWritable = false;
			}
		}
		
		return $langIsWritable;
	}	
	
}
