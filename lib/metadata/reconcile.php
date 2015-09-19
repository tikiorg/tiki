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
/**
 * Reconciles metadata included within a file according to the Metadata Working Group guidelines
 * See http://www.metadataworkinggroup.org/pdf/mwg_guidance.pdf
 * Metadata in images particularly is not standardized and there are 3 main formats (IPTC, EXIF and XMP)
 * that overlap. The guidelines set forth how these data should be reconciled
 */
class ReconcileExifIptcXmp
{
	/**
	 * Array of all the types of metadata handled by this class
	 *
	 * @var array
	 */
	var $alltypes = array('exif' => '', 'iptc' => '', 'xmp' => '');

	/**
	 * Maps IPTC field labels to EXIF field labels
	 *
	 * @var array
	 */
	var $iptcToExif = array(
		'2#055' => 'DateTimeOriginal',	//date
		'2#060' => 'DateTimeOriginalTime',//fake EXIF field to match IPTC time which is separated into a different field
		'2#062' => 'DateTimeDigitized',	//date
		'2#063' => 'DateTimeDigitizedTime',	//fake EXIF field to match IPTC time which is separated into a different field
		'2#080' => 'Artist',
		'2#116' => 'Copyright',
		'2#120' => 'ImageDescription',
	);

	/**
	 * Maps IPTC field labels to XMP field labels
	 * XMP categories are indicated in comments
	 *
	 * @var array
	 */
	var $iptcToXmp = array (
		'2#004' => 'IntellectualGenre',		//Iptc4xmpCore
		'2#005' => 'title',					//dc
		'2#010' => 'Urgency',				//photoshop
		'2#012' => 'SubjectCode',			//Iptc4xmpCore
		'2#015' => 'Category',				//photoshop
		'2#020' => 'SupplementalCategories',//photoshop
		'2#025' => 'subject',				//dc
		'2#040' => 'Instructions',			//photoshop
		'2#055' => 'DateCreated',			//photoshop
		'2#060' => 'DateCreatedTime',		//fake XMP field to match IPTC time which is separated into a different field
		'2#062' => 'CreateDate',			//photoshop
		'2#063' => 'CreateDateTime',		//fake XMP field to match IPTC time which is separated into a different field
		'2#080' => 'creator',				//dc
		'2#085' => 'AuthorsPosition',		//photoshop
		'2#090' => 'City',					//photoshop
		'2#092' => 'Location',				//Iptc4xmpCore
		'2#095' => 'State',					//photoshop
		'2#100' => 'CountryCode',			//Iptc4xmpCore
		'2#101' => 'Country',				//photoshop
		'2#103' => 'TransmissionReference',	//photoshop
		'2#105' => 'Headline',				//photoshop
		'2#110' => 'Credit',				//photoshop
		'2#115' => 'Source',				//photoshop
		'2#116' => 'rights',				//dc
		'2#118' => 'ContactInfoDetails',	//Iptc4xmpCore
		'2#120' => 'description',			//dc
		'2#122' => 'CaptionWriter',			//photoshop
		'2#140' => 'Instructions',			//photosho
	);

	//Mapping for those fields where the name isn't the same and EXIF is preferred
	/**
	 * Maps XMP field labels to EXIF labels where the labels aren't the same and EXIF is the preferred value
	 * per the Metadata Working Group guidelines
	 *
	 * @var array
	 */
	var $xmpToExif = array(
		'description'		=> 'ImageDescription',
		'rights'			=> 'Copyright',
		'creator'			=> 'Artist',
		'GPSVersionID'		=> 'GPSVersion',
		'FlashpixVersion'	=> 'FlashPixVersion',
		'PixelXDimension'	=> 'ExifImageWidth',
		'PixelYDimension'	=> 'ExifImageLength',
		'format'			=> 'MimeType',
		'ModifyDate'		=> 'DateTime',
		'DateCreated'		=> 'DateTimeOriginal',
		'CreateDate'		=> 'DateTimeDigitized',
		'LensInfo'			=> 'UndefinedTag:0xA432',
	);

	/**
	 * Maps EXIF field labals to XMP labels where the labels don't match and where XMP is preferred
	 * XMP is preferred for dates, if it matches EXIF, because it includes a time zone offset
	 *
	 * @var array
	 */
	var $xmpPreferred = array(
		'DateTime'			=> 'ModifyDate',
		'DateTimeOriginal'	=> 'DateCreated',
		'DateTimeDigitized'	=> 'CreateDate',
	);

	/**
	 * Specifications for summary key information to be placed first in the array of data
	 *
	 * @var array
	 */
	var $basicSummary = array(
		'User Data'			=> array(
			'Title'				=> array(
				'iptc'				=> '2#005',
				'xmp'				=> 'title',
			),
			'Description'		=> array(
				'exif'				=> 'ImageDescription',
				'iptc'				=> '2#120',
				'xmp'				=> 'description',
			),
			'Keywords'			=> array(
				'iptc'				=> '2#025',
				'xmp'				=> 'subject',
			),
			'Creator'			=> array(
				'exif'				=> 'Artist',
				'iptc'				=> '2#080',
				'xmp'				=> 'creator',
			),
			'Copyright'			=> array(
				'exif'				=> 'Copyright',
				'iptc'				=> '2#116',
				'xmp'				=> 'rights',
			),
		),
		'Dates'				=> array(
			'Date of Original' => array(
				'exif'				=> 'DateTimeOriginal',
				'iptc'				=> '2#055',
				'xmp'				=> 'DateCreated'
			),
			'Date Digitized'	 	=> array(
				'exif'				=> 'DateTimeDigitized',
				'iptc'				=> '2#062',
				'xmp'				=> 'CreateDate',
			),
			'Date Modified'			=> array(
				'exif'				=> 'DateTime',
				'xmp'				=> 'ModifyDate',
			),
			'Metadata Date'			=> array(
				'xmp'				=> 'MetadataDate',
			),
		),
		'File Data'			=> array(
			'File Type'			=> array (
				'exif'				=> 'FileType',
				'xmp'				=> 'format',
			),
			'File Size'			=> array(
				'exif'				=> 'FileSize',
			),
			'Width'				=> array(
				'exif'				=> 'Width',
				'xmp'				=> 'PixelXDimension',
			),
			'Height'			=> array(
				'exif'				=> 'Height',
				'xmp'				=> 'PixelYDimension',
			),
			'Resolution'		=> array(
				'exif'				=> 'XResolution',
			),
			'Resolution Unit'		=> array(
				'exif'				=> 'ResolutionUnit',
			),
		),
	);
	/**
	 * Labels for reconciliation stats
	 *
	 * @var array
	 */
	var $statspecs = array (
		'fields'			=> array(
			'label'			=> 'Total Fields Shown',
		),
		'dupes'				=> array(
			'label'			=> 'Duplicate Fields'
		),
		'mismatches'		=> array(
			'label'			=> 'Mismatches',
		),
	);

	/**
	 * Array used to determine which data types to compare based on which iteration we're on
	 *
	 * @var array
	 */
	var $repeat = array(
		2	=> array('exif' => '', 'iptc' => ''),
		3	=> array('iptc' => '', 'xmp' => ''),
		4	=> array('exif' => '', 'xmp' => ''),
		5	=> array('exif' => '', 'xmp' => ''),
	);

	/**
	 * Map between xmp (keys) and exif (values) for the FLash field
	 * xmp stores as different fields whereas exif stores as one number
	 *
	 * @var array
	 */
	var $flashmap = array (
		'Fired' => array(
			'False'	=> 0,
			'True'	=> 1,
		),
		'Return' => array(
			'0'		=> 0,	//No return detected
			'2'		=> 4,	//Return not detected
			'3'		=> 6,	//Return detected
		),
		'Mode' => array(
			'0'		=> 0,	//Unknown
			'1'		=> 8,	//On
			'2'		=> 16,	//Off
			'3'		=> 24,	//Auto
		),
		'Function' => array(
			'False'	=> 0,
			'True'	=> 32,
		),
		'RedEyeMode' => array(
			'False'	=> 0,
			'True'	=> 64,
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
	 * Reconcile EXIF, IPTC and XMP metadata and return a single reconciled array
	 *
	 * @param 		array		$metadata		Expects the following format
	 * 											$metadata[type-eg IPTC][group-eg GPS][field-eg height]
	 *
	 * @return 		array|bool	$finalall		Array of reconciled data included stats
	 */
	function reconcileAllMeta($metadata)
	{
		//check which metadata types exist
		foreach ($this->alltypes as $alltype => $val) {
			if ($metadata[$alltype] !== false) {
				$types[$alltype] = '';
			}
		}
		//return false if no metadata
		if (count($types) == 0) {
			return false;
		//no need to reconcile with 1 type, just add summary info
		} elseif (count($types) == 1) {
			$omni['all'][key($types)] = $this->flatten($metadata[key($types)]);
			$basicsum = $this->makeSummaryInfo($omni);
			$metarray =	array(key($types) => $metadata[key($types)]);
			$metarray = $basicsum + $metarray;
			return $metarray;
		//more than one metadata type, so need to reconcile
		} else {
			//set main array with all data from all types
			foreach ($types as $type => $val) {
				$omni[$type]['flat'] = $this->flatten($metadata[$type]);
			}
			$omni = $this->addFakeFields($omni);
			foreach ($omni as $type => $flat) {
				$omni[$type]['left'] = $omni[$type]['flat'];
			}
			//send to reconciling function
			//if all three types are present, will need to iterate 5 times in total
			if (count($types) == count($this->alltypes)) {
				$omni = $this->reconcile($types, $omni, false, 1);
			//if exif and xmp are the types, then will need to iterate twice: once for matching field names and once for
			//mapped field names
			} elseif (array_key_exists('exif', $types) && array_key_exists('xmp', $types)) {
				$omni = $this->reconcile($types, $omni, false, 4);
			//other combinations of two data types are only iterated once
			} else {
				$omni = $this->reconcile($types, $omni, false, 5);
			}
			//combine duplicated fields with unduplicated for final array
			$omni['stats']['fields']['newval'] = 0;
			foreach ($types as $type => $val) {
				if (isset($omni[$type]['left']) && count($omni[$type]['left']) > 0) {
					if (!isset($omni['all'][$type])) {
						$omni['all'][$type] = $omni[$type]['left'];
						$omni['stats']['fields']['newval'] += count($omni['all'][$type]);
					} else {
						$omni['all'][$type] += $omni[$type]['left'];
						$omni['stats']['fields']['newval'] += count($omni['all'][$type]);
					}
				}
			}
		}
		//Prepare stats
		$stats = '';
		if (isset($omni['stats'])) {
			foreach ($this->statspecs as $key => $array) {
				if (isset($omni['stats'][$key])) {
					$stats[$key] = $omni['stats'][$key];
					$stats[$key]['label'] = $this->statspecs[$key]['label'];
				}
			}
			if (isset($stats['mismatches']['newval'])) {
				$stats['mismatches']['suffix'] = '(fields that should match but do not - see data detail)';
			}
			if (isset($stats['dupes']['newval']) && $stats['dupes']['newval'] > 0) {
				$stats['dupes']['suffix'] = '(this is normal - standard preferred field shown)';
			}
		}

		//summarize basic information to display first
		$basicsum = $this->makeSummaryInfo($omni);

		//add stats
		if (isset($stats)) {
			$basicsum['Summary of Basic Information']['Metadata Reconciliation Stats'] = $stats;
		}

		//unflatten the file metadata arrays by restoring the group level
		foreach ($types as $type => $val) {
			if (isset($metadata[$type])) {
				foreach ($metadata[$type] as $group => $fields) {
					$finalall[$type][$group] = array_intersect_key($omni['all'][$type], $fields);
				}
			}
		}
		$finalall = $basicsum + $finalall;
		return $finalall;
	}

	/**
	 * Remove second layer of multiple array. Used to ease array comparisons
	 *
	 * @param 		array		$multiArray			Minimum 3-level multiple array
	 *
	 * @return		array		$flat				flatted array
	 */
	function flatten($multiArray)
	{
		$flat = array();
		foreach ($multiArray as $secondkeys) {
			$flat = $flat + $secondkeys;
		}
		return $flat;
	}

	/**
	 * Add fake EXIF fields to reconcile with IPTC time which is separated into a different field
	 *
	 * @param 		array		$omni			Array where reconciliation results are collected
	 *
	 * @return 		array		$omni			Array with fake fields added
	 */
	private function addFakeFields($omni)
	{
		if (array_key_exists('2#060', $omni['iptc']['flat'])) {
			if (array_key_exists('DateTimeOriginal', $omni['exif']['flat'])) {
				$omni['exif']['flat']['DateTimeOriginalTime'] = $omni['exif']['flat']['DateTimeOriginal'];
			}
			if (array_key_exists('DateCreated', $omni['xmp']['flat'])) {
				$omni['xmp']['flat']['DateCreatedTime'] = $omni['xmp']['flat']['DateCreated'];
			}
		}
		if (array_key_exists('2#063', $omni['iptc']['flat'])) {
			if (array_key_exists('DateTimeDigitized', $omni['exif']['flat'])) {
				$omni['exif']['flat']['DateTimeDigitizedTime'] = $omni['exif']['flat']['DateTimeDigitized'];
			}
			if (array_key_exists('CreateDate', $omni['xmp']['flat'])) {
				$omni['xmp']['flat']['CreateDateTime'] = $omni['xmp']['flat']['CreateDate'];
			}
		}
		return $omni;
	}

	/**
	 * Create array of a summary of key metadata information. The structure and fields of the summary are set by
	 * $this->basicInfo
	 *
	 * @param $omni
	 *
	 * @return mixed
	 */
	private function makeSummaryInfo($omni)
	{
		$basicsum = array();
		foreach ($this->basicSummary as $infogroup => $fields) {
			foreach ($fields as $label => $infotypes) {
				foreach ($infotypes as $infotype => $fieldame) {
					if (isset($omni['all'][$infotype][$fieldame])) {
						$basicsum['Summary of Basic Information'][$infogroup][$label]
							= $omni['all'][$infotype][$fieldame];
						$basicsum['Summary of Basic Information'][$infogroup][$label]['label']
							= $label;
					}
				}
			}
		}
		return $basicsum;
	}

	/**
	 * Performs actual reconciliation of two data types. Multiple iterations are needed in some cases
	 *
	 * @param		array		$types			Array of types of metadata included in the information
	 * @param		array		$omni			Array of metadata to be reconciled
	 * @param		bool		$samekey		Indicates whether the field labels for the two datatypes to be
	 * 												compared are the same or not
	 * @param      numeric		$i				Indicates which data types to compare
	 *
	 * @return mixed
	 */
	function reconcile($types, $omni, $samekey = false, $i)
	{
		$match = array();
		//identify the types and determine matches
		//for files with all 3 metadata types, first pass checks to see if any fields are triplicated
		if (count($types) == 3) {
			$type1 = 'exif';
			$type2 = 'iptc';
			$type3 = 'xmp';
			//extract actual EXIF fields that could be duplicated in IPTC
			$exifmatch = array_flip(array_intersect_key(array_flip($this->iptcToExif), $omni['exif']['left']));
			//extract actual XMP fields that could be duplicated in IPTC
			$xmpmatch = array_flip(array_intersect_key(array_flip($this->iptcToXmp), $omni['xmp']['left']));
			//now extract any triplicated fields (ie, fields in all three metadata types)
			//resulting array will have IPTC => EXIF fieldname key => value pairs
			$match = array_intersect_key($exifmatch, $xmpmatch, $omni['iptc']['left']);
			//need an array with XMP fieldnames too
			$matchx = array_flip(array_intersect_key($xmpmatch, $match));
		//for files with 2 metadata types, or for subsequent iterations after checking for triplicates for files
		//with all three metadata types
		} elseif (count($types) == 2) {
			if ($samekey === false) {
				if (array_key_exists('exif', $types)) {
					$type1 = 'exif';
					$type2 = key(array_diff_key($types, array('exif' => '')));
				} else {
					$type1 = 'xmp';
					$type2 = 'iptc';
				}
				$map = $type2 . 'To' . ucfirst($type1);
				//compare actual fields in type2 to list of possible duplicates with type1
				$two2one = array_intersect_key($this->$map, $omni[$type2]['left']);
				$one2two = array_flip($two2one);
				//compare possible duplicate list to actual type one fields to identify actual duplicates
				$match = array_intersect_key($one2two, $omni[$type1]['left']);
			//$samekey = true, which is for EXIF and XMP fields with the same field names, therefore no mapping needed
			} else {
				$type1 = 'exif';
				$type2 = 'xmp';
				$match = array_intersect_key($omni[$type1]['left'], $omni[$type2]['left']);
			}
		}
		//start reconciling if there are duplicates
		if (count($match) > 0) {
			foreach ($match as $name => $value) {
				//set type => fieldname pairs for all metadata types in the file
				if (count($types) == 3) {
					$fnames = array(
						$type1 => $exifmatch[$name],
						$type2 => $name,
						$type3 => $xmpmatch[$name],
					);
				} else {
					$fnames = array(
						$type1 => $name,
						$type2 => $samekey === false ? $one2two[$name] : $name,
					);
				}
				//check to see if duplicate fields have equal values
				//check exif vs iptc
				if (array_key_exists('exif', $types) && array_key_exists('iptc', $types)) {
					$check['exif-iptc'] = $this->compareIptcExifValues(
						$fnames['exif'], $fnames['iptc'],
						$omni['iptc']['left'][$fnames['iptc']]['rawval'],
						$omni['exif']['left'][$fnames['exif']]['rawval']
					);
				}
				//check exif vs xmp
				if (array_key_exists('exif', $types) && array_key_exists('xmp', $types)) {
					$check['exif-xmp'] = $this->compareExifXmpValues(
						$fnames['exif'],
						$omni['xmp']['left'][$fnames['xmp']]['rawval'],
						$omni['exif']['left'][$fnames['exif']]['rawval']
					);
					//per MWG guidelines, prefer XMP time fields to EXIF if they match since XMP has the time zone
					//offset and EXIF doesn't
					if (array_key_exists($fnames['exif'], $this->xmpPreferred) && $check['exif-xmp'] == true) {
						$preferred = 'xmp';
					} else {
						$preferred = 'exif';
					}
				}
				//check iptc vs xmp
				if (array_key_exists('iptc', $types) && array_key_exists('xmp', $types)) {
					$check['iptc-xmp'] = $this->compareIptcXmpValues(
						$fnames['xmp'],
						$fnames['iptc'],
						$omni['iptc']['left'][$fnames['iptc']]['rawval'],
						$omni['xmp']['left'][$fnames['xmp']]['rawval']
					);
				}
				//now determine which of the duplicates will be displayed according to MWG guidelines
				if (array_key_exists('iptc', $types)) {
					//per MWG guidelines, if actual and stored IPTC hash are equal or stored is empty,
					//prefer other values over IPTC
					$hashmatch = $this->checkIptcHash($omni['iptc']['left']);
					if ($hashmatch) {
						if (count($types) == 3) {
							$type = $preferred;
						} else {
							$type = $type1;
						}
					//per MWG guidelines, if actual and stored IPTC hash differ, prefer IPTC over EXIF,
					//but prefer XMP if values match
					} else {
						if (array_key_exists('xmp', $types)) {
							if ($check['iptc-xmp']) {
								$type = 'xmp';
							} else {
								$type = 'iptc';
							}
						} else {
							$type = 'iptc';
						}
					}
				//$type2 is XMP and $type1 is EXIF
				} else {
					//prefer XMP for certain date fields because they include time zone offset info
					$type = $preferred;
				}

				foreach ($fnames as $tname => $fname) {
					//this is the field data that will be displayed
					if ($type == $tname) {
						if (isset($omni['all'][$type][$fname])) {
							$omni['all'][$type][$fname] += $omni[$tname]['left'][$fname];
						} else {
							$omni['all'][$type][$fname] = $omni[$tname]['left'][$fname];
						}
					//this is the duplicate data that will be stored with the displayed data, but not displayed
					} else {
						$omni['all'][$type][$fnames[$type]][$tname][$fname] = $omni[$tname]['left'][$fname];
					}
				}
				//collect stats on mismatches
				foreach ($check as $typecheck => $result) {
					$omni['all'][$type][$fnames[$type]]['check'][$typecheck] = $result;
					if ($result === false) {
						$omni['mismatches'][$type][$fnames[$type]][$typecheck] = $result;
						if (!isset($omni['stats']['mismatches']['newval'])) {
							$omni['stats']['mismatches']['newval'] = 1;
						} else {
							$omni['stats']['mismatches']['newval'] += 1;
						}
						$note = '  (' . strtoupper($typecheck) . '  ' . tra('duplicate fields do not match') . ')';
						if (!isset($omni['all'][$type][$fnames[$type]]['suffix'])) {
							$omni['all'][$type][$fnames[$type]]['suffix'] = $note;
						} else {
							$omni['all'][$type][$fnames[$type]]['suffix'] .= ' ' . $note;
						}
						break;
					}
				}
			}
			//collect stats on how many duplicates
			$count = $i == 1 ? count($match) * 3 : count($match);
			if (!isset($omni['stats']['dupes']['newval'])) {
				$omni['stats']['dupes']['newval'] = $count;
			} else {
				$omni['stats']['dupes']['newval'] += $count;
			}

			//delete duplicates from complete field list to identify unduplicated fields that are left
			if (count($types) == 3) {
				$omni['exif']['left'] = array_diff_key($omni['exif']['left'], array_flip($match));
				$omni['iptc']['left'] = array_diff_key($omni['iptc']['left'], $match);
				$omni['xmp']['left'] = array_diff_key($omni['xmp']['left'], $matchx);
			} else {
				$omni[$type1]['left'] = array_diff_key($omni[$type1]['left'], $match);
				$omni[$type2]['left'] = array_diff_key(
					$omni[$type2]['left'],
					isset($samekey) && $samekey ? $match : array_flip($match)
				);
			}
			//see if the data needs another pass
			//files with all three metadata types need 5 passes in total
			//files with EXIF and XMP need 2 passes, one for fields with matching field names and one for mapped fields
			if (isset($i) && $i < 5) {
				$i++;
				$newsamekey = $i == 4 ? true : false;
				$newtypes = $this->repeat[$i];
				$omni = $this->reconcile($newtypes, $omni, $newsamekey, $i);
			}
		} else {
			if (isset($i) && $i < 5) {
				$i++;
				$newsamekey = $i == 4 ? true : false;
				$newtypes = $this->repeat[$i];
				$omni = $this->reconcile($newtypes, $omni, $newsamekey, $i);
			}
		}
		return $omni;
	}

	/**
	 * Check stored IPTC checksum against calculated checksum. Per Metadata Working Group guidelines, if there
	 * is no stored checksum or if it is there and matches the calculated checksum, then prefer the non-IPTC value
	 *
	 * @param 		array		$iptcflat		Array of IPTC data including the calulated and stored checksum values
	 *
	 * @return 		bool						Return true when non-IPTC value should be used
	 */
	private function checkIptcHash($iptcflat)
	{
		if (!isset($iptcflat['iptchashstored']['newval']) || (strlen($iptcflat['iptchashstored']['newval']) > 0
			&& $iptcflat['iptchashstored']['newval'] == $iptcflat['iptchashcurrent']['newval'])) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Compare IPTC and EXIF values for fields that should be the same, taking into account IPTC length limitations
	 *
	 * @param 		string		$iptcval		Value of the IPTC field
	 * @param 		string		$exifval		Value of the EXIF field
	 *
	 * @return 		bool						Return true or false depending on whether the fields matched or not
	 */
	private function compareIptcExifValues($exifkey, $iptckey, $iptcval, $exifval)
	{
		//handle special cases first
		if (array_key_exists($exifkey, array('DateTimeDigitized' => '', 'DateTimeOriginal' => '', 'DateTimeDigitizedTime' => '', 'DateTimeOriginalTime' => ''))) {
			$exifdate = new DateTime($exifval);
			$iptcdate = new DateTime($iptcval);
			//time
			if ($iptckey == '2#060' || $iptckey == '2#063') {
				$exifcheckval = $exifdate->format('H:i:s');
				$iptccheckval = $iptcdate->format('H:i:s');
			//date
			} else {
				$exifcheckval = $exifdate->format('Y-m-d');
				$iptccheckval = $iptcdate->format('Y-m-d');
			}
		} else {
			//IPTC fields have length limits, so compare up to the length of the IPTC field to avoid false negatives
			$len = strlen($iptcval);
			$exifcheckval = substr($exifval, 0, $len);
			$iptccheckval = $iptcval;
		}
		if ($exifcheckval == $iptccheckval) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Compare IPTC and XMP fields that should have the same values, taking into account IPTC length limitations
	 *
	 * @param 		string		$xmpkey			XMP field name for special handling cases
	 * @param 		string		$iptcval		IPTC field value
	 * @param 		string		$xmpval			XMP field value
	 *
	 * @return 		bool						Return true or false depending on whether the fields matched or not
	 */
	private function compareIptcXmpValues($xmpkey, $iptckey, $iptcval, $xmpval)
	{
		$iptccheckval = '';
		$xmpcheckval = '';
		//the subject field is an array in both IPTC and XMP, so concatenate to compare
		if ($xmpkey == 'subject') {
			foreach ($iptcval as $val) {
				$iptccheckval .= $val;
			}
			foreach ($xmpval as $val) {
				$xmpcheckval .= $val['rawval'];
			}
		} elseif (array_key_exists($xmpkey, array('DateCreated' => '', 'CreateDate' => '', 'DateCreatedTime' => '', 'CreateDateTime' => ''))) {
			$xmpdate = new DateTime($xmpval);
			$iptcdate = new DateTime($iptcval);
			//time
			if ($iptckey == '2#060' || $iptckey == '2#063') {
				$xmpcheckval = $xmpdate->format('H:i:s');
				$iptccheckval = $iptcdate->format('H:i:s');
				//date
			} else {
				$xmpcheckval = $xmpdate->format('Y-m-d');
				$iptccheckval = $iptcdate->format('Y-m-d');
			}
		} else {
			//the ultimate raw value for XMP list fields (<li>) is one level deeper
			if (is_array($xmpval)) {
				$xmpcheckval = $xmpval['rawval'];
			} else {
				$xmpcheckval = $xmpval;
			}
			//IPTC fields have length limits, so compare up to the length of the IPTC field to avoid false negatives
			$iptccheckval = $iptcval;
			$xmpcheckval = substr($xmpcheckval, 0, strlen($iptcval));
		}
		//now check against each other
		if ($iptccheckval == $xmpcheckval) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Compare EXIF and XMP fields that should have the same values
	 *
	 * @param 		string		$exifkey		EXIF field name for fields that need special handling
	 * @param 		string		$xmpval			XMP field vale
	 * @param 		string		$exifval		EXIF field value
	 *
	 * @return 		bool						Return true or false depending on whether the fields matched or not
	 */
	private function compareExifXmpValues($exifkey, $xmpval, $exifval)
	{
		if (isset($xmpval)) {
			 //handle special cases
			 //XMP has the timezone offset for these fields whereas EXIF does not, so compare times without the offset
			if ($exifkey == 'DateTimeOriginal' ||
					$exifkey == 'DateTimeDigitized' ||
					$exifkey == 'DateTime' ||
					$exifkey == 'DateTimeOriginalTime' ||
					$exifkey == 'DateTimeDigitizedTime'
			) {
				$exifcheckval = strtotime($exifval);
				$xmpcheckval = strtotime(substr($xmpval, 0, strlen($exifval)));
			//XMP GPS Version raw field is already in final format whereas EXIF field is not
			} elseif ($exifkey == 'GPSVersion') {
				$xmpcheckval = explode('.', $xmpval);
				$xmpcheckval = '0' . implode('0', $xmpcheckval);
			} elseif ($exifkey == 'ComponentsConfiguration') {
				foreach ($xmpval as $val) {
					$new = '0' . $val['rawval'];
					$xmpcheckval = isset($xmpcheckval) ? $xmpcheckval . $new : $new;
				}
			} elseif ($exifkey == 'UndefinedTag:0xA432') {
				$exifcheckval = implode(' ', $exifval);
				$xmpcheckval = $xmpval;
			}
			//set EXIF value to check for all other cases
			if (!isset($exifcheckval)) {
				$exifcheckval = $exifval;
			}
			//when the XMP value is an array
			if (is_array($xmpval) && !array_key_exists($exifkey, $this->special)) {
				//Flash is an array in XMP and a single number code in EXIF
				if ($exifkey == 'Flash') {
					$exifcheckval = $exifval;
					$xmpcheckval = '';
					foreach ($xmpval as $flash => $status) {
						$xmpcheckval = $xmpcheckval + $this->flashmap[$flash][$status['rawval']];
					}
				//the ultimate raw value for XMP list fields (<li>) is one level deeper
				} else {
					$xmpcheckval = $xmpval['rawval'];
				}
			}
			//set XMP value to check for all other cases
			if (!isset($xmpcheckval)) {
				$xmpcheckval = $xmpval;
			}
		} else {
			return false;
		}
		//now check against each other
		if ($xmpcheckval == $exifcheckval) {
			return true;
		} else {
			return false;
		}
	}
}
