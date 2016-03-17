<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * TikiImporter
 *
 * This file has the main class for the TikiImporter.
 * The TikiImporter was started as a Google Summer of Code project and
 * aim to provide a generic structure for importing content from other
 * softwares to TikiWiki
 * See http://dev.tiki.org/gsoc2009rodrigo for more information
 *
 * @author Rodrigo Sampaio Primo <rodrigo@utopia.org.br>
 * @package tikiimporter
 */

/**
 * TikiImporter is a generic class that should be extended
 * by any importer class. Each importer class must implement
 * the methods validateInput(), parseData() and import()
 *
 */
class TikiImporter
{
	/**
	 * The name of the software to import from.
	 * Should be defined in child class
	 * @var string
	 */
	public $softwareName = '';

	/**
	 * During the importing process all the log
	 * strings will be appended to this object property
	 * using the method saveAndDisplayLog()
	 *
	 * @var string
	 */
	public $log = '';

	/**
	 * During the importing process all the error
	 * messagens will be appended to this property
	 * using the method saveAndDisplayLog()
	 *
	 * @var string
	 */
	public $errors = '';

	/**
	 * Options to the importer (i.e. the number of page
	 * revisions to import in the case of a wiki software)
	 *
	 * The function when implemented by child classes should return
	 * an array of options that is used in tiki-importer.tpl to display to the user
	 * the options related with the data import. Currently we support the following
	 * types: checkbox, select, text
	 *
	 * Example of array:
	 *
	 * $options = array(
	 * 		array('name' => 'importAttachments', 'type' => 'checkbox', 'label' => tra('Import images and other attachments')),
	 * );
	 *
	 * @return array
	 */
	static public function importOptions()
	{
		return array();
	}

	/**
	 * Abstract method to start the import process and
	 * call all other functions for each step of the importation
	 * (validateInput(), parseData(), insertData())
	 *
	 * @return array $importFeedback array with the number of pages imported etc
	 */
	function import($filePath = null)
	{

	}

	/**
	 * Abstract method to validate the input import data
	 *
	 * Must be implemented by classes
	 * that extends this one.
	 */
	function validateInput()
	{

	}

	/**
	 * Abstract method to parse the input import data
	 *
	 * Must be implemented by classes
	 * that extends this one and should return
	 * the data to be used by insertData.
	 */
	function parseData()
	{

	}

	/**
	 * Abstract method to insert the imported content
	 * into Tiki
	 *
	 * Must be implemented by classes
	 * that extends this one.
	 *
	 * @param array $parsedData data ready to be inserted into Tiki
	 */
	function insertData($parsedData = null)
	{

	}

	/**
	 * Abstract method to check the requirements for a
	 * specific importer.
	 *
	 * This method is optional and can be implemented by classes
	 * that extends this one.
	 *
	 * If an error is found the methods should raise an exception.
	 */
	function checkRequirements()
	{

	}

	/**
	 * Return a $importOptions array with the result of the concatenation of the $importOptions
	 * property of all classes in the hierarchy. Should be called by the classes that
	 * extend from this one, it doesn't make sense to call this method directly from this
	 * class.
	 *
	 * This method should be static but apparently only with PHP >= 5.3.0 is possible to get
	 * the name of the class the static method was called. For more information see
	 * http://us2.php.net/manual/en/function.get-called-class.php
	 *
	 * @return array $importOptions
	 */
	function getOptions()
	{
		$class = get_class($this);
		$importOptions = array();

		do {
			$importOptions = array_merge($importOptions, call_user_func(array($class, 'importOptions')));
		} while ($class = get_parent_class($class));

		return $importOptions;
	}

	/**
	 * Try to change some PHP settings to avoid problens while running the script:
	 *   - error_reporting
	 *   - display_errors
	 *   - max_execution_time
	 *
	 * @return void
	 */
	static function changePhpSettings()
	{
		if (ini_get('error_reporting') != E_ALL & ~E_DEPRECATED) {
			error_reporting(E_ALL & ~E_DEPRECATED);
		}

		if (ini_get('display_errors') != true) {
			ini_set('display_errors', true);
		}

		// change max_execution_time
		if (ini_get('max_execution_time') != 0) {
			set_time_limit(0);
		}
	}

	/**
	 * Handle the PHP $_FILES errors
	 *
	 * @param int $code error code
	 * @return string $message error message
	 */
	static function displayPhpUploadError($code)
	{
		require_once(dirname(__FILE__) . '/../init/tra.php');
		$errors = array(1 => tra('The uploaded file exceeds the upload_max_filesize directive in php.ini.') . ' ' . ini_get('upload_max_filesize') . 'B',
				2 => tra('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.'),
				3 => tra('The uploaded file was only partially uploaded. Please try again.'),
				4 => tra('No file was uploaded.'),
				6 => tra('Missing a temporary folder.'),
				7 => tra('Failed to write file to disk.'),
				8 => tra('File upload stopped by extension.'),
				);

		if (isset($errors[$code])) {
			return $errors[$code];
		}
	}

	/**
	 * Append $msg to $this->log and output the $msg to the browser
	 * during the execution of the script using the flush() method
	 *
	 * @param string $msg the log message
	 * @param bool $error if the message is a error or not (default false)
	 * @return void
	 */
	function saveAndDisplayLog($msg, $error = false)
	{
		$this->log .= $msg;

		if ($error) {
			$this->errors .= $msg;
		}

		// convert \n to <br> if running script in web browser
		if (php_sapi_name() != 'cli') {
			$msg = nl2br($msg);
			ob_flush();
		}

		echo $msg;

		flush();
	}
}

/**
 *
 */
class ImporterParserException extends Exception
{
}
