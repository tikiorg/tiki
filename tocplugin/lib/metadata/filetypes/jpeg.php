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

require_once('lib/metadata/filetypes/imagefile.php');

/**
 * Reads JPEG-specific metadata from a JPEG file
 * Called by the FileMetadata class at metadata/metadata.php, which handles generic file metadata
 * Extends the ImageFile class which obtains metadata common to image files
 */
class Jpeg extends ImageFile
{

	/**
	 * @param	object		$metaObj		Object from the FileMetadata class which calls this function
	 *
	 * @return	array		$metadata		Returns an array of metadata
	 */
	function getExtendedData($metaObj)
	{
		/*
		 * these properties can be accessed similarly for all image files and were set in the ImageFile class that
		 * this class extends
		 */
		$metadata['header'] = $this->header;
		$metadata['width'] = $this->width;
		$metadata['height'] = $this->height;
		$metadata['otherinfo'] = $this->otherinfo;

		/*
		 * these following types of metadata are also common to other types of files but may need to be accessed
		 * differently based on file type
		 */


		//EXIF
		//get raw exif
		$metadata['exifraw'] = function_exists('exif_read_data') ? exif_read_data($metaObj->currname, 0, true) : false;
		//interpret and add tags
		if ($metadata['exifraw']) {
			require_once('lib/metadata/datatypes/exif.php');
			$exif = new Exif;
			$metadata['exif'] = $exif->processRawData($metadata['exifraw']);
			//add EXIF to combined metadata array that is not reconciled
			$metadata['combined']['exif'] = $metadata['exif'];
		} else {
			$metadata['exif'] = false;
		}


		//IPTC
		//get raw iptc and place in an iptc key so that iptc array has same number of levels as exif and xmp
		//and to distinguish from digest key added later
		$metadata['iptcraw']['iptc'] = !empty($this->otherinfo['APP13']) ? iptcparse($this->otherinfo['APP13']) : false;
		//process raw iptc
		if (is_array($metadata['iptcraw']['iptc'])) {
			//first prepare for processing
			foreach ($metadata['iptcraw']['iptc'] as $fieldname => $value) {
				if (count($value) > 1) {
					$metadata['iptcraw']['iptc'][$fieldname] = $value;
				} else {
					$metadata['iptcraw']['iptc'][$fieldname] = $value[0];
				}
			}
			//process raw data
			require_once('lib/metadata/datatypes/iptc.php');
			$iptc = new Iptc;
			$metadata['iptc'] = $iptc->processRawData($metadata['iptcraw']);
			//add IPTC to combined metadata array that is not reconciled
			$metadata['combined']['iptc'] = $metadata['iptc'];

			//check stored and create current hash
			/*
					 * add stored iptc hash if it exists
					 * The IPTC block is within the APP13 (Photoshop) segment, which is at $this->otherinfo['APP13']
					 * The stored checksum is at hex marker \x38\x42\x49\x4D\x04\x25\x00\x00\x00\x00 (resource ID 1061)
					 */
			$hashstored = $metaObj->getDataSegment(
				$this->otherinfo['APP13'],
				"\x38\x42\x49\x4D\x04\x25\x00\x00\x00\x00",
				10,
				2
			);
			if (!empty($hashstored)) {
				$metadata['iptc']['digest']['iptchashstored'] = array(
					'newval'    => bin2hex($hashstored),
					'label'     => 'Stored IPTC Hash'
				);
			}
			/*
		 * add calculated current hash of the IPTC block,
		 * which starts at hex marker \x38\x42\x49\x4D\x04\x04\x00\x00\x00\x00 within the APP13 segment
		 */
			$iptcblock = $metaObj->getDataSegment(
				$this->otherinfo['APP13'],
				"\x38\x42\x49\x4D\x04\x04\x00\x00\x00\x00",
				10,
				2
			);
			if (!empty($iptcblock)) {
				$metadata['iptc']['digest']['iptchashcurrent'] = array(
					'newval'    => md5($iptcblock),
					'label'     => 'Computed IPTC Hash'
				);
			}

			if (!isset($metadata['iptc']['digest']['iptchashstored']['newval']) ||
				(strlen($metadata['iptc']['digest']['iptchashstored']['newval']) > 0
					&& $metadata['iptc']['digest']['iptchashstored']['newval'] ==
						$metadata['iptc']['digest']['iptchashcurrent']['newval'])) {
				if (isset($metadata['iptc']['digest']['iptchashstored']['newval'])) {
					$metadata['iptc']['digest']['match']['newval'] = '';
					//place text in suffix so it can be translated
					$metadata['iptc']['digest']['match']['suffix'] =
						'IPTC stored and actual hash match - indication that metadata editors were compliant';
					$metadata['iptc']['digest']['match']['label'] = 'Note';
				}
			} else {
				$metadata['iptc']['digest']['mismatch']['newval'] = '';
				$metadata['iptc']['digest']['mismatch']['suffix'] =
					'Metadata has been edited by a noncompliant editor - IPTC stored and actual hash do not match';
				$metadata['iptc']['digest']['mismatch']['label'] = 'Warning';
			}

			/*
		  * In case we needed to get the individual APP13 records, below are a couple of examples
		  * each record starts with the tag marker of hex 1c (13), followed by record number and dataset number
		  *
		  * Example: record number is 02 and dataset number is hex 74 (116), so this is IPTC field 2#116 (or 2:116)
		  * $single = $metaObj->getDataSegment($this->otherinfo['APP13'], "\x1c\x02\x74", 3, 2);
		  *
		  * Example2: record number is 01 and dataset number is hex 5a (90), so this is IPTC field 1#090 (or 1:090)
		  * $single2 = $metaObj->getDataSegment($this->otherinfo['APP13'], "\x1c\x01\x5a", 3, 2);
		  *
		  * next two bytes after the record and dataset number are the size of the dataset
		 */
		} else {
			$metadata['iptc'] = false;
		}

		//XMP
		//get raw xmp DOM, convert to an array add tags and interpret
		if (!empty($metaObj->content)) {
			require_once('lib/metadata/datatypes/xmp.php');
			$xmp = new Xmp;
			$metadata['xmpraw'] = $xmp->getXmp($metaObj->content, $metaObj->basicraw['type']);
			$metadata['xmp'] = $xmp->processRawData($metadata['xmpraw']);
		} else {
			$metadata['xmpraw'] = false;
			$metadata['xmp'] = false;
		}
		//add XMP to combined unreconciled metadata array
		if ($metadata['xmp'] !== false) {
			$metadata['combined']['xmp'] = $metadata['xmp'];
		}

		//Reconcile extended metadata in accordance with the Metadata Working Group standards
		require_once('lib/metadata/reconcile.php');
		$rec = new ReconcileExifIptcXmp;
		$metadata['reconciled'] = $rec->reconcileAllMeta($metadata);

		//Add basic info
		$metadata = $metaObj->mergeBasicInfo($metaObj, $metadata);

		return $metadata;
	}
}
