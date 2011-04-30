<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$


/*
Returns xmp metadata as a SimpleXMLElement object
$filecontent is the file as a string (after applying file_get_contents)
$mimetype is the fiel type as noted in file headers, such as image/jpeg
 */
function get_xmp($filecontent, $mimetype) {
	switch ($mimetype) {
		case 'image/jpeg':
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
					//the segment APP1 marker is also used for other things (EXIF data), 
					//so check that the segment starts with the right info
					//allowing for 2 bytes for the marker and 2 bytes for the size before segment data starts
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
		break;
	}
	//TODO need to be able to handle multiple segments
	$xmp_element = !empty($xmp_text) ? simplexml_load_string($xmp_text[0]) : false;
	return $xmp_element;
}


function metaview_dialog($imageObj, $id, $filename) {
	$beg_table = "\r\t" . '<div><table>';
	$end_table = "\r\t" . '</table></div>'; 
	$col1_begin = "\r\t\t" . '<tr>' . "\r\t\t\t" . '<td>' . '<div class="wpimg-meta-col1">';
	$betw_col = '</div>' . '</td>' . "\r\t\t\t" . '<td>' . '<div class="wpimg-meta-col2">';
	$col2_end = '</div>' . '</td>' . "\r\t\t" . '</tr>';
	$beg_header = "\r\t" . '<h3><a href="#">';
	$end_header = '</a></h3>';
	//start the dialog box
	$dialog = "\r" . '<div id="' . $id . '" title="Image Metadata for ' . $filename . '" style="display:none">';
	//iptc section
	$dialog .= $beg_header . tra('Photographer Data (IPTC)') . $end_header;
	if ($imageObj->iptc == null) {
		$dialog .= "\r\t" . '<div>' . tra('No IPTC data') . '</div>';
	} else {
		$dialog .= $beg_table;
		foreach (array_keys($imageObj->iptc) as $key => $s) {
			$dialog .= $col1_begin . $imageObj->iptc[$s][1] . $betw_col . htmlspecialchars($imageObj->iptc[$s][0]) . $col2_end;
		}
		$dialog .= $end_table; 
	}
	//exif section
	$name_array = array('ComponentsConfiguration', 'FileSource', 'SceneType', 'CFAPattern', 'GPSVersion');
	$dialog .= $beg_header . tra('File Data (EXIF)') . $end_header;
	if ($imageObj->exif === false) {
		$dialog .= "\r\t" . '<div>' . tra('No EXIF data') . '</div>';
	} else {
		$dialog .= $beg_table;
		foreach ($imageObj->exif as $cat => $fields) {
			$dialog .= "\r\t\t" . '<tr><td colspan="2"><div class="wpimg-meta-section"><em>' 
				. ucfirst(strtolower($cat)) . '</em></div></td></tr>' ;
			foreach ($fields as $name => $val) {
				$clean_val = trim($val);
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
	if ($imageObj->xmp === false) {
		$dialog .= "\r\t" . '<div>' . tra('No XMP data') . '</div>';
	} else {	
		include_once ('lib/xml.php');
		$imageObj->xmp_array = xml2array($imageObj->xmp);
		$dialog .= $beg_table;
		foreach ($imageObj->xmp_array['children'][0]['children'] as $level_1) {
			$dialog .= "\r\t\t" . '<tr><td colspan="2"><div class="wpimg-meta-section"><em>' 
				. ucfirst($level_1['children'][0]['namespace']) . '</em></div></td></tr>' ;
			foreach ($level_1['children'] as $level_2) {
				if (isset($level_2['content'])) {
					$clean_content = trim($level_2['content']);
					if (strlen($clean_content) > 0) {
						$dialog .= $col1_begin . $level_2['name'] . $betw_col . htmlspecialchars($clean_content) . $col2_end;
					}
				}
			}
		}
		$dialog .= $end_table; 
	}
	
	$dialog .= "\r" . '</div>';
	return $dialog;
}

function get_iptc($otherinfo) {
	if (!empty($otherinfo['APP13'])) {
		$iptc = iptcparse($otherinfo['APP13']);
		$tags = get_iptc_tags();
		foreach ($iptc as $key => $value) {
			if (array_key_exists($key, $tags)) {
				trim($iptc[$key][0]);
				$iptc[$key][1] = trim($tags[$key]);
			} else {
				$iptc[$key][1] = '';
			}
		}
	} else {
		$iptc = null;
	}
	return $iptc;
}

function get_iptc_tags() {
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
		'2#122' => tra('Writer-Editor'),
	
	);
	return $tags;
}
