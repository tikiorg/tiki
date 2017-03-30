<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

class H5PLib
{
	/**
	 * Lib version, used for cache-busting of style and script file references.
	 * Keeping track of the DB version.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const VERSION = '1.0.0';

	private $H5PTiki = null;

	private static $settings = null;

	function __construct()
	{
		$this->H5PTiki = \H5P_H5PTiki::get_h5p_instance('interface');
	}

	function __destruct()
	{
	}

	/**
	 * Triggered by the tiki.file.create event from filegallib
	 *
	 * @param array $args containing:
	 *  - 'type' => 'file'
	 *  - 'object' => $fileId
	 *  - 'user' => $GLOBALS['user']
	 *  - 'galleryId' => $galleryId
	 *  - 'filetype' => $type
	 *
	 */
	function handle_fileCreation($args)
	{
		if (!$this->H5PTiki->isSaving && $metadata = $this->getRequestMetadata($args)) {

			$validator = H5P_H5PTiki::get_h5p_instance('validator');

			if ($validator->isValidPackage()) {

				$storage = H5P_H5PTiki::get_h5p_instance('storage');
				$storage->savePackage(null, $args['object']);

			} else {

				// TODO: What to do if the file isn't a valid H5P? Seems a bit drastic to delete the file – but then again, why would we host broken files?
				// @unlink($interface->getUploadedH5pPath());

				Feedback::error($validator->get);
			}

		}
	}

	/**
	 * Triggered by the tiki.file.update event from filegallib
	 *
	 * @param array $args containing:
	 *  - 'type' => 'file'
	 *  - 'object' => $fileId
	 *  - 'user' => $GLOBALS['user']
	 *  - 'galleryId' => $galleryId
	 *  - 'filetype' => $type
	 *
	 */
	function handle_fileUpdate($args)
	{
		if (!$this->H5PTiki->isSaving && isset($args['object']) && $metadata = $this->getRequestMetadata($args)) {

			$content = $this->loadContentFromFileId($args['object']);

			// Clear content dependency cache
			$this->H5PTiki->deleteLibraryUsage($content['id']);

			$core = \H5P_H5PTiki::get_h5p_instance('core');
			$core->savePackage($content);// TODO: This doesn't exist…
		}
	}

	/**
	 * Triggered by the tiki.file.delete event from filegallib
	 *
	 * @param array $args containing:
	 *  - 'type' => 'file'
	 *  - 'object' => $fileId
	 *  - 'user' => $GLOBALS['user']
	 *  - 'galleryId' => $galleryId
	 *  - 'filetype' => $type
	 *
	 */
	function handle_fileDelete($args)
	{
		if (isset($args['object']) && $args['type'] === 'file') {

			$id = $this->getContentIdFromFileId($args['object']);

			if ($id) {
				// Remove the h5p contents
				$this->H5PTiki->deleteContentData($id);
			}
		}
	}

	/**
	 * Get H5P content row from the Tiki fileId
	 *
	 * @param int $fileId
	 *
	 * @return array|bool
	 */
	public function loadContentFromFileId($fileId)
	{
		global $prefs;

		$id = $this->getContentIdFromFileId($fileId);

		if ($id) {// Try to find content with $id.
			$core = \H5P_H5PTiki::get_h5p_instance('core');
			$content = $core->loadContent($id); // TODO: Is it possible to pass $fileId directly here to reduce the number of queries?

			if (is_array($content) && ! empty($content)) {
				// no error
				$content['language'] = substr($prefs['language'], 0, 2);    // TODO better



			}

			return $content;
		} else {
			return false;
		}
	}

	/**
	 * @param int $fileId
	 * @return bool|mixed
	 */
	public function getContentIdFromFileId($fileId)
	{
		$tiki_h5p_contents = TikiDb::get()->table('tiki_h5p_contents');

		return $tiki_h5p_contents->fetchOne('id', ['file_id' => $fileId]);
	}

	private function getRequestMetadata($args)
	{
		$metadata = null;

		if ($this->isZipFile($args) && $zip = $this->getZipFile($args['object'])) {

			if ($manifest = $this->getH5PManifest($zip)) {
				$metadata = $this->getMetadata($manifest);
			}

			$zip->close();
		}

		return $metadata;
	}

	private function isZipFile($args)
	{
		if (! isset($args['filetype'])) {
			return false;
		}

		return in_array($args['filetype'], array('application/zip', 'application/x-zip', 'application/x-zip-compressed'));
	}

	private function getZipFile($fileId)
	{
		global $prefs, $tikipath;

		if (! class_exists('ZipArchive')) {
			Feedback::error(tra('PHP Class "ZipArchive" not found'));
		}

		$filegallib = TikiLib::lib('filegal');

		if (! $info = $filegallib->get_file_info($fileId, false, true, false)) {
			return null;
		}

		// make a copy of the h5p file for the validator to unpack (and eventually delete)
		$dir = $filegallib->get_gallery_save_dir($info['galleryId']);

		$dest = $tikipath . 'temp/' . $info['filename'];
		if ($dir) {
			copy($dir . $info['path'], $dest);
		} else {
			file_put_contents($dest, $info['data']);
		}

		/** @var ZipArchive $zip */
		$zip = new ZipArchive;
		$interface = H5P_H5PTiki::get_h5p_instance('interface');

		$filepath = $interface->getUploadedH5pPath($dest);

		if ($zip->open($filepath) === true) {
			return $zip;
		}
	}

	/**
	 * @param ZipArchive $zip
	 * @return mixed
	 */
	private function getH5PManifest($zip)
	{
		return $zip->getFromName('h5p.json');
	}

	private function getMetadata($manifest)
	{

		return json_decode($manifest, false);
	}


	/**
	 * Include settings and assets for the given content.
	 *
	 * @param array $content
	 * @param boolean $no_cache
	 * @return string Embed code
	 */
	public function addAssets($content, $no_cache = false)
	{
		// Add core assets
		$this->addCoreAssets();

		// Detemine embed type
		$embed = H5PCore::determineEmbedType($content['embedType'], $content['library']['embedTypes']);

		// Make sure content isn't added twice
		$cid = 'cid-' . $content['id'];
		if (! isset(self::$settings['contents'][$cid])) {
			self::$settings['contents'][$cid] = $this->getContentSettings($content);
			$core = \H5P_H5PTiki::get_h5p_instance('core');

			// Get assets for this content
			$preloaded_dependencies = $core->loadContentDependencies($content['id'], 'preloaded');
			$files = $core->getDependenciesFiles($preloaded_dependencies);

			// TODO maybe?
			//$this->alter_assets($files, $preloaded_dependencies, $embed);

			if ($embed === 'div') {
				$this->enqueue_assets($files);
			} elseif ($embed === 'iframe') {
				self::$settings['contents'][$cid]['scripts'] = $core->getAssetsUrls($files['scripts']);
				self::$settings['contents'][$cid]['styles'] = $core->getAssetsUrls($files['styles']);
			}
		}

		// Tiki JB note: I had to add this here to get the js files to be included,
		// the WP plugin doesn't use this here so i must be missing something else...
		$this->printSettings(self::$settings);

		if ($embed === 'div') {
			return '<div class="h5p-content" data-content-id="' . $content['id'] . '"></div>';
		} else {
			return '<div class="h5p-iframe-wrapper"><iframe id="h5p-iframe-' . $content['id'] . '" class="h5p-iframe" data-content-id="' . $content['id'] . '" style="height:1px" src="about:blank" frameBorder="0" scrolling="no"></iframe></div>';
		}
	}

	/**
	 * Set core JavaScript settings and add core assets.
	 */
	public function addCoreAssets()
	{

		if (! empty(self::$settings)) {
			return; // Already added
		}

		self::$settings = $this->getCoreSettings();
		self::$settings['core'] = [
			'styles' => [],
			'scripts' => [],
		];
		self::$settings['loadedJs'] = [];
		self::$settings['loadedCss'] = [];
		$TWV = new TWVersion;
		$cache_buster = '?ver=' . $TWV->version;

		$lib_url = 'vendor_bundled/vendor/h5p/h5p-core/';

		// Add core stylesheets
		foreach (H5PCore::$styles as $style) {
			self::$settings['core']['styles'][] = $lib_url . $style . $cache_buster;
			TikiLib::lib('header')->add_cssfile($lib_url . $style . $cache_buster);
		}

		// Add core JavaScript
		foreach (H5PCore::$scripts as $script) {
			self::$settings['core']['scripts'][] = $lib_url . $script . $cache_buster;
			TikiLib::lib('header')->add_jsfile($lib_url . $script . $cache_buster);
		}
	}

	/**
	 * Get generic h5p settings
	 */
	public function getCoreSettings()
	{
		global $user, $base_url, $prefs;

		$userId = TikiLib::lib('tiki')->get_user_id($user);

		$settings = array(
			'baseUrl' => $base_url,
			'url' => $base_url . \H5P_H5PTiki::$h5p_path,
			'postUserStatistics' => ($prefs['h5p_track_user'] === 'y') && $userId,
			'ajax' => array(
				'setFinished' => 'tiki-ajax_services.php?controller=h5p&action=results',
				'contentUserData' => 'tiki-ajax_services.php?controller=h5p&action=userdata&contentId=:contentId&dataType=:dataType&subContentId=:subContentId',
			),
			'saveFreq' => $prefs['h5p_save_content_state'] === 'y' ? $prefs['h5p_save_content_frequency'] : false,
			'siteUrl' => $base_url,
			'l10n' => array(
				'H5P' => array(
					'fullscreen' => tra('Fullscreen'),
					'disableFullscreen' => tra('Disable fullscreen'),
					'download' => tra('Download'),
					'copyrights' => tra('Rights of use'),
					'embed' => tra('Embed'),
					'size' => tra('Size'),
					'showAdvanced' => tra('Show advanced'),
					'hideAdvanced' => tra('Hide advanced'),
					'advancedHelp' => tra('Include this script on your website if you want dynamic sizing of the embedded content:'),
					'copyrightInformation' => tra('Rights of use'),
					'close' => tra('Close'),
					'title' => tra('Title'),
					'author' => tra('Author'),
					'year' => tra('Year'),
					'source' => tra('Source'),
					'license' => tra('License'),
					'thumbnail' => tra('Thumbnail'),
					'noCopyrights' => tra('No copyright information available for this content.'),
					'downloadDescription' => tra('Download this content as a H5P file.'),
					'copyrightsDescription' => tra('View copyright information for this content.'),
					'embedDescription' => tra('View the embed code for this content.'),
					'h5pDescription' => tra('Visit H5P.org to check out more cool content.'),
					'contentChanged' => tra('This content has changed since you last used it.'),
					'startingOver' => tra("You'll be starting over."),
					'confirmDialogHeader' => tra('Confirm action'),
					'confirmDialogBody' => tra('Please confirm that you wish to proceed. This action is not reversible.'),
					'cancelLabel' => tra('Cancel'),
					'confirmLabel' => tra('Confirm')
				),
			),
		);

		if ($userId) {
			$settings['user'] = array(
				'name' => $user,
				//'mail' => $userId->user_email, // TODO: Used in xAPI statements to uniquely identify the user across systems, i.e. if an LRS is used.
			);
		}

		return $settings;
	}

	/**
	 * Enqueue assets for content embedded by div.
	 *
	 * @param array $assets
	 */
	public function enqueue_assets(&$assets)
	{
		$rel_url = \H5P_H5PTiki::$h5p_path;

		foreach ($assets['scripts'] as $script) {
			$url = $rel_url . $script->path . $script->version;
			if (! in_array($url, self::$settings['loadedJs'])) {
				self::$settings['loadedJs'][] = $url;
				TikiLib::lib('header')->add_jsfile( $rel_url . $script->path);
			}
		}
		foreach ($assets['styles'] as $style) {
			$url = $rel_url . $style->path . $style->version;
			if (! in_array($url, self::$settings['loadedCss'])) {
				self::$settings['loadedCss'][] = $url;
				TikiLib::lib('header')->add_cssfile( $rel_url . $style->path);
			}
		}
	}


	/**
	 * Add H5P JavaScript settings to the bottom of the page.
	 */
	public function addSettings()
	{
		if (self::$settings !== null) {
			$this->printSettings(self::$settings);
		}
	}

	/**
	 * JSON encode and print the given H5P JavaScript settings.
	 *
	 * @param array $settings
	 */
	public function printSettings(&$settings, $obj_name = 'H5PIntegration')
	{
		static $printed;
		if (! empty($printed[$obj_name])) {
			return; // Avoid re-printing settings
		}

		$json_settings = json_encode($settings);
		if ($json_settings !== false) {
			$printed[$obj_name] = true;
			TikiLib::lib('header')->add_js('var ' . $obj_name . ' = ' . $json_settings . ";\n");
		}
	}

	/**
	 * Get added JavaScript settings.
	 *
	 * @return array
	 */
	public function getSettings()
	{
		return self::$settings;
	}

	/**
	 * Get settings for given content
	 *
	 * @since 1.5.0
	 * @param array $content
	 * @return array
	 */
	public function getContentSettings($content)
	{
		global $prefs;

		$core = \H5P_H5PTiki::get_h5p_instance('core');

		// Add global disable settings - odd, not found?
		//$content['disable'] |= $core->getGlobalDisable();

		$safe_parameters = $core->filterParameters($content);
		/*		if (has_action('h5p_alter_filtered_parameters')) {
					// Parse the JSON parameters
					$decoded_parameters = json_decode($safe_parameters);

					/**
					 * Allows you to alter the H5P content parameters after they have been
					 * filtered. This hook only fires before view.
					 *
					 * @since 1.5.3
					 *
					 * @param object &$parameters
					 * @param string $libraryName
					 * @param int $libraryMajorVersion
					 * @param int $libraryMinorVersion
					 * /
					do_action_ref_array('h5p_alter_filtered_parameters', array(&$decoded_parameters, $content['library']['name'], $content['library']['majorVersion'], $content['library']['minorVersion']));

					// Stringify the JSON parameters
					$safe_parameters = json_encode($decoded_parameters);
				}
		*/

		$smarty = TikiLib::lib('smarty');
		$smarty->loadPlugin('smarty_function_service');
		$embedUrl = smarty_function_service([
			'controller' => 'h5p',
			'action' => 'embed',
			'fileId' => $content['file_id'],
		], $smarty);


		// Add JavaScript settings for this content
		$settings = [
			'library' => H5PCore::libraryToString($content['library']),
			'jsonContent' => $safe_parameters,
			'fullScreen' => $content['library']['fullscreen'],
			'exportUrl' => ($prefs['h5p_export'] === 'y' ? 'tiki-download_file.php?fileId=' . $content['file_id'] : ''),
			'embedCode' => '<iframe src="' . $embedUrl . '" width=":w" height=":h" frameborder="0" allowfullscreen="allowfullscreen"></iframe>',
			'resizeCode' => '<script src="vendor_bundled/vendor/h5p/h5p-core/js/h5p-resizer.js" charset="UTF-8"></script>',
			'url' => $embedUrl,
			'title' => $content['title'],
			'disable' => $content['disable'],
			'contentUserData' => [
				0 => [
					'state' => '{}',
				],
			],
			'displayOptions' => [],
		];

		// Get preloaded user data for the current user
		global $user;

		$userId = TikiLib::lib('tiki')->get_user_id($user);

		if ($prefs['h5p_save_content_state'] === 'y' && $userId) {

			$results = json_decode(TikiLib::lib('user')->get_user_preference($user, "h5p_content_{$content['id']}"), true);

			if (! empty($results['preload'])) {
					$settings['contentUserData'][$results['subContentId']][$results['dataType']] = json_encode($results['data']);
			}
		}

		return $settings;
	}

	/**
	 * Add assets and JavaScript settings for the editor.
	 *
	 * @param int $id optional content identifier
	 */
	public function addEditorAssets($id = NULL)
	{
		global $tikiroot, $tikipath, $prefs;

		// Add core assets
		$this->addCoreAssets();

		// Use jQuery and styles from core.
		$assets = array(
			'css' => self::$settings['core']['styles'],
			'js' => self::$settings['core']['scripts']
		);

		// Use relative URL to support both http and https.
		$editorpath = 'vendor_bundled/vendor/h5p/h5p-editor/';
		$url = $tikiroot . $editorpath;

		// Make sure files are reloaded for new versions
		$TWV = new TWVersion;
		$cachebuster = '?ver=' . $TWV->version;

		// Add editor styles
		foreach (H5peditor::$styles as $style) {
			$assets['css'][] = $url . $style . $cachebuster;
		}

		// Add editor JavaScript
		foreach (H5peditor::$scripts as $script) {
			// We do not want the creator of the iframe inside the iframe
			if ($script !== 'scripts/h5peditor-editor.js') {
				$assets['js'][] = $url . $script . $cachebuster;
			}
		}

		// Add JavaScript with library framework integration (editor part)
		TikiLib::lib('header')->add_jsfile($url . 'scripts/h5peditor-editor.js');
		TikiLib::lib('header')->add_jsfile($tikiroot . 'lib/core/H5P/editor.js');

		// Add translation
		$languagescript = $editorpath . 'language/' . substr($prefs['language'], 0, 2) . '.js';
		if (!file_exists($tikipath . $languagescript)) {
			$languagescript = $editorpath . 'language/en.js';
		}
		TikiLib::lib('header')->add_jsfile($tikiroot . $languagescript);

		// needs to be non-sefurl version so h5p can append the action and params
		$ajaxPath = 'tiki-ajax_services.php?controller=h5p&action=';

		// Add JavaScript settings
		$contentvalidator = \H5P_H5PTiki::get_h5p_instance('contentvalidator');
		self::$settings['editor'] = array(
			'filesPath' => $tikiroot . \H5P_H5PTiki::$h5p_path . '/editor',
			'fileIcon' => array(
				'path' => $url . 'images/binary-file.png',
				'width' => 50,
				'height' => 50,
			),
			'ajaxPath' => $ajaxPath,
			'libraryUrl' => $url,
			'copyrightSemantics' => $contentvalidator->getCopyrightSemantics(),
			'assets' => $assets,
		);

		if ($id !== NULL) {
			self::$settings['editor']['nodeVersionId'] = $id;
		}

		$this->printSettings(self::$settings);
	}

	/**
	 * Add assets and JavaScript settings for the editor.
	 * @param array $content    H5P content
	 * @param JitFilter $input
	 * @return bool|int
	 */
	public function saveContent(&$content, $input)
	{
		$core = \H5P_H5PTiki::get_h5p_instance('core');

		$oldLibrary = empty($content['library']) ? NULL : $content['library'];
		$oldParams = empty($content['params']) ? NULL : $content['params'];

		// Check title input
		$content['title'] = $input->title->text();
		if (empty($content['title'])) {
			$core->h5pF->setErrorMessage(tr('Missing title.'));
			return FALSE;
		}

		// Get content type chosen in editor
		$content['library'] = $core->libraryFromString($input->library->text());
		if (!$content['library']) {
			$core->h5pF->setErrorMessage(tr('Invalid content type.'));
			return FALSE;
		}

		// Check if content type exists
		$content['library']['libraryId'] = $core->h5pF->getLibraryId($content['library']['machineName'], $content['library']['majorVersion'], $content['library']['minorVersion']);
		if (!$content['library']['libraryId']) {
			$core->h5pF->setErrorMessage(tr("The chosen content type isn't installed."));
			return FALSE;
		}

		// Check parameters input
		$content['params'] = $input->parameters->xss();
		if (empty($content['params'])) {
			$core->h5pF->setErrorMessage(tr('Missing content parameters.'));
			return FALSE;
		}

		// Decode parameters input
		$params = json_decode($content['params']);
		if ($params === NULL) {
			$core->h5pF->setErrorMessage(tr('Invalid content parameters.'));
			return FALSE;
		}

		// Set disabled features
		// TODO: Implement

		// create the file gallery file to attach this to
		global $prefs, $user;

		$fileId = $input->fileId->int();
		if (! $fileId) {

			// Prevent extracting and inserting the file we're creating
			$this->H5PTiki->isSaving = true;
			$fileId = TikiLib::lib('filegal')->insert_file(
				$prefs['h5p_filegal_id'],
				$content['title'],
				tr('Created by H5P'),
				TikiLib::remove_non_word_characters_and_accents($content['title']) . '.h5p',
				'',
				0,
				'application/zip',
				$user,
				''
			);
			$this->H5PTiki->isSaving = false;
		}

		// Save new content
		$content['id'] = $core->saveContent($content, $fileId);

		// Move images to parmanent storage and find all required content dependencies
		$editor = \H5P_EditorTikiStorage::get_h5peditor_instance();
		$editor->processParameters($content['id'], $content['library'], $params, $oldLibrary, $oldParams);

		// export the project into the new file gallery file
		$content['file_id'] = $fileId;
		$core->filterParameters($content); // rebuild content

		return $fileId;
	}

	public function removeOldTmpFiles()
	{
		$older_than = time() - 86400;

	  // Locate files
		$result = TikiDb::get()->query(
				'SELECT tf.`path`
FROM `tiki_h5p_tmpfiles` tf
WHERE tf.`created_at` < ?',
				$older_than
		);

		// Delete files from file system
		foreach ($result->result as $file) {
			@unlink($file['path']);
		}

		// Remove from tmpfiles table
		TikiDb::get()->query(
				'DELETE FROM `tiki_h5p_tmpfiles`
WHERE `created_at` < ?',
				$older_than
		);
	}
}
