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
 * Reads JPEG-specific metadata from a JPEG file
 * Called by the FileMetadata class at metadata/metadata.php, which handles generic file metadata
 * Extends the ImageFile class which obtains metadata common to image files
 */

require_once('lib/metadata/imagefile.php');

class Jpeg extends ImageFile
{
	
	/*
	 * Get extended JPEG metadata
	 * @param		object		$metaObj		Object from the FileMetadata class which calls this function as part of its __constructor
	 * @return		array		$metadata		Returns an array of metadata
	 */
	function getExtendedData($metaObj) 
	{
		//these properties can be accessed similarly for all image files and were set in the ImageFile class that this class extends
		$metadata['header'] = $this->header;
		$metadata['width'] = $this->width;
		$metadata['height'] = $this->height;
		$metadata['otherinfo'] = $this->otherinfo;
		
		//these types of metadata are also common to other types of files but may need to be accessed differently based on exact file type
		$metadata['exif'] = function_exists('exif_read_data') ? exif_read_data($metaObj->currname, 0, true) : false;
		$metadata['iptcraw'] = !empty($this->otherinfo['APP13']) ? iptcparse($this->otherinfo['APP13']) : false;
		if ($metadata['iptcraw']) {
			require_once('lib/metadata/iptc.php');
			$iptc = new Iptc;
			$metadata['iptc'] = $iptc->addIptcTags($metadata['iptcraw']);
		} else {
			$metadata['iptc'] = false;
		}
		$metadata['xmp'] = isset($metaObj->content) ? $this->getXmp($metaObj->content) : false;
		$metadata['xmparray'] = $metadata['xmp'] ? $metaObj->xmpDomToArray($metadata['xmp']) : false;
				
		/* Check the stored checksum value for IPTC data against a current hash calculation
		 * Affects how IPTC and XMP metadata within a file are reconciled according to the Metadata Working Group guidelines 
		 * (http://www.metadataworkinggroup.org/pdf/mwg_guidance.pdf)
		 */
		
		/* First check to see if there is a checksum stored for the iptc block
		 * The IPTC block is within the APP13 (Photoshop) segment, which is at $this->otherinfo['APP13']
		 * The stored checksum is at the hex marker \x38\x42\x49\x4D\x04\x25\x00\x00\x00\x00 (resource ID 1061)
		 */
		$hashstored = $metaObj->getDataSegment($this->otherinfo['APP13'], "\x38\x42\x49\x4D\x04\x25\x00\x00\x00\x00", 10, 2);
		$metadata['iptc']['hashstored']['value']['display'] = bin2hex($hashstored);
		$metadata['iptc']['hashstored']['specs']['label'] = tra('Stored IPTC Digest');
		//calculate current hash of the IPTC block, which starts at hex marker \x38\x42\x49\x4D\x04\x04\x00\x00\x00\x00 within the APP13 segment
		$iptcblock = $metaObj->getDataSegment($this->otherinfo['APP13'], "\x38\x42\x49\x4D\x04\x04\x00\x00\x00\x00", 10, 2);
		$metadata['iptc']['hashcurrent']['value']['display'] = md5($iptcblock);
		$metadata['iptc']['hashcurrent']['specs']['label'] = tra('Calculated IPTC Digest');
		
		/*
		 * In case we needed to get the individual APP13 records, below are a couple of examples
		 * each record starts with the tag marker of hex 1c (13), followed by record number and dataset number
		 * 
		 * record number is 02 and dataset number is hex 74 (116), so this is IPTC field 2#116 (or 2:116)
		 * $single = $metaObj->getDataSegment($this->otherinfo['APP13'], "\x1c\x02\x74", 3, 2);
		 * 
		 * record number is 01 and dataset number is hex 5a (90), so this is IPTC field 1#090 (or 1:090)
		 * $single2 = $metaObj->getDataSegment($this->otherinfo['APP13'], "\x1c\x01\x5a", 3, 2);
		 * 
		 * next two bytes are the size of the dataset
		*/		
		return $metadata;
	}
	
	/*
	 * Returns xmp metadata from a JPEG file as a DOMDocument
	 * @param		string		$filecontent		The file as a string (eg, after applying file_get_contents)
	 */
	function getXmp($filecontent) 
	{
		$done = false;
		$start = 0;
		$i = 0;
		while ($done === false) {
			//search for hexadecimal marker for segment APP1 used for xmp data and note position
			$app1_hit = strpos($filecontent, "\xFF\xE1", $start);
			if ($app1_hit !== false) {
				//next two bytes after marker indicate the segment size
				$size_raw = substr($filecontent, $app1_hit + 2, 2);
				$size = unpack('nsize', $size_raw);
				/*the segment APP1 marker is also used for other things (like EXIF data), 
				so check that the segment starts with the right info
				allowing for 2 bytes for the marker and 2 bytes for the size before segment data starts*/
				$seg_data = substr($filecontent, $app1_hit + 4, $size['size']);
				$xmp_hit = strpos($seg_data, 'http://ns.adobe.com/xap/1.0/');
				if ($xmp_hit === 0) {
					//it's possible to have xmp data in more than one APP1 segment
					//so use an array
					$xmp_text = array();
					$xmp_text_start = strpos($seg_data, '<x:xmpmeta');
					$xmp_text_end = strpos($seg_data, '</x:xmpmeta>');
					$xmp_length = $xmp_text_end - $xmp_text_start;
					$test_end = substr($seg_data, $xmp_text_end);
					$xmp_text[$i] = substr($seg_data, $xmp_text_start, $xmp_length + 12);
					$i++;
				}
				//start at the end of the segment just searched for the next search
				$start = $app1_hit + 4 + $size['size'];
			} else {
				$done = true;
			}
		}
		//TODO need to be able to handle multiple segments
		if (!empty($xmp_text)) {
			$xmp_doc = new DOMDocument();
			$xmp_doc->loadXML($xmp_text[0]);
		} else {
			$xmp_doc = false;
		}
		return $xmp_doc;
	}
}
