<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}
/*
 * Reads and manipulates metadata included within a file
 * For separate classes for specific file types are called from here as well
 */

class FileMetadata
{
	var $currname = null;		//working file path used to access file, may not be the same as embedded in file metadata
	var $content = null;		//file content
	var $size = null;			//size of file
	var $width = null;			//width of image
	var $height = null;			//size of image
	var $type = null;			//file type - important for accessing all metadata
	var $charset = null;		//character set of file content
	var $devices = null;		//finfo devices
	var $typemeta = array();	//used to store metadata beyond generic file data
	var $header = null;			//header information
	var $otherinfo = null;		//specific to jpegs TODO move to JPEG class if possible
	var $error = null;			//error messages stored here
	
	/*
	 * Get basic and extended metadata included in the file itself
	 * @param 	string		$file				path to file or content of file
	 * @param	bool		$ispath				indicates whether $file is a path (true) or the file contents (false)
	 * @param	bool		$extended			indicates whether to retrieve extended metadata information
	 * @param	bool		$mwg_compliant		indicates whether to reconcile metadata in accordance with the 
	 *  											Metadata Working Group guidelines at http://www.metadataworkinggroup.org/pdf/mwg_guidance.pdf
	 * @return	object							FileMetadata object with properties for the various types of metadata
	 */
	function __construct($file, $ispath = true, $extended = true, $mwg_compliant = true) {
		//set contents and current name as well as type in some situations
		if (!$ispath) {
			//when $file is actual file contents rather than a path - create a temporary file name needed for some php functions
			$temppath = $this->temppathFromContent($file);
			$leavelink = false;
			if (!$temppath) {
				$this->error = tra('The file is empty');
			} else {
				$this->content = $file;
			}
		} else {
			//when $file is a path
			if (is_readable($file)) {
				$this->currname = $file;
				$this->content = file_get_contents($file);
				$temppath = $file;
				$leavelink = true;
				if (empty($this->content)) {
					$this->error = tra('The file is empty');
				}
			//if not readable, see if it's an external file
			} elseif (strpos($file, 'http') !== false) {
				$filegallib = TikiLib::lib('filegal');
				$externalinfo = $filegallib->get_info_from_url($file);
				$temppath = $this->temppathFromContent($externalinfo['data']);
				$leavelink = false;
				if (!$temppath) {
					$this->error = tra('The file is not readable');
				} else {
					$this->currname = $file;
					$this->content = $externalinfo['data'];
					//set type here for external files
					$this->type = $externalinfo['type'];
				}
			} else {
				$this->error = tra('The file is not readable');
				$leavelink = true;
			}
		}
		
		$this->size = function_exists('mb_strlen') ? mb_strlen($this->content, '8bit') : strlen($this->content);
		if (class_exists('finfo') && is_readable($temppath)) {
			$finfo = new finfo(FILEINFO_MIME);
			$type_charset = $finfo->file($temppath);
			$type_charset = explode(';', $type_charset);
			//external file tyes may already be set at this point
			$this->type = empty($this->type) ?  $type_charset[0] : $this->type;
			$this->charset = trim($type_charset[1]);
			$finfo = new finfo(FILEINFO_DEVICES);
			$this->devices = $finfo->file($temppath);
		}
		
		//get basic info common to all image types
		if (strpos($this->type, 'image') === 0) {
			$this->header = getimagesize($temppath, $otherinfo);
			$this->width = $this->header[0];
			$this->height = $this->header[1];
			$this->otherinfo = $otherinfo;
		}
		
		//from this point, additional metadata is obtained from classes specific to the file type in separate php files
		//all results for this additional metadata go into the $this->typemeta array
		if ($extended && !empty($this->type)) {
			switch ($this->type) {
				case 'image/jpeg':
					//used for name of class and the file the class is in
					$type = 'jpeg';
					break;
				default:
					$this->typemeta['error'] = tra('File type not handled by Tiki - only basic metadata available');
					if (!$leavelink) {
						unlink($temppath);
					}
					return $this;
			}
			$dataObj = '';
			//file must be named based on $type
			include_once($type . '.php');
			//class name is same as file name except first letter is capitalized
			$type = ucfirst($type);
			$typeObj = new $type($temppath, true);
				$dataObj = $typeObj->getExtendedData($this, $temppath);
			if (!$leavelink) {
				unlink($temppath);
			}
			return $dataObj;
		} else {
			if (!$leavelink) {
				unlink($temppath);
			}
			return $this;
		}
	}

	/*
	 * Used to create a temporary path to a file when only the contents are available
	 * Necessary because some php functions used to extract metadata require a file path
	 * @param		string		$content		contents of a file
	 * @return		string/bool					path to a temporary file in the temp directory or false if $content is empty 
	 * 												or file is not writeable
	 */
	//Remember to unlink $tempfile after using this function
	function temppathFromContent($content) {
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
	
	/*
	 * Creates a table that can be used in a Jquery accordion dialog window
	 * TODO need to make generic and simplify - specific to JPEGs right now; require a pre-made array as an input
	 * @param		FileMetadata object		$metaObj		Object should have necessary properties that will be displayed already set
	 * @param		string					$id				HTML id attribute the Jquery will use to identify the table
	 * @filename	string					$filename		Used in the title of the dialog box
	 * @return		string									Tables ready for an Jquery accordion dialog box
	 */
	function dialogMetadata($metaObj, $id, $filename) {
		if ($metaObj->type == 'image/jpeg') {
			$beg_table = "\r\t" . '<div><table>';
			$end_table = "\r\t" . '</table></div>'; 
			$col1_begin = "\r\t\t" . '<tr>' . "\r\t\t\t" . '<td>' . '<div class="wpimg-meta-col1">';
			$betw_col = '</div>' . '</td>' . "\r\t\t\t" . '<td>' . '<div class="wpimg-meta-col2">';
			$col2_end = '</div>' . '</td>' . "\r\t\t" . '</tr>';
			$beg_header = "\r\t" . '<h3><a href="#">';
			$end_header = '</a></h3>';
			$beg_section = "\r\t\t" . '<tr><td colspan="2"><div class="wpimg-meta-section"><em>';
			$end_section = '</em></div></td></tr>';
			$beg_false = "\r\t" . '<div>';
			$end_false = '</div>';
			//start the dialog box
			$dialog = "\r" . '<div id="' . $id . '" title="Image Metadata for ' . $filename . '" style="display:none">';
			//iptc section
			$dialog .= $beg_header . tra('Photographer Data (IPTC)') . $end_header;
			if ($metaObj->typemeta['iptc'] === false) {
				$dialog .= $beg_false . tra('No IPTC data') . $end_false;
			} else {
				$dialog .= $beg_table;
				foreach (array_keys($metaObj->typemeta['iptc']) as $key => $s) {
					$dialog .= $col1_begin . $metaObj->typemeta['iptc'][$s][1] . $betw_col 
						. htmlspecialchars($metaObj->typemeta['iptc'][$s][0]) . $col2_end;
				}
				$dialog .= $end_table; 
			}
			//exif section
			$dialog .= $beg_header . tra('File Data (EXIF)') . $end_header;
			if ($metaObj->typemeta['exif'] === false) {
				$dialog .= $beg_false . tra('No EXIF data') . $end_false;
			} else {
				global $tikilib, $user;
				//No processing of maker notes yet as specific code is needed for each manufacturer
				//Blank out field since it is very long and will distort the dialog box
				if (!empty($metaObj->typemeta['exif']['EXIF']['MakerNote'])) {
					$metaObj->exif['EXIF']['MakerNote'] = '(' . tra('Not processed') . ')';
				}
				//avoid using temporary file names
				if (!empty($metaObj->typemeta['exif']['FILE']['FileName'])) {
					$metaObj->typemeta['exif']['FILE']['FileName'] = $filename;
				}
				//convert unix time for display
				if (!empty($metaObj->typemeta['exif']['FILE']['FileDateTime'])) {
					$metaObj->typemeta['exif']['FILE']['FileDateTime'] = $tikilib->get_long_datetime($metaObj->typemeta['exif']['FILE']['FileDateTime'], $user) .
						'(' . tra('Unixtime:') . ' ' . $metaObj->typemeta['exif']['FILE']['FileDateTime'] . ')';
				}
				$name_array = array('ComponentsConfiguration', 'FileSource', 'SceneType', 'CFAPattern', 'GPSVersion');
				$dialog .= $beg_table;
				foreach ($metaObj->typemeta['exif'] as $cat => $fields) {
					$dialog .= $beg_section . ucfirst(strtolower($cat)) . $end_section;
					$clean_val = '';
					foreach ($fields as $name => $val) {
						//avoid notice as some values interpreted as arrays
						$clean_val = !is_array($val) ? trim($val) : '';
						if (in_array($name, $name_array)) {
							$clean_val = hexdec($clean_val);
						}
						if (strlen($clean_val) > 0) {
							$dialog .= $col1_begin . $name . $betw_col . htmlspecialchars($clean_val) . $col2_end;
						}
					}
				}
				$dialog .= $end_table; 
			}
			//xmp section
			$dialog .= $beg_header . tra('XMP Data') . $end_header;
			if ($metaObj->typemeta['xmp'] === false) {
				$dialog .= $beg_false . tra('No XMP data') . $end_false;
			} else {	
				$dialog .= $beg_table;
				$parent = $metaObj->typemeta['xmp']->getElementsByTagName('Description');
				$len = $parent->length;
				for($i = 0; $i < $len; $i++) {
					$dialog .= $beg_section . ucfirst($parent->item($i)->childNodes->item(1)->prefix) . $end_section;
					$len2 = $parent->item($i)->childNodes->length;
					for($j = 1; $j < $len2; $j++) {
						$dialog .= $col1_begin . $parent->item($i)->childNodes->item($j)->localName . $betw_col
								. htmlspecialchars($parent->item($i)->childNodes->item($j)->nodeValue) . $col2_end;
					}
				}
				$dialog .= $end_table; 
			}
			
			$dialog .= "\r" . '</div>';
		} else {
		$dialog = '<div id="' . $id . '" title="Image Metadata for ' . $filename . '" style="display:none">' 
			. tra('File type not handled by Tiki') . '</div>';
		}
		return $dialog;
	}
	
	/*
	 * Add labels to iptc array since the raw iptc has only numeric identifiers
	 * @param		array		$iptc_raw		Raw iptc array: identifier (like 2#000)=>value
	 * @return		array						Each identifier will be an array with the label and value
	 */
	function addIptcTags($iptc_raw) {
		$tags = $this->getIptcTags();
		foreach ($iptc_raw as $key => $value) {
			if (array_key_exists($key, $tags)) {
				trim($iptc_raw[$key][0]);
				$iptc_raw[$key][1] = trim($tags[$key]);
			} else {
				$iptc_raw[$key][1] = '';
			}
		}
		return $iptc_raw;
	}
	
	/*
	 * Maps iptc identifiers to labels
	 * Used in addIptcTags fucntion
	 */
	function getIptcTags() {
		$tags = array(
			'2#000' => tra('Application Record Version'),
			'2#003' => tra('Object Type Reference'),
			'2#004' => tra('Object Attribute Reference'),
			'2#005' => tra('Object Name'),
			'2#007' => tra('Edit Status'),
			'2#008' => tra('Editorial Update'),
			'2#010' => tra('Urgency'),
			'2#012' => tra('Subject Reference'),
			'2#015' => tra('Category'),
			'2#020' => tra('Supplemental Categories'),
			'2#022' => tra('Fixture Identifier'),
			'2#025' => tra('Keywords'),
			'2#026' => tra('Content Location Code'),
			'2#027' => tra('Content Location Name'),
			'2#030' => tra('Release Date'),
			'2#035' => tra('Release Time'),
			'2#037' => tra('Expiration Date'),
			'2#038' => tra('Expiration Time'),
			'2#040' => tra('Special Instructions'),
			'2#042' => tra('Action Advised'),
			'2#045' => tra('Reference Service'),
			'2#047' => tra('Reference Date'),
			'2#050' => tra('Reference Number'),
			'2#055' => tra('Date Created'),
			'2#060' => tra('Time Created'),
			'2#062' => tra('Digital Creation Date'),
			'2#063' => tra('Digital Creation Time'),
			'2#065' => tra('Originating Program'),
			'2#070' => tra('Program Version'),
			'2#075' => tra('Object Cycle'),
			'2#080' => tra('Byline'),
			'2#085' => tra('Byline Title'),
			'2#090' => tra('City'),
			'2#095' => tra('Province/State'),
			'2#100' => tra('Country Code'),
			'2#101' => tra('Country'),
			'2#103' => tra('Original Transmission Reference'),
			'2#105' => tra('Headline'),
			'2#110' => tra('Credit'),
			'2#115' => tra('Source'),
			'2#116' => tra('Copyright String'),
			'2#120' => tra('Caption'),
			'2#121' => tra('Local Caption'),
			'2#122' => tra('Writer-Editor'),
			'2#125' => tra('Rasterized Caption'),
			'2#130' => tra('Image Type'),
		);
		return $tags;
	}

	
} //end of class
