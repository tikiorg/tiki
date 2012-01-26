<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: metadata.php 36361 2011-08-21 04:02:48Z lindonb $


/*
Returns xmp metadata as a DOMDocument
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
		break;
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

/*
 * Create an array of image metadata compliant with the Metadata Working Group guidelines
 * at http://www.metadataworkinggroup.org/pdf/mwg_guidance.pdf
 */
/*function img_meta_array($exif = false, $iptc = false, $xmp = false) {
	if ($exif !== false)
}
*/
function metaview_dialog($imageObj, $id, $filename) {
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
	if ($imageObj->iptc === false) {
		$dialog .= $beg_false . tra('No IPTC data') . $end_false;
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
		$dialog .= $beg_false . tra('No EXIF data') . $end_false;
	} else {
		$dialog .= $beg_table;
		foreach ($imageObj->exif as $cat => $fields) {
			$dialog .= $beg_section . ucfirst(strtolower($cat)) . $end_section;
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
		$dialog .= $beg_false . tra('No XMP data') . $end_false;
	} else {	
		$dialog .= $beg_table;
		$parent = $imageObj->xmp->getElementsByTagName('Description');
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
	return $dialog;
}

function iptc_to_xmp() {
	$map = array (
		'2#004' => 'Iptc4xmpCore:IntellectualGenre',
		'2#005' => 'dc:title',
		'2#010' => 'photoshop:Urgency',
		'2#012' => 'Iptc4xmpCore:SubjectCode',
		'2#015' => 'photoshop:Category',
		'2#020' => 'photoshop:SupplementalCategories',
		'2#025' => 'dc:subject',
		'2#040' => 'photoshop:Instructions',
		'2#055' => 'photoshop:DateCreated',
		'2#080' => 'dc:creator',
		'2#085' => 'photoshop:AuthorsPosition',
		'2#090' => 'photoshop:City',
		'2#092' => 'Iptc4xmpCore:Location',
		'2#095' => 'photoshop:State',
		'2#100' => 'Iptc4xmpCore:CountryCode',
		'2#101' => 'photoshop:Country',
		'2#103' => 'photoshop:TransmissionReference',
		'2#105' => 'photoshop:Headline',
		'2#110' => 'photoshop:Credit',
		'2#115' => 'photoshop:Source',
		'2#116' => 'dc:rights',
		'2#118' => 'Iptc4xmpCore:ContactInfoDetails',
		'2#120' => 'dc:description',
		'2#122' => 'photoshop:CaptionWriter',
	);
	return $map;
}

function get_iptc_tags_new() {
	$tags = array(
		'2#000' => 'ApplicationRecordVersion',
		'2#003' => 'ObjectTypeReference',
		'2#004' => 'Iptc4xmpCore:IntellectualGenre',
		'2#005' => 'dc:title',
		'2#007' => 'EditStatus',
		'2#008' => 'EditorialUpdate',
		'2#010' => 'photoshop:Urgency',
		'2#012' => 'Iptc4xmpCore:SubjectCode',
		'2#015' => 'photoshop:Category',
		'2#020' => 'photoshop:SupplementalCategory',
		'2#022' => 'FixtureIdentifier',
		'2#025' => 'dc:subject',
		'2#026' => 'ContentLocationCode',
		'2#027' => 'ContentLocationName',
		'2#030' => 'ReleaseDate',
		'2#035' => 'ReleaseTime',
		'2#037' => 'ExpirationDate',
		'2#038' => 'ExpirationTime',
		'2#040' => 'photoshop:Instructions',
		'2#042' => 'ActionAdvised',
		'2#045' => 'ReferenceService',
		'2#047' => 'ReferenceDate',
		'2#050' => 'ReferenceNumber',
		'2#055' => 'photoshop:DateCreated',
		'2#060' => 'TimeCreated',
		'2#062' => 'DigitalCreationDate',
		'2#063' => 'DigitalCreationTime',
		'2#065' => 'OriginatingProgram',
		'2#070' => 'ProgramVersion',
		'2#075' => 'ObjectCycle',
		'2#080' => 'dc:creator',
		'2#085' => 'photoshop:AuthorsPosition',
		'2#090' => 'photoshop:City',
		'2#092' => 'Iptc4xmpCore:Location',
		'2#095' => 'photoshop:State',
		'2#100' => 'Iptc4xmpCore:CountryCode',
		'2#101' => 'photoshop:Country',
		'2#103' => 'photoshop:TransmissionReference',
		'2#105' => 'photoshop:Headline',
		'2#110' => 'photoshop:Credit',
		'2#115' => 'photoshop:Source',
		'2#116' => 'dc:rights',
		'2#118' => 'Iptc4xmpCore:ContactInfoDetails',
		'2#120' => 'dc:description',
		'2#121' => 'LocalCaption',
		'2#122' => 'photoshop:CaptionWriter',
		'2#125' => 'RasterizedCaption',
		'2#130' => 'ImageType',
	);
	return $tags;
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
	);
	return $tags;
}

$xmp_tags = array(
// IPTC Core
	'Iptc4xmpCore:CiAdrCity',
	'Iptc4xmpCore:CiAdrCtry',
	'Iptc4xmpCore:CiAdrExtadr',
	'Iptc4xmpCore:CiAdrPcode',
	'Iptc4xmpCore:CiAdrRegion',
	'Iptc4xmpCore:CiEmailWork',
	'Iptc4xmpCore:CiTelWork',
	'Iptc4xmpCore:CiUrlWork',
	'Iptc4xmpCore:CountryCode',
	'Iptc4xmpCore:CreatorContactInfo',
	'Iptc4xmpCore:IntellectualGenre',
	'Iptc4xmpCore:Location',
	'Iptc4xmpCore:Scene',
	'Iptc4xmpCore:SubjectCode',
// Dublin Core Schema
	'dc:contributor',
	'dc:coverage',
	'dc:creator',
	'dc:date',
	'dc:description',
	'dc:format',
	'dc:identifier',
	'dc:language',
	'dc:publisher',
	'dc:relation',
	'dc:rights',
	'dc:source',
	'dc:subject',
	'dc:title',
	'dc:type',
// XMP Basic Schema
	'xmp:Advisory',
	'xmp:BaseURL',
	'xmp:CreateDate',
	'xmp:CreatorTool',
	'xmp:Identifier',
	'xmp:Label',
	'xmp:MetadataDate',
	'xmp:ModifyDate',
	'xmp:Nickname',
	'xmp:Rating',
	'xmp:Thumbnails',
	'xmpidq:Scheme',
// XMP Rights Management Schema
	'xmpRights:Certificate',
	'xmpRights:Marked',
	'xmpRights:Owner',
	'xmpRights:UsageTerms',
	'xmpRights:WebStatement',
// These are not in spec but Photoshop CS seems to use them
	'xap:Advisory',
	'xap:BaseURL',
	'xap:CreateDate',
	'xap:CreatorTool',
	'xap:Identifier',
	'xap:MetadataDate',
	'xap:ModifyDate',
	'xap:Nickname',
	'xap:Rating',
	'xap:Thumbnails',
	'xapidq:Scheme',
	'xapRights:Certificate',
	'xapRights:Copyright',
	'xapRights:Marked',
	'xapRights:Owner',
	'xapRights:UsageTerms',
	'xapRights:WebStatement',
// XMP Media Management Schema
	'xapMM:DerivedFrom',
	'xapMM:DocumentID',
	'xapMM:History',
	'xapMM:InstanceID',
	'xapMM:ManagedFrom',
	'xapMM:Manager',
	'xapMM:ManageTo',
	'xapMM:ManageUI',
	'xapMM:ManagerVariant',
	'xapMM:RenditionClass',
	'xapMM:RenditionParams',
	'xapMM:VersionID',
	'xapMM:Versions',
	'xapMM:LastURL',
	'xapMM:RenditionOf',
	'xapMM:SaveID',
// XMP Basic Job Ticket Schema
	'xapBJ:JobRef',
// XMP Paged-Text Schema
	'xmpTPg:MaxPageSize',
	'xmpTPg:NPages',
	'xmpTPg:Fonts',
	'xmpTPg:Colorants',
	'xmpTPg:PlateNames',
// Adobe PDF Schema
	'pdf:Keywords',
	'pdf:PDFVersion',
	'pdf:Producer',
// Photoshop Schema
	'photoshop:AuthorsPosition',
	'photoshop:CaptionWriter',
	'photoshop:Category',
	'photoshop:City',
	'photoshop:Country',
	'photoshop:Credit',
	'photoshop:DateCreated',
	'photoshop:Headline',
	'photoshop:History',
// Not in XMP spec
	'photoshop:Instructions',
	'photoshop:Source',
	'photoshop:State',
	'photoshop:SupplementalCategories',
	'photoshop:TransmissionReference',
	'photoshop:Urgency',
// EXIF Schemas
	'tiff:ImageWidth',
	'tiff:ImageLength',
	'tiff:BitsPerSample',
	'tiff:Compression',
	'tiff:PhotometricInterpretation',
	'tiff:Orientation',
	'tiff:SamplesPerPixel',
	'tiff:PlanarConfiguration',
	'tiff:YCbCrSubSampling',
	'tiff:YCbCrPositioning',
	'tiff:XResolution',
	'tiff:YResolution',
	'tiff:ResolutionUnit',
	'tiff:TransferFunction',
	'tiff:WhitePoint',
	'tiff:PrimaryChromaticities',
	'tiff:YCbCrCoefficients',
	'tiff:ReferenceBlackWhite',
	'tiff:DateTime',
	'tiff:ImageDescription',
	'tiff:Make',
	'tiff:Model',
	'tiff:Software',
	'tiff:Artist',
	'tiff:Copyright',
	'exif:ExifVersion',
	'exif:FlashpixVersion',
	'exif:ColorSpace',
	'exif:ComponentsConfiguration',
	'exif:CompressedBitsPerPixel',
	'exif:PixelXDimension',
	'exif:PixelYDimension',
	'exif:MakerNote',
	'exif:UserComment',
	'exif:RelatedSoundFile',
	'exif:DateTimeOriginal',
	'exif:DateTimeDigitized',
	'exif:ExposureTime',
	'exif:FNumber',
	'exif:ExposureProgram',
	'exif:SpectralSensitivity',
	'exif:ISOSpeedRatings',
	'exif:OECF',
	'exif:ShutterSpeedValue',
	'exif:ApertureValue',
	'exif:BrightnessValue',
	'exif:ExposureBiasValue',
	'exif:MaxApertureValue',
	'exif:SubjectDistance',
	'exif:MeteringMode',
	'exif:LightSource',
	'exif:Flash',
	'exif:FocalLength',
	'exif:SubjectArea',
	'exif:FlashEnergy',
	'exif:SpatialFrequencyResponse',
	'exif:FocalPlaneXResolution',
	'exif:FocalPlaneYResolution',
	'exif:FocalPlaneResolutionUnit',
	'exif:SubjectLocation',
	'exif:SensingMethod',
	'exif:FileSource',
	'exif:SceneType',
	'exif:CFAPattern',
	'exif:CustomRendered',
	'exif:ExposureMode',
	'exif:WhiteBalance',
	'exif:DigitalZoomRatio',
	'exif:FocalLengthIn35mmFilm',
	'exif:SceneCaptureType',
	'exif:GainControl',
	'exif:Contrast',
	'exif:Saturation',
	'exif:Sharpness',
	'exif:DeviceSettingDescription',
	'exif:SubjectDistanceRange',
	'exif:ImageUniqueID',
	'exif:GPSVersionID',
	'exif:GPSLatitude',
	'exif:GPSLongitude',
	'exif:GPSAltitudeRef',
	'exif:GPSAltitude',
	'exif:GPSTimeStamp',
	'exif:GPSSatellites',
	'exif:GPSStatus',
	'exif:GPSMeasureMode',
	'exif:GPSDOP',
	'exif:GPSSpeedRef',
	'exif:GPSSpeed',
	'exif:GPSTrackRef',
	'exif:GPSTrack',
	'exif:GPSImgDirectionRef',
	'exif:GPSImgDirection',
	'exif:GPSMapDatum',
	'exif:GPSDestLatitude',
	'exif:GPSDestLongitude',
	'exif:GPSDestBearingRef',
	'exif:GPSDestBearing',
	'exif:GPSDestDistanceRef',
	'exif:GPSDestDistance',
	'exif:GPSProcessingMethod',
	'exif:GPSAreaInformation',
	'exif:GPSDifferential',
	'stDim:w',
	'stDim:h',
	'stDim:unit',
	'xapGImg:height',
	'xapGImg:width',
	'xapGImg:format',
	'xapGImg:image',
	'stEvt:action',
	'stEvt:instanceID',
	'stEvt:parameters',
	'stEvt:softwareAgent',
	'stEvt:when',
	'stRef:instanceID',
	'stRef:documentID',
	'stRef:versionID',
	'stRef:renditionClass',
	'stRef:renditionParams',
	'stRef:manager',
	'stRef:managerVariant',
	'stRef:manageTo',
	'stRef:manageUI',
	'stVer:comments',
	'stVer:event',
	'stVer:modifyDate',
	'stVer:modifier',
	'stVer:version',
	'stJob:name',
	'stJob:id',
	'stJob:url',
// Exif Flash
	'exif:Fired',
	'exif:Return',
	'exif:Mode',
	'exif:Function',
	'exif:RedEyeMode',
// Exif OECF/SFR
	'exif:Columns',
	'exif:Rows',
	'exif:Names',
	'exif:Values',
// Exif CFAPattern
	'exif:Columns',
	'exif:Rows',
	'exif:Values',
// Exif DeviceSettings
	'exif:Columns',
	'exif:Rows',
	'exif:Settings',
);

