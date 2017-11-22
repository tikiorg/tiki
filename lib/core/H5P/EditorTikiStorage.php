<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

/**
 * Class H5P_EditorTikiStorage
 *
 * Implementation of the H5P Edtior Storage Interface
 */
class H5P_EditorTikiStorage implements H5peditorStorage
{

	/**
	 * Will created and return the same instance of the H5peditor each time.
	 *
	 * @return \H5peditor
	 */
	public static function get_h5peditor_instance()
	{

		static $h5peditor;

		$ajaxInterface = new H5P_EditorTikiAjax();

		if (empty($h5peditor)) {
			$h5peditor = new H5peditor(
				H5P_H5PTiki::get_h5p_instance('core'),
				new H5P_EditorTikiStorage(),
				$ajaxInterface
			);
		}

		return $h5peditor;
	}

	/**
	 * Load language file(JSON) from database.
	 * This is then used to translate the editor widget field properties, e.g.
	 * title, description etc.
	 *
	 * @param string $name The machine readable name of the library(Content Type)
	 * @param int $major Major part of the version number
	 * @param int $minor Minor part of the version number
	 * @param string $lang Language code
	 * @return string Translation in JSON format
	 */
	public function getLanguage($name, $majorVersion, $minorVersion, $language)
	{

		// Load translation field from DB
		$translation = TikiDb::get()->query(
			'SELECT hlt.`translation`
FROM `tiki_h5p_libraries_languages` hlt
JOIN `tiki_h5p_libraries` hl ON hl.`id` = hlt.`library_id`
WHERE hl.`name` = ?
AND hl.`major_version` = ?
AND hl.`minor_version` = ?
AND hlt.`language_code` = ?',
			[$name, $majorVersion, $minorVersion, $language]
		);

		return empty($translation->result) ? false : $translation->result[0]->translation;
	}

	/**
	 * "Callback" for mark the given file as a permanent file.
	 * Used when saving content that has new uploaded files.
	 *
	 * @param int $fileid
	 */
	public function keepFile($fileId)
	{
		TikiDb::get()->query('DELETE FROM `tiki_h5p_tmpfiles` WHERE `path` = ?', $fileId);
	}

	/**
	 * Decides which content types the editor should have.
	 *
	 * Two usecases:
	 * 1. No input, will list all the available content types.
	 * 2. Libraries supported are specified, load additional data and verify
	 * that the content types are available. Used by e.g. the Presentation Tool
	 * Editor that already knows which content types are supported in its
	 * slides.
	 *
	 * @param array $libraries List of library names + version to load info for
	 * @return array List of all libraries loaded
	 */
	public function getLibraries($libraries = null)
	{
		$can_use_all = Perms::get()->h5p_admin;

		if ($libraries !== null) {
			// Get details for the specified libraries only.
			$librariesWithDetails = [];
			foreach ($libraries as $library) {
				// Look for library
				$details = TikiDb::get()->query(
					'SELECT `title`, `runnable`, `restricted`, `tutorial_url`
FROM `tiki_h5p_libraries`
WHERE `name` = ?
AND `major_version` = ?
AND `minor_version` = ?
AND `semantics` IS NOT NULL',
					[$library->name, $library->majorVersion, $library->minorVersion]
				)->fetchRow();
				if ($details) {
					// Library found, add details to list
					$library->tutorialUrl = $details['tutorial_url'];
					$library->title = $details['title'];
					$library->runnable = $details['runnable'];
					$library->restricted = $can_use_all ? false : ($details['restricted'] ? true : false);
					$librariesWithDetails[] = $library;
				}
			}

			// Done, return list with library details
			return $librariesWithDetails;
		}

		// Load all libraries
		$result = TikiDb::get()->query(
			'SELECT `name`, `title`, `major_version` AS majorVersion, `minor_version` AS minorVersion, `tutorial_url` AS tutorialUrl, `restricted`
FROM `tiki_h5p_libraries`
WHERE `runnable` = 1 AND `semantics` IS NOT NULL
ORDER BY `title`'
		);

		$libraries = [];
		foreach ($result->result as $library) {
			// Make sure we only display the newest version of a library.
			foreach ($libraries as $key => $existingLibrary) {
				if ($library['name'] === $existingLibrary->name) {
					// Found library with same name, check versions
					if (( $library['majorVersion'] === $existingLibrary->majorVersion &&
								 $library['minorVersion'] > $existingLibrary->minorVersion ) ||
							 ( $library['majorVersion'] > $existingLibrary->majorVersion ) ) {
						// This is a newer version
						$existingLibrary->isOld = true;
					} else {
						// This is an older version
						$library['isOld'] = true;
					}
				}
			}

			// Check to see if content type should be restricted
			$library['restricted'] = $can_use_all ? false : ($library['restricted'] ? true : false);

			// Add new library
			$libraries[] = (object)$library;
		}

		return $libraries;
	}

	/**
	 * Allow for other plugins to decide which styles and scripts are attached.
	 * This is useful for adding and/or modifing the functionality and look of
	 * the content types.
	 *
	 * @param array $files
	 *	List of files as objects with path and version as properties
	 * @param array $libraries
	 *	List of libraries indexed by machineName with objects as values. The objects
	 *	have majorVersion and minorVersion as properties.
	 */
	public function alterLibraryFiles(&$files, $libraries)
	{
		// Not really needed for Tiki
	}

	/**
	 * Saves a file or moves it temporarily. This is often necessary in order to
	 * validate and store uploaded or fetched H5Ps.
	 *
	 * @param string $data Uri of data that should be saved as a temporary file
	 * @param boolean $move_file Can be set to TRUE to move the data instead of saving it
	 *
	 * @return bool|object Returns false if saving failed or the path to the file
	 *  if saving succeeded
	 */
	public static function saveFileTemporarily($data, $move_file)
	{
		// TODO: Implement saveFileTemporarily() method.
	}

	/**
	 * Marks a file for later cleanup, useful when files are not instantly cleaned
	 * up. E.g. for files that are uploaded through the editor.
	 *
	 * @param H5peditorFile
	 * @param $content_id
	 */
	public static function markFileForCleanup($file, $content_id)
	{
		// TODO: Implement markFileForCleanup() method.
	}

	/**
	 * Clean up temporary files
	 *
	 * @param string $filePath Path to file or directory
	 */
	public static function removeTemporarilySavedFiles($filePath)
	{
		// TODO: Implement removeTemporarilySavedFiles() method.
	}
}
