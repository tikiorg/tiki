<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 *
 */
class MimeLib
{
	private $finfo;

    /**
     * @param $filename
     * @param $path
     * @return string
     */
    function from_path($filename, $path)
	{
		if ($type = $this->physical_check_from_path($path)) {
			return $this->handle_physical_exceptions($type, $filename);
		}

		return $this->from_file_extension($filename);
	}

    /**
     * @param $filename
     * @param $content
     * @return string
     */
    function from_content($filename, $content)
	{
		if ($type = $this->physical_check_from_content($content)) {
			return $this->handle_physical_exceptions($type, $filename, $content);
		}

		return $this->from_file_extension($filename);
	}

    /**
     * @param $filename
     * @return string
     */
    function from_filename($filename)
	{
		return $this->from_file_extension($filename);
	}

    /**
     * @param $type
     * @param $filename
     * @param $content
     * @return string
     */
    private function handle_physical_exceptions($type, $filename, $content = '')
	{
		global $prefs;

		if ($type === 'application/zip' || $type === 'application/octet-stream' || $type === 'application/vnd.ms-office') {
			return $this->from_file_extension($filename);
		} else if ($type === 'text/html' && $this->get_extension($filename) == 'svg') {
			$type = 'image/svg+xml';
		} else if ($type === 'text/html' && $prefs['vimeo_upload'] === 'y' && is_numeric($filename)) {
			if (strpos($content, 'vimeo.com') !== false) {
				$type = 'video/vimeo';
			}
		} else {
			$extension = $this->get_extension($filename);

			if (in_array($extension, array("xlsx", "xltx", "potx", "ppsx", "pptx", "sldx", "docx", "dotx", "xlam", "xlsb"))) {
				return $this->from_file_extension($filename);
			}
		}

		return $type;
	}

    /**
     * @param $filename
     * @return string
     */
    private function get_extension($filename)
	{
		$ext = pathinfo($filename);
		return isset($ext['extension']) ? $ext['extension'] : '';
	}

    /**
     * @param $filename
     * @return string
     */
    private function from_file_extension($filename)
	{
		global $mimetypes; include_once('lib/mime/mimetypes.php');

		if (isset($mimetypes)) {
			$ext = $this->get_extension($filename);
			$mimetype = isset($mimetypes[$ext]) ? $mimetypes[$ext] : '';

			if (!empty($mimetype)) {
				return $mimetype;
			}
		}

        return "application/octet-stream";
	}

    /**
     * @param $path
     * @return string
     */
    private function physical_check_from_path($path)
	{
		if ($finfo = $this->get_finfo()) {
			if (file_exists($path)) {
				$type = $finfo->file($path);
				return $type;
			}
		}
	}

    /**
     * @param $content
     * @return string
     */
    private function physical_check_from_content($content)
	{
		if ($finfo = $this->get_finfo()) {
			$type = $finfo->buffer($content);
			return $type;
		}
	}

    /**
     * @return finfo
     */
    private function get_finfo()
	{
		global $prefs;

		if ($this->finfo) {
			return $this->finfo;
		}

		if ($prefs['tiki_check_file_content'] == 'y' && class_exists('finfo')) {
			if ($finfo = new finfo(FILEINFO_MIME_TYPE)) {
				$this->finfo = $finfo;
				return $finfo;
			}
		}
	}
}

