<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Class H5P_H5PTiki
 *
 * Main wrapper class around the H5P library
 *
 */
class H5P_H5PTiki implements H5PFrameworkInterface
{

	// properties for table objects
	private $tiki_h5p_contents = null;
	private $tiki_h5p_contents_libraries = null;
	private $tiki_h5p_libraries = null;
	private $tiki_h5p_libraries_cachedassets = null;
	private $tiki_h5p_libraries_libraries = null;
	private $tiki_h5p_libraries_languages = null;
	private $tiki_h5p_results = null;

	public $isSaving = false;

	public static $h5p_path;

	function __construct()
	{
		// just as an example of how to get a table objects
		// docs here https://dev.tiki.org/Database+Access

		$tikiDb = TikiDb::get();

		$this->tiki_h5p_contents = $tikiDb->table('tiki_h5p_contents');
		$this->tiki_h5p_contents_libraries = $tikiDb->table('tiki_h5p_contents_libraries');
		$this->tiki_h5p_libraries = $tikiDb->table('tiki_h5p_libraries');
		$this->tiki_h5p_libraries_cachedassets = $tikiDb->table('tiki_h5p_libraries_cachedassets');
		$this->tiki_h5p_libraries_libraries = $tikiDb->table('tiki_h5p_libraries_libraries');
		$this->tiki_h5p_libraries_languages = $tikiDb->table('tiki_h5p_libraries_languages');
		$this->tiki_h5p_results = $tikiDb->table('tiki_h5p_results');
		// possibly others needed?

		self::$h5p_path = 'storage/public';

		if ($this->getOption('cron_last_run') < time() - 86400) {
			// Cron not run in >24h, trigger it

			// Determine full URL
			$cronUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}/" .
					TikiLib::lib('service')->getUrl(['controller' => 'h5p', 'action' => 'cron']);

			// Use token to prevent unauthorized use
			$token = $this->getOption('cron_token');
			if ($token === null) {
				// Create new token
				$token = uniqid();
				$this->setOption('cron_token', $token);
			}

			$this->fetchExternalData($cronUrl, ['token' => $token], false);
		}
	}

	/**
	 * Get the different instances of the core components.
	 *
	 * @param string $component
	 * @return \H5PCore|\H5PContentValidator|\H5PExport|\H5PStorage|\H5PValidator|\H5P_H5PTiki
	 */
	public static function get_h5p_instance($component)
	{
		static $interface, $core;
		global $prefs, $tikiroot, $tikipath;

		if (! function_exists('curl_init')) {
			throw new Exception(tr('H5P requires the CURL extension to be installed in PHP'));
		}

		if (is_null($interface)) {
			// Setup Core and Interface components that are always needed
			$interface = new \H5P_H5PTiki();

			$core = new \H5PCore(
				$interface,
				$tikipath . self::$h5p_path,   // Where the extracted content files will be stored
				$tikiroot . self::$h5p_path,     // URL of the previous option
				$prefs['language'],                  // TODO: Map proper language code from Tiki to H5P langs
				true                          // each time an h5p is saved it exports the reult into the file gallery to keep it up to date
			);

			// Will combine all JavaScript and all CSS files to reduce the total number of requests
			$core->aggregateAssets = true;
		}

		// Determine which component to return
		switch ($component) {
			case 'validator':
				return new \H5PValidator($interface, $core);
			case 'storage':
				return new \H5PStorage($interface, $core);
			case 'contentvalidator':
				return new \H5PContentValidator($interface, $core);
			case 'export':
				return new \H5PExport($interface, $core);
			case 'interface':
				return $interface;
			case 'core':
				return $core;
		}
	}

	/**
	 * Returns info for the current platform
	 *
	 * @return array
	 *   An associative array containing:
	 *   - name: The name of the platform, for instance "Wordpress"
	 *   - version: The version of the platform, for instance "4.0"
	 *   - h5pVersion: The version of the H5P plugin/module
	 */
	public function getPlatformInfo()
	{
		$TWV = new TWVersion();

		return [
			'name' => 'Tiki',
			'version' => $TWV->version,
			'h5pVersion' => '1.0.0', // TODO: Use variable? (\H5PLib not loaded)
		];
	}

	/**
	 * Fetches a file from a remote server using HTTP GET
	 *
	 * @param $url
	 * @param $data
	 * @return string The content (response body). null if something went wrong
	 */
	public function fetchExternalData($url, $data, $blocking = true)
	{
		$handle = curl_init($url);
		curl_setopt($handle, CURLOPT_POST, true);
		curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
		 curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);

		if (! $blocking) {
			curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 0.01);
		}

		$response = curl_exec($handle);
		curl_close($handle);

		if (! $response) {
			$error = curl_error($handle);
			// Print error?
		}

		if ($blocking) {
			return $response;
		}
	}

	/**
	 * Set the tutorial URL for a library. All versions of the library is set
	 *
	 * @param string $machineName
	 * @param string $tutorialUrl
	 */
	public function setLibraryTutorialUrl($machineName, $tutorialUrl)
	{
		$this->tiki_h5p_libraries->update(
			[
			'tutorial_url' => $tutorialUrl,
			],
			['name' => $machineName]
		);
	}

	/**
	 * Show the user an error message
	 *
	 * @param string $message
	 *   The error message
	 */
	public function setErrorMessage($message)
	{
		if (Perms::get()->h5p_edit) {
			// needs 'session' as the method param if the error happens asychronously
			Feedback::error(tra($message), 'session');
		}
	}

	/**
	 * Show the user an information message
	 *
	 * @param string $message
	 *  The error message
	 */
	public function setInfoMessage($message)
	{
		if (Perms::get()->h5p_edit) {
			Feedback::success(tra($message), 'session');
		}
	}

	/**
	 * Translation function
	 *
	 * @param string $message
	 *  The english string to be translated.
	 * @param array $replacements
	 *   An associative array of replacements to make after translation. Incidences
	 *   of any key in this array are replaced with the corresponding value. Based
	 *   on the first character of the key, the value is escaped and/or themed:
	 *    - !variable: inserted as is
	 *    - @variable: escape plain text to HTML
	 *    - %variable: escape text and theme as a placeholder for user-submitted
	 *      content
	 * @return string Translated string
	 * Translated string
	 */
	public function t($message, $replacements = [])
	{
		$args = [];
		$counter = 0;

		foreach ($replacements as $key => $val) {
			$args[] = $val;
			$message = str_replace($key, "%$counter", $message);
		}

		return tra($message, '', false, $args);
	}

	/**
	 * Get the Path to the last uploaded h5p
	 *
	 * @param string $setDir
	 *   Set the dir insted of using an auto generated one.
	 * @return string
	 *   Path to the folder where the last uploaded h5p for this session is located.
	 */
	public function getUploadedH5pFolderPath($setDir = null)
	{
		static $dir;

		if ($setDir !== null) {
			$dir = $setDir;
		}
		if (is_null($dir)) {
			$core = self::get_h5p_instance('core');
			$dir = $core->fs->getTmpPath();
		}

		return $dir;
	}

	/**
	 * Get the path to the last uploaded h5p file
	 *
	 * @param string $setPath
	 *   Set the path insted of using an auto generated one.
	 * @return string
	 *   Path to the last uploaded h5p
	 */
	public function getUploadedH5pPath($setPath = null)
	{
		static $path;

		if ($setPath !== null) {
			$path = $setPath;
		}
		if (is_null($path)) {
			$core = self::get_h5p_instance('core');
			$path = $core->fs->getTmpPath() . '.h5p';
		}

		return $path;
	}

	/**
	 * Get a list of the current installed libraries
	 *
	 * @return array
	 *   Associative array containing one entry per machine name.
	 *   For each machineName there is a list of libraries(with different versions)
	 */
	public function loadLibraries()
	{
		$res = $this->tiki_h5p_libraries->fetchAll(
			['id', 'name', 'title', 'major_version', 'minor_version', 'patch_version', 'runnable', 'restricted'],
			[],
			-1,
			0,
			['title' => 'ASC', 'major_version' => 'ASC', 'minor_version' => 'ASC']
		);

		$libraries = [];
		foreach ($res as $library) {
			$libraries[$library['name']][] = (object)$library;
		}

		return $libraries;
	}

	/**
	 * Returns the URL to the library admin page
	 *
	 * @return string
	 *   URL to admin page
	 */
	public function getAdminUrl()
	{
		// TODO: What is this for? This will be needed when the Library Managment page is implemented
		return TikiLib::tikiUrl('tiki-admin.php?page=h5p');
	}

	/**
	 * Get id to an existing library.
	 * If version number is not specified, the newest version will be returned.
	 *
	 * @param string $machineName
	 *   The librarys machine name
	 * @param int $majorVersion
	 *   Optional major version number for library
	 * @param int $minorVersion
	 *   Optional minor version number for library
	 * @return int
	 *   The id of the specified library or FALSE
	 */
	public function getLibraryId($machineName, $majorVersion = null, $minorVersion = null)
	{
		$conditions = [
			'name' => $machineName,
			'major_version' => $majorVersion,
			'minor_version' => $minorVersion,
		];

		$orderby = [];

		if ($majorVersion !== null) {
			$conditions['major_version'] = $majorVersion;
			$orderby[] = ['major_version' => 'desc'];
		}
		if ($minorVersion !== null) {
			$conditions['minor_version'] = $minorVersion;
			$orderby[] = ['minor_version' => 'desc'];
		}
		$orderby[] = ['patch_version' => 'desc'];

		return $this->tiki_h5p_libraries->fetchOne(
			'id',
			$conditions
		);
	}

	/**
	 * Get file extension whitelist
	 *
	 * The default extension list is part of h5p, but admins should be allowed to modify it
	 *
	 * @param boolean $isLibrary
	 *   TRUE if this is the whitelist for a library. FALSE if it is the whitelist
	 *   for the content folder we are getting
	 * @param string $defaultContentWhitelist
	 *   A string of file extensions separated by whitespace
	 * @param string $defaultLibraryWhitelist
	 *   A string of file extensions separated by whitespace
	 *
	 * @return string
	 */
	public function getWhitelist($isLibrary, $defaultContentWhitelist, $defaultLibraryWhitelist)
	{
		global $prefs;

		return $prefs['h5p_whitelist'] . ($isLibrary ? ' ' . $defaultLibraryWhitelist : '');
	}

	/**
	 * Is the library a patched version of an existing library?
	 *
	 * @param object $library
	 *   An associative array containing:
	 *   - machineName: The library machineName
	 *   - majorVersion: The librarys majorVersion
	 *   - minorVersion: The librarys minorVersion
	 *   - patchVersion: The librarys patchVersion
	 * @return boolean
	 *   TRUE if the library is a patched version of an existing library
	 *   FALSE otherwise
	 */
	public function isPatchedLibrary($library)
	{
		$operator = $this->isInDevMode() ? '<=' : '<';

		$result = $this->tiki_h5p_libraries->fetchCount([
			'name' => $library['machineName'],
			'major_version' => $library['majorVersion'],
			'minor_version' => $library['minorVersion'],
			'patch_version' => $this->tiki_h5p_libraries->expr("$$ $operator ?", [$library['patchVersion']]),
		]);

		return ! empty($result);
	}

	/**
	 * Is H5P in development mode?
	 *
	 * @return boolean
	 *  TRUE if H5P development mode is active
	 *  FALSE otherwise
	 */
	public function isInDevMode()
	{
		global $prefs;

		return $prefs['h5p_dev_mode'] === 'y';
	}

	/**
	 * Is the current user allowed to update libraries?
	 *
	 * @return boolean
	 *  TRUE if the user is allowed to update libraries
	 *  FALSE if the user is not allowed to update libraries
	 */
	public function mayUpdateLibraries()
	{
		return Perms::get()->h5p_admin;    // Do we need a separate perm for update? Or h5p_edit maybe?
	}

	/**
	 * Store data about a library
	 *
	 * Also fills in the libraryId in the libraryData object if the object is new
	 *
	 * @param array $libraryData
	 *   Associative array containing:
	 *   - libraryId: The id of the library if it is an existing library.
	 *   - title: The library's name
	 *   - machineName: The library machineName
	 *   - majorVersion: The library's majorVersion
	 *   - minorVersion: The library's minorVersion
	 *   - patchVersion: The library's patchVersion
	 *   - runnable: 1 if the library is a content type, 0 otherwise
	 *   - fullscreen(optional): 1 if the library supports fullscreen, 0 otherwise
	 *   - embedTypes(optional): list of supported embed types
	 *   - preloadedJs(optional): list of associative arrays containing:
	 *     - path: path to a js file relative to the library root folder
	 *   - preloadedCss(optional): list of associative arrays containing:
	 *     - path: path to css file relative to the library root folder
	 *   - dropLibraryCss(optional): list of associative arrays containing:
	 *     - machineName: machine name for the librarys that are to drop their css
	 *   - semantics(optional): Json describing the content structure for the library
	 *   - language(optional): associative array containing:
	 *     - languageCode: Translation in json format
	 * @param bool $new
	 */
	public function saveLibraryData(&$libraryData, $new = true)
	{

		$preloadedJs = $this->pathsToCsv($libraryData, 'preloadedJs');
		$preloadedCss = $this->pathsToCsv($libraryData, 'preloadedCss');
		$dropLibraryCss = '';

		if (isset($libraryData['dropLibraryCss'])) {
			$libs = [];
			foreach ($libraryData['dropLibraryCss'] as $lib) {
				$libs[] = $lib['machineName'];
			}
			$dropLibraryCss = implode(', ', $libs);
		}

		$embedTypes = '';
		if (isset($libraryData['embedTypes'])) {
			$embedTypes = implode(', ', $libraryData['embedTypes']);
		}
		if (! isset($libraryData['semantics'])) {
			$libraryData['semantics'] = '';
		}
		if (! isset($libraryData['fullscreen'])) {
			$libraryData['fullscreen'] = 0;
		}
		if ($new) {
			$libraryId = $this->tiki_h5p_libraries->insert([
				'name' => $libraryData['machineName'],
				'title' => $libraryData['title'],
				'major_version' => $libraryData['majorVersion'],
				'minor_version' => $libraryData['minorVersion'],
				'patch_version' => $libraryData['patchVersion'],
				'runnable' => $libraryData['runnable'],
				'fullscreen' => $libraryData['fullscreen'],
				'embed_types' => $embedTypes,
				'preloaded_js' => $preloadedJs,
				'preloaded_css' => $preloadedCss,
				'drop_library_css' => $dropLibraryCss,
				'semantics' => $libraryData['semantics'],
				'tutorial_url' => ''
			]);

			$libraryData['libraryId'] = $libraryId;
		} else {
			$this->tiki_h5p_libraries->update(
				[
				'title' => $libraryData['title'],
				'patch_version' => $libraryData['patchVersion'],
				'runnable' => $libraryData['runnable'],
				'fullscreen' => $libraryData['fullscreen'],
				'embed_types' => $embedTypes,
				'preloaded_js' => $preloadedJs,
				'preloaded_css' => $preloadedCss,
				'drop_library_css' => $dropLibraryCss,
				'semantics' => $libraryData['semantics'],
				],
				['id' => $libraryData['libraryId']]
			);

			$this->deleteLibraryDependencies($libraryData['libraryId']);
		}

		// Log library successfully installed/upgraded
		new H5P_Event(
			'library',
			($new ? 'create' : 'update'),
			null,
			null,
			$libraryData['machineName'],
			$libraryData['majorVersion'] . '.' . $libraryData['minorVersion']
		);

		$this->tiki_h5p_libraries_languages->deleteMultiple(['library_id' => $libraryData['libraryId']]);

		if (isset($libraryData['language'])) {
			foreach ($libraryData['language'] as $languageCode => $languageJson) {
				$id = $this->tiki_h5p_libraries_languages->insert([
					'library_id' => $libraryData['libraryId'],
					'language_code' => $languageCode,
					'translation' => $languageJson
				]);
			}
		}
	}

	/**
	 * Convert list of file paths to csv (from the WP implementation)
	 *
	 * @param array $libraryData
	 *  Library data as found in library.json files
	 * @param string $key
	 *  Key that should be found in $libraryData
	 * @return string
	 *  file paths separated by ', '
	 */
	private function pathsToCsv($libraryData, $key)
	{
		if (isset($libraryData[$key])) {
			$paths = [];
			foreach ($libraryData[$key] as $file) {
				$paths[] = $file['path'];
			}
			return implode(', ', $paths);
		}
		return '';
	}

	/**
	 * Insert new content.
	 *
	 * @param array $content
	 *   An associative array containing:
	 *   - id: The content id
	 *   - params: The content in json format
	 *   - library: An associative array containing:
	 *     - libraryId: The id of the main library for this content
	 * @param int $contentMainId
	 *   Main id for the content if this is a system that supports versions
	 *
	 * @return mixed
	 */
	public function insertContent($content, $contentMainId = null)
	{
		return $this->updateContent($content, $contentMainId);
	}

	/**
	 * Update old content.
	 *
	 * @param array $content
	 *   An associative array containing:
	 *   - id: The content id
	 *   - params: The content in json format
	 *   - library: An associative array containing:
	 *     - libraryId: The id of the main library for this content
	 * @param int $contentMainId
	 *   Main id for the content if this is a system that supports versions
	 *   ** In Tiki this is the fileId **
	 * @return int the content id
	 */
	public function updateContent($content, $contentMainId = null)
	{
		global $user;

		if (empty($content['title'])) {
			$title = TikiLib::lib('filegal')->get_file_label($contentMainId);
		} else {
			$title = $content['title'];
		}


		$data = [
			'updated_at' => date("Y-m-d H:i:s", TikiLib::lib('tiki')->now),
			'title' => $title,
			'parameters' => isset($content['params']) ? $content['params'] : '',
			'embed_type' => 'div', // TODO: Determine from library?
			'library_id' => $content['library']['libraryId'],
			'filtered' => '',
			'slug' => '',
			'disable' => isset($content['disable']) ? $content['disable'] : 0,
			'file_id' => $contentMainId,
		];

		if (! isset($content['id'])) {
			// Insert new content
			$data['created_at'] = $data['updated_at'];
			$data['user_id'] = TikiLib::lib('tiki')->get_user_id($user);

			$content['id'] = $this->tiki_h5p_contents->insert($data);
			$event_type = 'create';
		} else {
			// Update existing content
			$this->tiki_h5p_contents->update(
				$data,
				['id' => $content['id']]
			);
			$event_type = 'update';
		}

		// Log content create/update/upload
		if (! empty($content['uploaded'])) {
			$event_type .= ' upload';
		}
		new H5P_Event(
			'content',
			$event_type,
			$content['id'],
			$content['title'],
			$content['library']['machineName'],
			$content['library']['majorVersion'] . '.' . $content['library']['minorVersion']
		);

		return $content['id'];
	}

	/**
	 * Resets marked user data for the given content.
	 *
	 * @param int $contentId
	 */
	public function resetContentUserData($contentId)
	{
		// TODO: Implement resetContentUserData() method.
	}

	/**
	 * Save what libraries a library is depending on
	 *
	 * @param int $libraryId
	 *   Library Id for the library we're saving dependencies for
	 * @param array $dependencies
	 *   List of dependencies as associative arrays containing:
	 *   - machineName: The library machineName
	 *   - majorVersion: The library's majorVersion
	 *   - minorVersion: The library's minorVersion
	 * @param string $dependencyType
	 *   What type of dependency this is, the following values are allowed:
	 *   - editor
	 *   - preloaded
	 *   - dynamic
	 */
	public function saveLibraryDependencies($libraryId, $dependencies, $dependencyType)
	{
		foreach ($dependencies as $dependency) {
			$lh = $this->tiki_h5p_libraries->fetchOne(
				'id',
				[
					'name' => $dependency['machineName'],
					'major_version' => $dependency['majorVersion'],
					'minor_version' => $dependency['minorVersion'],
				]
			);

			$this->tiki_h5p_libraries_libraries->insert(
				[
					'library_id' => $libraryId,
					'required_library_id' => $lh,
					'dependency_type' => $dependencyType,
				]
			);
		}
	}

	/**
	 * Give an H5P the same library dependencies as a given H5P
	 *
	 * @param int $contentId
	 *   Id identifying the content
	 * @param int $copyFromId
	 *   Id identifying the content to be copied
	 * @param int $contentMainId
	 *   Main id for the content, typically used in frameworks
	 *   That supports versions. (In this case the content id will typically be
	 *   the version id, and the contentMainId will be the frameworks content id
	 */
	public function copyLibraryUsage($contentId, $copyFromId, $contentMainId = null)
	{
		$hcl = $this->tiki_h5p_contents_libraries->fetchRow(
			[
				'library_id',
				'dependency_type',
				'weight',
				'drop_css',
			],
			[
				'content_id' => $copyFromId,
			]
		);

		$this->tiki_h5p_contents_libraries->insert([
			'content_id' => $contentId,
			'library_id' => $hcl['library_id'],
			'dependency_type' => $hcl['dependency_type'],
			'weight' => $hcl['weight'],
			'drop_css' => $hcl['drop_css'],
		]);
	}

	/**
	 * Deletes content data
	 *
	 * @param int $contentId
	 *   Id identifying the content
	 */
	public function deleteContentData($contentId)
	{
		// Remove content data and library usage
		$this->tiki_h5p_contents->delete(['id' => $contentId]);
		$this->deleteLibraryUsage($contentId);

		// Remove results (really?)
		$this->tiki_h5p_results->delete(['content_id' => $contentId]);

		$tiki_user_preferences = TikiDb::get()->table('tiki_user_preferences');
		$tikilib = TikiLib::lib('tiki');

		// Remove contents user/usage data
		$users = $tiki_user_preferences->fetchColumn(
			'user',
			['prefName' => "h5p_content_$contentId"]
		);

		foreach ($users as $u) {
			$tikilib->set_user_preference($u, "h5p_content_$contentId", '');	// no delete userpref?
		}
		// tidy up
		$tiki_user_preferences->deleteMultiple(
			['prefName' => "h5p_content_$contentId"]
		);
	}

	/**
	 * Delete what libraries a content item is using
	 *
	 * @param int $contentId
	 *   Content Id of the content we'll be deleting library usage for
	 */
	public function deleteLibraryUsage($contentId)
	{
		$this->tiki_h5p_contents_libraries->deleteMultiple(['content_id' => $contentId]);
	}

	/**
	 * Saves what libraries the content uses
	 *
	 * @param int $contentId
	 *   Id identifying the content
	 * @param array $librariesInUse
	 *   List of libraries the content uses. Libraries consist of associative arrays with:
	 *   - library: Associative array containing:
	 *     - dropLibraryCss(optional): comma separated list of machineNames
	 *     - machineName: Machine name for the library
	 *     - libraryId: Id of the library
	 *   - type: The dependency type. Allowed values:
	 *     - editor
	 *     - dynamic
	 *     - preloaded
	 */
	public function saveLibraryUsage($contentId, $librariesInUse)
	{
		$dropLibraryCssList = [];
		foreach ($librariesInUse as $dependency) {
			if (! empty($dependency['library']['dropLibraryCss'])) {
				$dropLibraryCssList = array_merge($dropLibraryCssList, explode(', ', $dependency['library']['dropLibraryCss']));
			}
		}

		foreach ($librariesInUse as $dependency) {
			$dropCss = in_array($dependency['library']['machineName'], $dropLibraryCssList) ? 1 : 0;
			$this->tiki_h5p_contents_libraries->insert(
				[
					'content_id' => $contentId,
					'library_id' => $dependency['library']['id'],
					'dependency_type' => $dependency['type'],
					'drop_css' => $dropCss,
					'weight' => $dependency['weight'],
				]
			);
		}
	}

	/**
	 * Get number of content/nodes using a library, and the number of
	 * dependencies to other libraries
	 *
	 * @param int $libraryId
	 *   Library identifier
	 * @return array
	 *   Associative array containing:
	 *   - content: Number of content using the library
	 *   - libraries: Number of libraries depending on the library
	 */
	public function getLibraryUsage($libraryId, $skipContent = false)
	{
		$usage = [
			'libraries' => $this->tiki_h5p_libraries_libraries->fetchCount(['required_library_id' => $libraryId]),
		];

		if ($skipContent) {
			$usage['content'] = -1;
		} else {
			$usage['content'] = intval(TikiDb::get()->query(
				'SELECT COUNT(DISTINCT c.`id`)
FROM `tiki_h5p_libraries` l
JOIN `tiki_h5p_contents_libraries` cl ON l.`id` = cl.`library_id`
JOIN `tiki_h5p_contents` c ON cl.content_id = c.id
WHERE l.id = ?',
				$libraryId
			));
		}

		return $usage;
	}

	/**
	 * Loads a library
	 *
	 * @param string $machineName
	 *   The library's machine name
	 * @param int $majorVersion
	 *   The library's major version
	 * @param int $minorVersion
	 *   The library's minor version
	 * @return array|FALSE
	 *   FALSE if the library does not exist.
	 *   Otherwise an associative array containing:
	 *   - libraryId: The id of the library if it is an existing library.
	 *   - title: The library's name
	 *   - machineName: The library machineName
	 *   - majorVersion: The library's majorVersion
	 *   - minorVersion: The library's minorVersion
	 *   - patchVersion: The library's patchVersion
	 *   - runnable: 1 if the library is a content type, 0 otherwise
	 *   - fullscreen(optional): 1 if the library supports fullscreen, 0 otherwise
	 *   - embedTypes(optional): list of supported embed types
	 *   - preloadedJs(optional): comma separated string with js file paths
	 *   - preloadedCss(optional): comma separated sting with css file paths
	 *   - dropLibraryCss(optional): list of associative arrays containing:
	 *     - machineName: machine name for the librarys that are to drop their css
	 *   - semantics(optional): Json describing the content structure for the library
	 *   - preloadedDependencies(optional): list of associative arrays containing:
	 *     - machineName: Machine name for a library this library is depending on
	 *     - majorVersion: Major version for a library this library is depending on
	 *     - minorVersion: Minor for a library this library is depending on
	 *   - dynamicDependencies(optional): list of associative arrays containing:
	 *     - machineName: Machine name for a library this library is depending on
	 *     - majorVersion: Major version for a library this library is depending on
	 *     - minorVersion: Minor for a library this library is depending on
	 *   - editorDependencies(optional): list of associative arrays containing:
	 *     - machineName: Machine name for a library this library is depending on
	 *     - majorVersion: Major version for a library this library is depending on
	 *     - minorVersion: Minor for a library this library is depending on
	 */
	public function loadLibrary($machineName, $majorVersion, $minorVersion)
	{
		$library = $this->tiki_h5p_libraries->fetchRow(
			[
				'id',
				'name',
				'title',
				'major_version',
				'minor_version',
				'patch_version',
				'embed_types',
				'preloaded_js',
				'preloaded_css',
				'drop_library_css',
				'fullscreen',
				'runnable',
				'semantics',
				'tutorial_url',
			],
			[
				'name' => $machineName,
				'major_version' => $majorVersion,
				'minor_version' => $minorVersion,
			]
		);

		if ($library === false) {
			return false;
		}
		$library = H5PCore::snakeToCamel($library);
		$library['machineName'] = $library['name'];
		$library['libraryId'] = $library['id'];

		$result = TikiDb::get()->query(
			'SELECT hl.`name`, hl.`major_version` AS major, hl.`minor_version` AS minor, hll.`dependency_type` AS type
FROM `tiki_h5p_libraries_libraries` hll
JOIN `tiki_h5p_libraries` hl ON hll.`required_library_id` = hl.`id`
WHERE hll.`library_id` = ?',
			$library['id']
		);

		foreach ($result->result as $dependency) {
			$library[$dependency['type'] . 'Dependencies'][] = [
				'machineName' => $dependency['name'],
				'majorVersion' => $dependency['major'],
				'minorVersion' => $dependency['minor'],
			];
		}
		if ($this->isInDevMode()) {
			$semantics = $this->getSemanticsFromFile($library['machineName'], $library['majorVersion'], $library['minorVersion']);
			if ($semantics) {
				$library['semantics'] = $semantics;
			}
		}
		return $library;
	}

	private function getSemanticsFromFile($name, $majorVersion, $minorVersion)
	{
		$semanticsPath = self::$h5p_path . '/libraries/' . $name . '-' . $majorVersion . '.' . $minorVersion . '/semantics.json';

		if (file_exists($semanticsPath)) {
			$semantics = file_get_contents($semanticsPath);
			if (! json_decode($semantics, true)) {
				$this->setErrorMessage($this->t('Invalid json in semantics for %library', ['%library' => $name]));
			}
			return $semantics;
		}
		return false;
	}

	/**
	 * Loads library semantics.
	 *
	 * @param string $machineName
	 *   Machine name for the library
	 * @param int $majorVersion
	 *   The library's major version
	 * @param int $minorVersion
	 *   The library's minor version
	 * @return string
	 *   The library's semantics as json
	 */
	public function loadLibrarySemantics($machineName, $majorVersion, $minorVersion)
	{
		if ($this->isInDevMode()) {
			$semantics = $this->getSemanticsFromFile($machineName, $majorVersion, $minorVersion);
		} else {
			$semantics = $this->tiki_h5p_libraries->fetchOne(
				'semantics',
				[
					'name' => $machineName,
					'major_version' => $majorVersion,
					'minor_version' => $minorVersion,
				]
			);
		}
		return (empty($semantics) ? null : $semantics);
	}

	/**
	 * Makes it possible to alter the semantics, adding custom fields, etc.
	 *
	 * @param array $semantics
	 *   Associative array representing the semantics
	 * @param string $machineName
	 *   The library's machine name
	 * @param int $majorVersion
	 *   The library's major version
	 * @param int $minorVersion
	 *   The library's minor version
	 */
	public function alterLibrarySemantics(&$semantics, $machineName, $majorVersion, $minorVersion)
	{
		// TODO: Implement alterLibrarySemantics() method.
		// find an equivalent of do_action_ref_array or drupal_alter('h5p_semantics', $semantics, $name, $majorVersion, $minorVersion);

		// Not sure if this will be needed in Tiki.
		// I guess it would be implemented as firing a new event that functions may bind to in events.php.
	}

	/**
	 * Delete all dependencies belonging to given library
	 *
	 * @param int $libraryId
	 *   Library identifier
	 */
	public function deleteLibraryDependencies($libraryId)
	{
		$this->tiki_h5p_libraries_libraries->deleteMultiple(['library_id' => $libraryId]);
	}

	/**
	 * Start an atomic operation against the dependency storage
	 */
	public function lockDependencyStorage()
	{
		TikiDb::get()->query('LOCK TABLES `tiki_h5p_libraries_libraries` write, `tiki_h5p_libraries` as hl read');
	}

	/**
	 * Stops an atomic operation against the dependency storage
	 */
	public function unlockDependencyStorage()
	{
		TikiDb::get()->query('UNLOCK TABLES');
	}

	/**
	 * Delete a library from database and file system
	 *
	 * @param stdClass $library
	 *   Library object with id, name, major version and minor version.
	 */
	public function deleteLibrary($library)
	{
		/*		might be an int according to drupal
				 $library = $this->tiki_h5p_libraries->fetchRow(
					$this->tiki_h5p_libraries->all(),
					['library_id' => $libraryId]
				);*/

		// Delete files
		H5PCore::deleteFileTree(self::$h5p_path . '/libraries/' . $library->machine_name . '-' . $library->major_version . '.' . $library->minor_version);

		// Delete data in database (won't delete content)
		$this->tiki_h5p_libraries_libraries->deleteMultiple(['library_id', $library->id]);
		$this->tiki_h5p_libraries_languages->deleteMultiple(['library_id', $library->id]);
		$this->tiki_h5p_libraries->deleteMultiple(['id', $library->id]);
	}

	/**
	 * Load content.
	 *
	 * @param int $id
	 *   Content identifier
	 * @return array
	 *   Associative array containing:
	 *   - id: Identifier for the content
	 *   - file_id: Tiki specific fileId (of the original h5p file in the galleries)
	 *   - params: json content as string
	 *   - embedType: csv of embed types
	 *   - title: The contents title
	 *   - language: Language code for the content
	 *   - libraryId: Id for the main library
	 *   - libraryName: The library machine name
	 *   - libraryMajorVersion: The library's majorVersion
	 *   - libraryMinorVersion: The library's minorVersion
	 *   - libraryEmbedTypes: CSV of the main library's embed types
	 *   - libraryFullscreen: 1 if fullscreen is supported. 0 otherwise.
	 */
	public function loadContent($id)
	{
		$content = TikiDb::get()->query(
			'SELECT hc.`id`, hc.`file_id`, hc.`title`, hc.`parameters` AS params, hc.`filtered` , hc.`slug` AS slug, hc.`user_id`, hc.`embed_type` AS embedType,
	hc.disable, hl.id AS libraryId , hl.name AS libraryName, hl.major_version AS libraryMajorVersion,
	hl.minor_version AS libraryMinorVersion, hl.embed_types AS libraryEmbedTypes, hl.fullscreen AS libraryFullscreen
FROM `tiki_h5p_contents` hc
JOIN `tiki_h5p_libraries` hl ON hl.id = hc.library_id
WHERE hc.id =?',
			$id
		);

		$row = $content->fetchRow();
		return $row;
	}

	/**
	 * Load dependencies for the given content of the given type.
	 *
	 * @param int $id
	 *   Content identifier
	 * @param int $type
	 *   Dependency types. Allowed values:
	 *   - editor
	 *   - preloaded
	 *   - dynamic
	 * @return array
	 *   List of associative arrays containing:
	 *   - libraryId: The id of the library if it is an existing library.
	 *   - machineName: The library machineName
	 *   - majorVersion: The library's majorVersion
	 *   - minorVersion: The library's minorVersion
	 *   - patchVersion: The library's patchVersion
	 *   - preloadedJs(optional): comma separated string with js file paths
	 *   - preloadedCss(optional): comma separated sting with css file paths
	 *   - dropCss(optional): csv of machine names
	 */
	public function loadContentDependencies($id, $type = null)
	{
		$query = 'SELECT hl.`id`, hl.`name` AS machineName, hl.`major_version` AS majorVersion, hl.`minor_version` AS minorVersion,
hl.`patch_version` AS patchVersion, hl.`preloaded_css` AS preloadedCss, hl.`preloaded_js` AS preloadedJs,
hcl.`drop_css` AS dropCss, hcl.`dependency_type` AS dependencyType
      FROM `tiki_h5p_contents_libraries` hcl
      JOIN `tiki_h5p_libraries` hl ON hcl.`library_id` = hl.`id`
      WHERE hcl.content_id = ?';

		$queryArgs = [$id];

		if ($type !== null) {
			$query .= " AND hcl.`dependency_type` = ?";
			$queryArgs[] = $type;
		}

		$query .= ' ORDER BY hcl.`weight`';

		$result = TikiDb::get()->query($query, $queryArgs);
		return $result->result;
	}

	/**
	 * Get stored setting.
	 *
	 * @param string $name
	 *   Identifier for the setting
	 * @param string $default
	 *   Optional default value if settings is not set
	 * @return mixed
	 *   Whatever has been stored as the setting
	 */
	public function getOption($name, $default = null)
	{
		global $prefs;

		$prefName = 'h5p_' . $name;

		return isset($prefs[$prefName]) ? $prefs[$prefName] : $default;
	}

	/**
	 * Stores the given setting.
	 * For example when did we last check h5p.org for updates to our libraries.
	 *
	 * @param string $name
	 *   Identifier for the setting
	 * @param mixed $value Data
	 *   Whatever we want to store as the setting
	 */
	public function setOption($name, $value)
	{
		TikiLib::lib('tiki')->set_preference('h5p_' . $name, $value);
	}

	/**
	 * This will update selected fields on the given content.
	 *
	 * @param int $id Content identifier
	 * @param array $fields Content fields, e.g. filtered or slug.
	 */
	public function updateContentFields($id, $fields)
	{
		$processedFields = [];

		foreach ($fields as $name => $value) {
			$processedFields[self::camelToString($name)] = $value;
		}

		$this->tiki_h5p_contents->update(
			$processedFields,
			['id' => $id]
		);
	}

	/**
	 * Convert variables to fit our DB.
	 */
	private static function camelToString($input)
	{
		$input = preg_replace('/[a-z0-9]([A-Z])[a-z0-9]/', '_$1', $input);
		return strtolower($input);
	}

	/**
	 * Will clear filtered params for all the content that uses the specified
	 * library. This means that the content dependencies will have to be rebuilt,
	 * and the parameters re-filtered.
	 *
	 * @param int $library_id
	 */
	public function clearFilteredParameters($library_id)
	{
		$this->tiki_h5p_contents->update(
			['filtered' => null],
			['library_id' => $library_id]
		);
	}

	/**
	 * Get number of contents that has to get their content dependencies rebuilt
	 * and parameters re-filtered.
	 *
	 * @return int
	 */
	public function getNumNotFiltered()
	{
		return $this->tiki_h5p_contents->fetchCount(['filtered' => '']);
	}

	/**
	 * Get number of contents using library as main library.
	 *
	 * @param int $libraryId
	 * @return int
	 */
	public function getNumContent($libraryId)
	{
		return $this->tiki_h5p_contents->fetchCount(['library_id' => $libraryId]);
	}

	/**
	 * Determines if content slug is used.
	 *
	 * @param string $slug
	 * @return boolean
	 */
	public function isContentSlugAvailable($slug)
	{
		return empty($this->tiki_h5p_contents->fetchOne('slug', ['slug' => $slug]));
	}

	/**
	 * Generates statistics from the event log per library
	 *
	 * @param string $type Type of event to generate stats for
	 * @return array Number values indexed by library name and version
	 */
	public function getLibraryStats($type)
	{
		return [];
		/*
		$count = [];

		$tiki_h5p_counters = TikiDb::get()->table('tiki_h5p_counters');

		$results = $tiki_h5p_counters->fetchAll(
			['library_name', 'library_version', 'num'],
			['type' => $type]
		);

		// Extract results
		foreach ($results as $library) {
			$count[$library['library_name'] . ' ' . $library['library_version']] = $library['num'];
		}

		return $count;
		*/
	}

	/**
	 * Aggregate the current number of H5P authors
	 * @return int
	 */
	public function getNumAuthors()
	{
		return $this->tiki_h5p_contents->fetchOne(
			$this->tiki_h5p_contents->expr('COUNT(DISTINCT `user_id`)'),
			[]
		);
	}

	/**
	 * Stores hash keys for cached assets, aggregated JavaScripts and
	 * stylesheets, and connects it to libraries so that we know which cache file
	 * to delete when a library is updated.
	 *
	 * @param string $key
	 *  Hash key for the given libraries
	 * @param array $libraries
	 *  List of dependencies(libraries) used to create the key
	 */
	public function saveCachedAssets($key, $libraries)
	{
		foreach ($libraries as $library) {
			$libraryId = isset($library['id']) ? $library['id'] : $library['libraryId'];

			if (! $this->tiki_h5p_libraries_cachedassets->fetchCount(['library_id' => $libraryId])) {
				$this->tiki_h5p_libraries_cachedassets->insert(
					[
						'library_id' => $libraryId,
						'hash' => $key,
					]
				);
			} else {
				$this->tiki_h5p_libraries_cachedassets->update(
					['hash' => $key],
					['library_id' => $libraryId]
				);
			}
		}
	}

	/**
	 * Locate hash keys for given library and delete them.
	 * Used when cache file are deleted.
	 *
	 * @param int $library_id
	 *  Library identifier
	 * @return array
	 *  List of hash keys removed
	 */
	public function deleteCachedAssets($library_id)
	{
		// Get all the keys so we can remove the files
		$results = $this->tiki_h5p_libraries_cachedassets->fetchAll(
			['hash'],
			['library_id' => $library_id]
		);

		// Remove all invalid keys
		$hashes = [];
		foreach ($results as $row) {
			$hashes[] = $row['hash'];

			$this->tiki_h5p_libraries_cachedassets->deleteMultiple(
				['hash' => $row['hash']]
			);
		}

		return $hashes;
	}

	/**
	 * Get the amount of content items associated to a library
	 * return int
	 */
	public function getLibraryContentCount()
	{
		$count = [];

		// Find number of content per library
		$results = TikiDb::get()->query('
SELECT l.`name`, l.`major_version`, l.`minor_version`, COUNT(*) AS count
FROM `tiki_h5p_contents` c, `tiki_h5p_libraries` l
WHERE c.`library_id` = l.`id`
GROUP BY l.`name`, l.`major_version`, l.`minor_version`');

		// Extract results
		foreach ($results->result as $library) {
			$count[$library['name'] . ' ' . $library['major_version'] . '.' . $library['minor_version']] = $library['count'];
		}
		return $count;
	}

	/**
	 * Will trigger after the export file is created.
	 */
	public function afterExportCreated($content, $filename)
	{
		global $prefs, $user;

		$exportedFile = H5P_H5PTiki::$h5p_path . '/exports/' . $filename;
		if (! file_exists($exportedFile)) {
			Feedback::error(tr('Exporting H5P content %0 failed', $content['id']), 'session');
		}

		$filegallib = TikiLib::lib('filegal');
		$info = $filegallib->get_file($content['file_id']);
		$this->isSaving = true;
		$result = $filegallib->insert_file(
			$prefs['h5p_filegal_id'],
			$content['title'],
			tr('Created by H5P'),
			TikiLib::remove_non_word_characters_and_accents($content['title']) . '.h5p',
			file_get_contents($exportedFile),
			filesize($exportedFile),
			'application/zip',
			$user,
			$exportedFile,
			'',
			$user,
			$info['created'],
			null,
			null,
			$content['file_id']
		);
		$this->isSaving = false;

		if (! $result) {
			Feedback::error(tr('Saving H5P content %0 (fileId %1) failed', $content['id'], $content['file_id']), 'session');
		}
	}

	/**
	 * Check if user has permissions to an action
	 *
	 * @method hasPermission
	 * @param  [H5PPermission] $permission Permission type, ref H5PPermission
	 * @param  [int]           $id         Id need by platform to determine permission
	 * @return boolean
	 */
	public function hasPermission($permission, $id = null)
	{
		// TODO: Implement hasPermission() method.
	}
}
