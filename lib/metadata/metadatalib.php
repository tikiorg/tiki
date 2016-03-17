<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

/**
 * Reads and manipulates metadata included within a file
 * For separate classes for specific file types are called from here
 */
class FileMetadata
{
	public $currname = null;		//working file path used to access file, may not be the same as embedded in file metadata
	public $content = null;			//file content string
	public $filesize = 0;			//file size in bytes
	public $basicraw = null;		//for basic file information
	public $basicinfo = null;		//processed basic file information
	public $typemeta = null;		//array used to store metadata beyond generic file data
	public $error = null;			//error messages stored here
	public $types = array (			//files types handled for extended metadata with values used for class and file name
		'image/jpeg' => 'jpeg',
		'image/jpg' => 'jpeg',
	);

	/**
	 * Get basic and extended metadata included in the file itself
	 *
	 * @param    string         $file              path to file or content of file
	 * @param    bool           $ispath            indicates whether $file is a path (true) or the file contents (false)
	 * @param    bool           $extended          indicates whether to retrieve extended metadata information
	 *
	 * @return \FileMetadata FileMetadata object with properties for the various types of metadata
	 */
	function getMetadata($file, $ispath = true, $extended = true)
	{
		if (empty($file)) {
			return false;
		//set contents and current name as well as type in some situations
		} elseif (!$ispath) {
			//when $file is actual file contents rather than a path - create a temporary file name
			// needed for some php functions
			$temppath = $this->temppathFromContent($file);
			$leavelink = false;
			if (!$temppath) {
				$this->error = 'The file is empty';
			} else {
				$this->filesize = @filesize($temppath);;
				$this->currname = $temppath;
			}
		} else {
			//when $file is a path
			if (is_readable($file)) {
				$this->currname = $file;
				$this->filesize = @filesize($file);
				$temppath = $file;
				$leavelink = true;
				if ($this->filesize <= 0) {
					$this->error = 'The file is empty';
				}
			//if not readable, see if it's an external file
			} elseif (strpos($file, 'http') !== false) {
				$filegallib = TikiLib::lib('filegal');
				$externalinfo = $filegallib->get_info_from_url($file);
				$temppath = $this->temppathFromContent($externalinfo['data']);
				$leavelink = false;
				if (!$temppath) {
					$this->error = 'The file is not readable';
				} else {
					$this->currname = $file;
					//go ahead and get content of external file here since this class is only called for external files
					//when getting extended metadata through the img plugin
					$this->content = $externalinfo['data'];
					$this->filesize = @filesize($file);
					//set type here for external files
					$this->type = $externalinfo['type'];
				}
			} else {
				$this->error = 'The file is not readable';
				$leavelink = true;
			}
		}

		//set basic info
		$this->basicraw['size'] = $this->filesize;
		if (class_exists('finfo') && is_readable($temppath)) {
			$finfo = new finfo(FILEINFO_MIME);
			$type_charset = $finfo->file($temppath);
			$type_charset = explode(';', $type_charset);
			//external file types may already be set at this point
			$this->basicraw['type'] = empty($this->type) ?  $type_charset[0] : $this->type;
			$this->basicraw['charset'] = trim($type_charset[1]);
			$finfo = new finfo(FILEINFO_DEVICES);
			$this->basicraw['devices'] = $finfo->file($temppath);
		}

		//process basic info
		if (is_array($this->basicraw)) {
			require_once('lib/metadata/datatypes/basicinfo.php');
			$basic = new BasicInfo;
			$this->basicinfo = $basic->processRawData($this->basicraw);
		}

		//from this point, additional metadata is obtained from classes specific to the file type in separate php files
		//all results for this additional metadata go into the $this->typemeta array
		if ($extended && $this->canProcessExtended()) {
			//set content property
			if ($this->content === null) {
				$this->content = $ispath === false ? $file : file_get_contents($file);
			}
			//used for name of class and the file the class is in
			$type = $this->types[$this->basicraw['type']];
			//file must be named based on $type
			include_once('lib/metadata/filetypes/' . $type . '.php');
			//class name is same as file name except first letter is capitalized
			$type = ucfirst($type);
			$typeObj = new $type($this);
			$this->typemeta = $typeObj->getExtendedData($this);
			//Set client to null to avoid having clients using metadata to access the file content
			$this->content = null;
		}
		$this->setBestMetadata();
		if (!$leavelink) {
			unlink($temppath);
		}
		return $this;
	}

	/**
	 * Set the most complete and reconciled metadata array. Called by getMetadata.
	 */
	private function setBestMetadata()
	{
		if (isset($this->typemeta['reconciled']) && count($this->typemeta['reconciled']) > 0) {
			$this->typemeta['best'] = $this->typemeta['reconciled'];
		} elseif (isset($this->typemeta['combined']) && count($this->typemeta['combined']) > 0) {
			$this->typemeta['best'] = $this->typemeta['combined'];
		} elseif (isset($this->basicinfo) && count($this->basicinfo) > 0) {
			$this->typemeta['best'] = array('basiconly' => true, 'Basic Information' =>
				array ('File Data' => $this->basicinfo));
		} else {
			$this->typemeta['best'] = false;
		}
	}


	/**
	 * Merge basic file information into the reconciled or combined metadata array. Also adds data extraction time
	 *
	 * @param		object		$metaObj			a FileMetadata object that has had metadata extracted and reconciled
	 * @param		array		$metarray			the metadata array that is being built from the object
	 *
	 * @return		array		$metarray			metarray with merged basic file data and extraction time
	 */
	function mergeBasicInfo($metaObj, $metarray)
	{
		$sumtab		= 'Summary of Basic Information';
		$timeheader = 'Metadata Extraction Time';
		$bheader	= 'File Data';
		//set time of data extraction as now
		global $tikilib, $user;
		$extracttime = $tikilib->get_long_datetime(null, $user);
		$extractarray = array (
			$timeheader => array(
				'Extraction Time' => array(
					'label' 	=> '',
					'newval'	=> $extracttime,
				)
			)
		);

		if (isset($metaObj->basicinfo) && $metaObj->basicinfo !== false) {
			if (isset($metarray['reconciled']) && $metarray['reconciled'] !== false) {
				//if summary tab is already set
				if (isset($metarray['reconciled'][$sumtab][$bheader])) {
					//merge in basic info to file data section
					array_merge($metarray['reconciled'][$sumtab][$bheader], $metaObj->basicinfo);
				} else {
					$metarray['reconciled'] =
						array($sumtab => array($bheader => $metaObj->basicinfo)) + $metarray['reconciled'];
					$metarray['reconciled'][$sumtab][$bheader] = $metaObj->basicinfo;
				}
				//add extraction time
				$metarray['reconciled'][$sumtab] = $extractarray + $metarray['reconciled'][$sumtab];
			}
			if (is_array($metarray['combined'])) {
				$metarray['combined'] = array($sumtab => array($bheader => $metaObj->basicinfo)) + $metarray['combined'];
			} else {
				$metarray['combined'][$sumtab][$bheader] = $metaObj->basicinfo;
			}
			$metarray['combined'][$sumtab] = $extractarray + $metarray['combined'][$sumtab];
		}
		return $metarray;
	}

	/**
	 * Used to create a temporary path to a file when only the contents are available
	 * Necessary because some php functions used to extract metadata require a file path
	 * @param		string			$content		contents of a file
	 *
	 * @return		bool|string		$temppath		path to a temporary file in the temp directory or false if $content is
	 * 												empty or file is not writeable
	 */
	private function temppathFromContent($content)
	{
		if (!empty($content)) {
			$cwd = getcwd();
			$temppath = tempnam("$cwd/temp", 'temp_file_');
			if (!is_writeable($temppath)) {
				return false;
			}
			$temphandle = fopen($temppath, 'w');
			fwrite($temphandle, $content);
			fclose($temphandle);
			return $temppath;
		} else {
			return false;
		}
	}

	/**
	 * Checks to see if Tiki handles the processing of extended metadata for this file type
	 *
	 * @return bool
	 */
	function canProcessExtended()
	{
		if (isset($this->basicraw['type']) && array_key_exists($this->basicraw['type'], $this->types)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * To get a segment of binary content based on segment information
	 *
	 * @param $binarycontent		the binary content that the segment will be extracted from
	 * @param $marker				the marker denoting the beginning of the segment data
	 * @param $markerlength			length of marker, after which the segment size is assumed to be indicated
	 * @param $sizelength			length of the size indicator. actual content assumed to start after marker,
	 * 									and size indicator
	 *
	 * @return bool|string			segment portion is returned
	 */
	function getDataSegment($binarycontent, $marker, $markerlength, $sizelength)
	{
		//find position of segment marker
		$markerpos = strpos($binarycontent, $marker);
		if ($markerpos === false) {
			return false;
		} else {
			//get the binary value of the size indicator
			$rawsize = substr($binarycontent, $markerpos + $markerlength, $sizelength);
			//convert the binary string into the size number
			$size = unpack('nsize', $rawsize);
			//extract the desired segment of data
			$segdata = substr($binarycontent, $markerpos + $markerlength + $sizelength, $size['size']);
			return $segdata;
		}
	}

	/**
	 * Creates a Jquery tabbed dialog window for metadata. Assumes a 3-level array: first level is type of data,
	 * second represents categories or groupings for that type, and third is the fields
	 *
	 * @param      FileMetadata object		$metadata		Object or array with necessary properties to be displayed set
	 * @param      string					$id				HTML id attribute to identify the table
	 * @param      string					$id_link		HTML id attribute to identify the dialog table link
	 * @param      string					$filename		Used in the title of the dialog box
	 * @param 		bool					$mwg_compliant	Whether to reconcile extended metadata according to
	 * 															the Metadata Working Group guidelines
	 *
	 * Calls a smarty template to render the dialog box. The template will require a newval value for each field and
	 * will check for label and suffix values
	 */
	function dialogTabs($metadata, $id, $id_link, $filename)
	{
		$smarty = TikiLib::lib('smarty');
		$smarty->assign('id', $id);
		$smarty->assign('id_link', $id_link);
		$smarty->assign('filename', $filename);
		if (is_array($metadata) && count($metadata) > 0) {
			$metarray = $metadata;
		} elseif (!empty($metadata)) {
			$metarray = json_decode($metadata, true);
		}
		if (is_array($metarray) && count($metarray) > 0) {
			$smarty->assign('metarray', $metarray);
			$smarty->assign('type', 'data');
		} else {
			$smarty->assign('type', 'nodata');
		}
		$smarty->display('metadata/meta_view_dialog.tpl');
	}

	function pageTabs($metadata)
	{
		$smarty = TikiLib::lib('smarty');
		if (is_array($metadata) && count($metadata) > 0) {
			$metarray = $metadata;
		} elseif (!empty($metadata)) {
			$metarray = json_decode($metadata, true);
		}
		if (is_array($metarray) && count($metarray) > 0) {
			$smarty->assign('metarray', $metarray);
			$smarty->assign('type', 'data');
		} else {
			$smarty->assign('type', 'nodata');
		}
		$smarty->assign('extended', $this->canProcessExtended() ? 'y' : 'n');
		$smarty->display('metadata/meta_view_tabs.tpl');
	}

} //end of class
