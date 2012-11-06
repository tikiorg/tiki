<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
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
	var $content = null;		//file content. Set private to avoid having clients using metadata to access the file content
	var $filesize = 0;			//file size in bytes
	var $size = null;			//size of file
	var $width = null;			//width of image
	var $height = null;			//size of image
	var $type = null;			//file type - important for accessing all metadata
	var $charset = null;		//character set of file content
	var $devices = null;		//finfo devices
	var $typemeta = null;		//used to store metadata beyond generic file data
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
	function __construct($file, $ispath = true, $extended = true, $mwg_compliant = true)
	{
		//set contents and current name as well as type in some situations
		if (!$ispath) {
			//when $file is actual file contents rather than a path - create a temporary file name needed for some php functions
			$temppath = $this->temppathFromContent($file);
			$leavelink = false;
			if (!$temppath) {
				$this->error = tra('The file is empty');
			} else {
				$this->content = $file;
				$this->filesize = function_exists('mb_strlen') ? mb_strlen($this->content, '8bit') : strlen($this->content);
				$this->currname = $temppath;
			}
		} else {
			//when $file is a path
			if (is_readable($file)) {
				$this->currname = $file;
				
				// Do not load the file content. The size may be excessive, e.g. for video images
				//	The filesize can be determined directly
				$this->content = null; // file_get_contents($file);
				$this->filesize = @filesize($file);				
				$temppath = $file;
				$leavelink = true;
				if ($this->filesize <= 0) {
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
					$this->filesize = function_exists('mb_strlen') ? mb_strlen($this->content, '8bit') : strlen($this->content);
					//set type here for external files
					$this->type = $externalinfo['type'];
				}
			} else {
				$this->error = tra('The file is not readable');
				$leavelink = true;
			}
		}
		
		$this->basicraw['size'] = $this->filesize;
		
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
			//file must be named based on $type
			include_once($type . '.php');
			//class name is same as file name except first letter is capitalized
			$type = ucfirst($type);
			$typeObj = new $type($this);
			$this->typemeta = $typeObj->getExtendedData($this);
//			if ($mwg_compliant) {
//				$rec = $this->metaMwgReconciled();
//			}
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
	function temppathFromContent($content)
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
	
	/*
	 * Creates a table that can be used in a Jquery accordion dialog window
	 * TODO need to make generic and simplify - specific to JPEGs right now; require a pre-made array as an input
	 * @param		FileMetadata object		$metaObj		Object should have necessary properties that will be displayed already set
	 * @param		string					$id				HTML id attribute the Jquery will use to identify the table
	 * @filename	string					$filename		Used in the title of the dialog box
	 * @return		string									Tables ready for an Jquery accordion dialog box
	 */
	function dialogMetadata($metaObj, $id, $filename)
	{
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
					$dialog .= $col1_begin . tra($metaObj->typemeta['iptc'][$s]['specs']['label']) . $betw_col 
						. htmlspecialchars($metaObj->typemeta['iptc'][$s]['value']['display']) . $col2_end;
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
				for ($i = 0; $i < $len; $i++) {
					$dialog .= $beg_section . ucfirst($parent->item($i)->childNodes->item(1)->prefix) . $end_section;
					$len2 = $parent->item($i)->childNodes->length;
					for ($j = 1; $j < $len2; $j++) {
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
	
	function xmpDomToArray($xmpObj)
	{
		if (get_class($xmpObj) == 'DOMDocument') {
			//file metadata is in the Description tag
			$parent = $xmpObj->getElementsByTagName('Description');
			$len = $parent->length;
			for ($i = 0; $i < $len; $i++) {
				//first level of nodes is assumed to have child nodes, so no values sought at this level
				$children = $parent->item($i)->childNodes;
				$len2 = $children->length;
				for ($j = 0; $j < $len2; $j++) {
					//only pick up DOMElements
					if ($children->item($j)->nodeType == 1) {
						$child = $children->item($j);
						if ($child->childNodes->length > 0 && !($child->childNodes->length == 1 && $child->firstChild->nodeType != 1)) {
							$xmparray[$child->prefix][$child->localName] = $this->xmpDomToArray($child->childNodes);
						} else {
							$xmparray[$child->prefix][$child->localName]['key'] = $child->prefix;
							$xmparray[$child->prefix][$child->localName]['label'] = ucfirst($child->localName);
							$xmparray[$child->prefix][$child->localName]['value'] = $child->nodeValue;
							$xmparray[$child->prefix][$child->localName]['locator'] = $child->getNodePath();
						}
					}
				}
			}
		} elseif (get_class($xmpObj) == 'DOMNodeList') {
			$parent = $xmpObj;
			$len3 = $parent->length;
			for ($i = 0; $i < $len3; $i++) {
				$item = $parent->item($i);
				if ($item->nodeType == 1) {
					if ($item->childNodes->length > 1) {
						if ($item->prefix == 'rdf' && $item->localName != 'li') {
							$len4 = $item->childNodes->length;
							$number = $item->childNodes;
							for ($z = 0; $z < $len4; $z++) {
								$list = $number->item($z);
								if ($list->nodeType == 1) {
/*									$xlist[$z] = array(
														'name' => $list->nodeName, 
														'val' => $list->nodeValue,
														'parentname' => $list->parentNode->nodeName, 
														'child' => $list->firstChild->nodeName,
														'childval' => $list->firstChild->nodeValue,
														'path' => $list->getNodePath(),
														'length' => $list->childNodes->length);
*/									$xmparray['value'][] = array(
													'key' => $list->prefix,
													'label' => $list->localName,
													'value' => $list->nodeValue,
													'locator' => $list->getNodePath(), 
												);
								}
							}
							return $xmparray;
						} else {
							$xmparray[$item->prefix][$item->localName] = $this->xmpDomToArray($item->childNodes);
						}
					} else {
						$xmparray[$item->localName]['key'] = $item->prefix;
						$xmparray[$item->localName]['label'] = $item->localName;
						$xmparray[$item->localName]['value'] = $item->nodeValue;
						$xmparray[$item->localName]['locator'] = $item->getNodePath();
					}
				}
			}
		}
		return $xmparray;
	}
	//TODO not complete - do not use yet
	function metaMwgReconciled()
	{
		$exist = 0;
		//start with exif
		if ($this->typemeta->exif !== false) {
			$exist += 1;
			//see if we need to reconcile exif with xmp data
			if (get_class($this->typemeta['xmp']) == 'DOMDocument') {
				$exist += 3;
				//convert xmp DOM document to an array for comparison
				$xmparray = $this->xmpDomToArray($this->typemeta['xmp']);
				//flatten xmp array to ease comparison
				$xmpflat = array();
				foreach ($xmparray as $topkey => $topdata) {
					foreach ($topdata as $field => $val) {
						$xmpflat[$field] = $val;
					}
				}
				//flatten the exif array too
				foreach ($this->typemeta['exif'] as $topkey => $topdata) {
					foreach ($topdata as $field => $val) {
						$exifflat[$field]['key'] = $topkey;
						$exifflat[$field]['value'] = $val;
					}
				}
				//compare here and extract duplicate fields between exif and xmp data
				$match = array_intersect_key($xmpflat, $exifflat);
				/*
				 * $exifflat will become the basis for the reconciled metadata since exif is preferred
				 * over xmp when both are present according to the Metadata Working Group guidelines
				 */
				foreach ($match as $key => $val) {
					//indicate that the exif field should be displayed
					$exifflat[$key]['read'] = 'exif';
					//indicate both the exif and xmp field will need to be updated if updating this field 
					// (in case write capability is added in the future)
					$exifflat[$key]['write'] = array('exif' => true, 'xmp' => true);
					//keep the xmp info in case needed for writing data back
					$exifflat[$key]['xmp'] = $xmpflat[$key];
					//note whether there is a difference between exif and xmp values
					if (is_array($xmpflat[$key]['value'])) {
						$valcheck = $xmpflat[$key]['value'][0]['value'];
					} else {
						$valcheck = $val['value'];
					}
					$exifflat[$key]['exifxmpok'] = $valcheck == $exifflat[$key]['value'] ? true : false ;
				}
				//delete xmp fields that duplicate exif fields
				$xmpflat = array_diff_key($xmpflat, $match);
			}
		}
		//now reconcile any iptc data
		if ($this->typemeta->iptc !== false) {
			include_once('lib/metadata/iptc.php');
			$exist += 5;
			if (get_class($this->typemeta['xmp']) == 'DOMDocument') {
				$match2 = array_intersect_key($iptcToXmp, $this->typemeta['iptc']);
				if (count($match2) > 0) {
					if (!empty($exifflat) && !empty($xmpflat)) {
						$xmpnewflat = $xmpflat;
					} else {
						$xmparray = $this->xmpDomToArray($this->typemeta['xmp']);
						foreach ($xmparray as $topkey => $topdata) {
							foreach ($topdata as $field => $val) {
								$xmpnewflat[$field] = $val;
							}
						}
					}
				}
				foreach ($match2 as $key => $valarray) {
					$flipmatch[$valarray['localname']] = $key;
				}
				$newmatch = array_intersect_key($xmpnewflat, $flipmatch);
				if ((strlen($this->typemeta['iptc']['hashstored']['value']['display']) == 16 && $this->typemeta['iptc']['hashstored']['value']['display'] 
					== $this->typemeta['iptc']['hashcurrent']['value']['display']) || !$this->typemeta['iptc']['hashstored']['value']['display']) {
					//prefer XMP value but use IPTC where XMP is missing
				} else {
					//campare field by field and use IPTC where they don't match
				}
			}
		}
		
		return $match;
	}
		
} //end of class
