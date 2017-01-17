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

	function __construct()
	{
		$this->H5PTiki = new H5PTiki();

	}

	function __destruct()
	{
	}

	function handle_file_creation($args)
	{
		if ($metadata = $this->getRequestMetadata($args)) {

			// TODO create an H5P content object

		}
	}

	function handle_file_update($args)
	{
		if (isset($args['initialFileId']) && $metadata = $this->getRequestMetadata($args)) {

			// TODO the updating here

		}
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
		if (!isset($args['filetype'])) {
			return false;
		}

		return in_array($args['filetype'], array('application/zip', 'application/x-zip', 'application/x-zip-compressed'));
	}

	private function getZipFile($fileId)
	{
		global $prefs;

		if (!class_exists('ZipArchive')) {
			Feedback::error(tra('PHP Class "ZipArchive" not found'));
		}

		$filegallib = TikiLib::lib('filegal');

		if (!$info = $filegallib->get_file_info($fileId, false, true, false)) {
			return null;
		}


		/** @var ZipArchive $zip */
		$zip = new ZipArchive;

		if ($info['path'] && $prefs['fgal_use_db'] == 'n') {
			$filepath = $prefs['fgal_use_dir'] . $info['path'];
		} else {
			$filepath = tempnam('temp/', 'h5p');
			file_put_contents($filepath, $info['data']);
			$this->unlinkList[] = $filepath;
		}

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

}

