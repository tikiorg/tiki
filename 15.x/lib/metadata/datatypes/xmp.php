<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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
 * Manipulates XMP metadata included within a file
 */
class Xmp
{
	/**
	 * Legend and label information for each field
	 *
	 * @var array
	 */
	var	$specs = array(
		'photoshop'			=> array(
			'ColorMode' 	=> array(
				'options' 	=> array(
					'0'		=> 'Bitmap',
					'1'		=> 'Gray scale',
					'2'		=> 'Indexed color',
					'3'		=> 'RGB color',
					'4'		=> 'CMYK color',
					'7'		=> 'Multi-channel',
					'8'		=> 'Duotone',
					'9'		=> 'LAB color',
				),
			),
		),
		'dc'				=> array(
			'rights'		=> array(
				'label'		=> 'Rights',
			),
			'description'	=> array(
				'label'		=> 'Description',
			),
			'title'			=> array(
				'label'		=> 'Title',
			),
			'subject'		=> array(
				'label'		=> 'Subject',
			),
			'format'		=> array(
				'label'		=> 'Format',
			),
			'creator'		=> array(
				'label'		=> 'Creator',
			),
		),
	);

	/**
	 * Fields requiring special handling
	 *
	 * @var array
	 */
	var $special = array(
		'ComponentsConfiguration' => '',
	);

	/**
	 * Process raw XMP string by converting to a DOM document, replacing legend codes with their meanings,
	 * fixing labels, etc.
	 *
	 * @param 		xml string$xmpstring
	 *
	 * @return array|bool
	 */
	function processRawData($xmpstring)
	{
		//need to do a little preparation before processing fields
		if (!empty($xmpstring)) {
			$xmp = new DOMDocument();
			//create a DOM Document from the XML string
			$xmp->loadXML($xmpstring);
			//convert Dom Document to an array
			//TODO use Zend_Json::fromXml when zend fixes bug (see http://framework.zend.com/issues/browse/ZF-12224)
			$xmparray = $this->xmpDomToArray($xmp);
			//re-label Native Digest fields in tiff and exif sections to keep from overwriting when arrays are flattened
			//in other functions
			$sections = array('exif', 'tiff');
			foreach ($sections as $section) {
				if (isset($xmparray[$section]['NativeDigest'])) {
					$temp = explode(',', $xmparray[$section]['NativeDigest']['rawval']);
					$xmparray[$section]['NativeDigest']['rawval'] = implode(', ', $temp);
					$xmparray[$section][strtoupper($section) . 'NativeDigest'] = $xmparray[$section]['NativeDigest'];
					unset($xmparray[$section]['NativeDigest']);
				}
			}
			//now we can process fields
			$filter   = new Zend\Filter\Word\CamelCaseToSeparator();
			foreach ($xmparray as $group => $fields) {
				foreach ($fields as $name => $field) {
					if (isset($this->specs[$group][$name])) {
						//shorten the variable
						$specname = $this->specs[$group][$name];
						//convert coded fields into tags
						if (isset($specname['options'])) {
							$xmparray[$group][$name]['newval'] = $specname['options'][$xmparray[$group][$name]['rawval']];
						} else {
							$xmparray[$group][$name]['newval'] = $xmparray[$group][$name]['rawval'];
						}
						//fix labels
						if (isset($specname['label'])) {
							$xmparray[$group][$name]['label'] = $specname['label'];
						} else {
							//create reading-friendly labels from camel case tags
							$xmparray[$group][$name]['label'] = $filter->filter($name);
						}
					} else {
						//those not covered in $specs
						//create reading-friendly labels from camel case tags
						$xmparray[$group][$name]['label'] = $filter->filter($name);
						$xmparray[$group][$name]['newval'] = $xmparray[$group][$name]['rawval'];
					}
					//deal with arrays
					if (is_array($field['rawval'])) {
						if (array_key_exists($name, $this->special)) {
							$xmparray[$group][$name]['newval'] = $this->specialHandling($name, $field['rawval']);
						} elseif (isset($field['rawval']['rawval'])) {
							$xmparray[$group][$name]['newval'] = $field['rawval']['rawval'];
						} elseif (isset($field['rawval'][0])) {
							$xmparray[$group][$name]['newval'] = '';
							foreach ($field['rawval'] as $val) {
								$xmparray[$group][$name]['newval'] .= $val['rawval'] . '; ';
							}
						} else {
							$xmparray[$group][$name]['newval'] = '';
							foreach ($field['rawval'] as $val) {
								$xmparray[$group][$name]['newval'] .= $val['label'] . ': ' . $val['rawval'] . '; ';
							}
						}
					}
					//convert dates
					if (array_key_exists(
						$name,
						array(
							'ModifyDate' => '',
							'DateCreated' => '',
							'CreateDate' => '',
							'MetadataDate' => ''
						)
					)
					) {
						$dateObj = new DateTime($xmparray[$group][$name]['newval']);
						$date = $dateObj->format('Y-m-d  H:i:s  T');
						$xmparray[$group][$name]['newval'] = $date;
					}
				}
			}
		} else {
			return false;
		}
		return $xmparray;
	}

	/**
	 * Returns xmp metadata from a file as a fully formed xml string
	 *
	 * @param 		string				$filecontent		The file as a string (eg, after applying file_get_contents)
	 * @param 		string				$filetype			File type
	 *
	 * @return 		xml string|false	$xmp_text			Returns fully formed xml string
	 */
	function getXmp($filecontent, $filetype)
	{
		if ($filetype == 'image/jpeg') {
			$done = false;
			$start = 0;
			//TODO need to be able to handle multiple segments
			while ($done === false) {
				//search for hexadecimal marker for segment APP1 used for xmp data, and note position
				$app1_hit		= strpos($filecontent, "\xFF\xE1", $start);
				if ($app1_hit	!== false) {
					//next two bytes after marker indicate the segment size
					$size_raw	= substr($filecontent, $app1_hit + 2, 2);
					$size		= unpack('nsize', $size_raw);
					/*the segment APP1 marker is also used for other things (like EXIF data),
					so check that the segment starts with the right info
					allowing for 2 bytes for the marker and 2 bytes for the size before segment data starts*/
					$seg_data = substr($filecontent, $app1_hit + 4, $size['size']);
					$xmp_hit = strpos($seg_data, 'http://ns.adobe.com/xap/1.0/');
					if ($xmp_hit === 0) {
						$xmp_text_start	= strpos($seg_data, '<rdf:RDF');
						$xmp_text_end	= strpos($seg_data, '</rdf:RDF>');
						$endlen			= strlen('</rdf:RDF>');
						$xmp_length		= $xmp_text_end + $endlen - $xmp_text_start;
						$xmp_text		= substr($seg_data, $xmp_text_start, $xmp_length);
					}
					//start at the end of the segment just searched for the next search
					$start = $app1_hit + 4 + $size['size'];
				} else {
					$done = true;
				}
			}
			if (!isset($xmp_text)) {
				$xmp_text = false;
			}
		} else {
			$xmp_text = false;
		}
		return $xmp_text;
	}

	/**
	 * Convert an XML DomDocument from an image to an array
	 *
	 * @param 		DOM document			$xmpObj			XML document to process
	 *
	 * @return 		array|bool				$xmparray		Relevant portions of document converted to an array
	 */
	function xmpDomToArray($xmpObj)
	{
		if ($xmpObj !== false) {
			//This section is for the first pass
			if (get_class($xmpObj) == 'DOMDocument') {
				//File metadata is in the Description elements
				//There's one description element for each section of xmp data (exif,tiff, dc, etc.)
				//$parent is a DOMNodeList
				$topparents	= $xmpObj->getElementsByTagName('Description');
				$toplen		= $topparents->length;
				//iterate through sections (like tiff, exif, xap, etc.)
				for ($i = 0; $i < $toplen; $i++) {
					//these sections (like exif, tiff, etc.) have child nodes, so no values captured at this level
					//$children is a DOMNodeList
					$children		= $topparents->item($i)->childNodes;
					$childrenlen	= $children->length;
					//iterate through fields in a section, e.g. Orientation, XResolution, etc. within tiff section
					for ($j = 0; $j < $childrenlen; $j++) {
						$child = $children->item($j);
						//only pick up DOMElements to avoid empty DOMText fields
						if ($child->nodeType == 1) {
							//if $child has at least one child that is not a single DOMText field, then send back
							//through to process children
							if ($child->childNodes->length > 0
								&& !($child->childNodes->length == 1 && $child->firstChild->nodeType != 1)
							) {
								$xmparray[$child->prefix][$child->localName]['rawval']
									= $this->xmpDomToArray($child->childNodes);
							//this is where data from fields with single values (not multiple values) are picked up.
							//Most fields go through here
							} else {
								$xmparray[$child->prefix][$child->localName]['rawval']	= $child->nodeValue;
							}
							$xmparray[$child->prefix][$child->localName]['key']		= $child->prefix;
							$xmparray[$child->prefix][$child->localName]['label']	= ucfirst($child->localName);
							$xmparray[$child->prefix][$child->localName]['locator']	= $child->getNodePath();
						}
					}
				}
			//if sent from above code, then array is already at the field name, e.g.$xmparray['exif']['ISOSpeedRatings']
			} elseif (get_class($xmpObj) == 'DOMNodeList') {
				$nodelist = $xmpObj;
				$nodelistlen = $nodelist->length;
				//iterate through node list
				for ($i = 0; $i < $nodelistlen; $i++) {
					$nodeitem = $nodelist->item($i);
					//only pick up DOMElements to avoid empty DOMText fields
					if ($nodeitem->nodeType == 1) {
						//if the item itself has multiple items
						if ($nodeitem->childNodes->length > 1) {
							//Should capture tags like Seq, Alt, or Bag that have one or more list (li) items
							if ($nodeitem->prefix == 'rdf' && $nodeitem->localName != 'li') {
								$list		= $nodeitem->childNodes;
								$listlen	= $nodeitem->childNodes->length;
								//iterate through list items
								for ($z = 0; $z < $listlen; $z++) {
									$listitem = $list->item($z);
									//only pick up DOMElements to avoid empty DOMText fields
									if ($listitem->nodeType == 1) {
										//3 items indicates there is really only one list item (li) with content since
										//an empty text field precedes and succeeds every content field
										if ($listlen == 3) {
											$xmparray			= array(
												'key'			=> $listitem->prefix,
												'label'			=> $listitem->localName,
												'rawval'		=> $listitem->nodeValue,
												'locator'		=> $listitem->getNodePath(),
											);
										//multiple list (li) items go in an array here
										} else {
											$xmparray[]			= array(
												'key'			=> $listitem->prefix,
												'label'			=> $listitem->localName,
												'rawval'		=> $listitem->nodeValue,
												'locator'		=> $listitem->getNodePath(),
											);
										}
									}
								}
								return $xmparray;
							} else {
								//in case a list item (li) has children - images tested so far don't seem to have this
								//situation, so untested
								$xmparray[$nodeitem->prefix][$nodeitem->localName] =
									$this->xmpDomToArray($nodeitem->childNodes);
							}
						//fields like ['exif']['Flash'] go here, ie multiple items but not a list (li) inside of
						//another element (like Seq, Bag or Alt)
						} else {
							$xmparray[$nodeitem->localName]['key']		= $nodeitem->prefix;
							$xmparray[$nodeitem->localName]['label']		= $nodeitem->localName;
							$xmparray[$nodeitem->localName]['rawval']		= $nodeitem->nodeValue;
							$xmparray[$nodeitem->localName]['locator']	= $nodeitem->getNodePath();
						}
					}
				}
			}
		} else {
			$xmparray = false;
		}
		return $xmparray;
	}

	function specialHandling($fieldname, $value)
	{
		$ret = '';
		if ($fieldname == 'ComponentsConfiguration' && isset($value)) {
			include_once 'lib/metadata/datatypes/exif.php';
			$exif = new Exif;
			foreach ($value as $singleval) {
				$ret .= $exif->specs['EXIF'][$fieldname]['options']['0' . $singleval['rawval']] . ' ';
			}
		}
		return trim($ret);
	}
}
