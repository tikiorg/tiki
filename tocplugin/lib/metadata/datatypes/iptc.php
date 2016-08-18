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
/*
 * Manipulates IPTC metadata included within a file
 */
class Iptc
{

	/**
	 * Legend, label, suffix and format information for each field
	 * See http://www.iptc.org/std/IIM/4.1/specification/IIMV4.1.pdf for specification source
	 *
	 * @var array
	 */
	var	$specs = array(
		'iptc'		=> array(
			'1#000' => array(
				'label' => 'Envelope Record Version',
				'maxlen' => 2,
				'binary' => true,
				'filter' => 'digits',
				'mandatory' => true,
			),
			'1#005' => array(
				'label' => 'Destination',
				'maxlen' => 1024, //minimum of 0
				'filter' => null,	//consists of sequentially contiguous graphic characters
			),
			'1#020' => array(
				'label' => 'File Format',
				'maxlen' => 2,
				'binary' => true,
				'mandatory' => true,
				'options' => array(
					 0 => 'No ObjectData',
					 1 => 'IPTC-NAA Digital Newsphoto Parameter Record',
					 2 => 'IPTC7901 Recommended Message Format',
					 3 => 'Tagged Image File Format (Adobe/Aldus Image data)',
					 4 => 'Illustrator (Adobe Graphics data)',
					 5 => 'AppleSingle (Apple Computer Inc)',
					 6 => 'NAA 89-3 (ANPA 1312)',
					 7 => 'MacBinary II',
					 8 => 'IPTC Unstructured Character Oriented File Format (UCOFF)',
					 9 => 'United Press International ANPA 1312 variant',
					10 => 'United Press International Down-Load Message',
					11 => 'JPEG File Interchange (JFIF)',
					12 => 'Photo-CD Image-Pac (Eastman Kodak)',
					13 => 'Microsoft Bit Mapped Graphics File [*.BMP]',
					14 => 'Digital Audio File [*.WAV] (Microsoft & Creative Labs)',
					15 => 'Audio plus Moving Video [*.AVI] (Microsoft)',
					16 => 'PC DOS/Windows Executable Files [*.COM][*.EXE]',
					17 => 'Compressed Binary File [*.ZIP] (PKWare Inc)',
					18 => 'Audio Interchange File Format AIFF (Apple Computer Inc)',
					19 => 'RIFF Wave (Microsoft Corporation)',
					20 => 'Freehand (Macromedia/Aldus)',
					21 => 'Hypertext Markup Language - HTML (The Internet Society)',
					22 => 'MPEG 2 Audio Layer 2 (Musicom), ISO/IEC',
					23 => 'MPEG 2 Audio Layer 3, ISO/IEC',
					24 => 'Portable Document File (*.PDF) Adobe',
					25 => 'News Industry Text Format (NITF)',
					26 => 'Tape Archive (*.TAR)',
					27 => 'Tidningarnas Telegrambyr� NITF version (TTNITF DTD)',
					28 => 'Ritzaus Bureau NITF version (RBNITF DTD)',
					29 => 'Corel Draw [*.CDR]',
				),
			),
			'1#022' => array(
				'label' => 'File Format Version',
				'maxlen' => 2,
				'binary' => true,
				'mandatory' => true,
				'options' => array(
					0 => 1,
					1 => array(
						//mapping from appendix A of http://www.iptc.org/std/IIM/4.1/specification/IIMV4.1.pdf
						//if 1#022 = 01 and 1#020 = one of the following
						 1 => '1',
						 3 => '5.0',
						 4 => '1.40',
						 5 => '2',
						 6 => '1',
						11 => '1.02',
						20 => '3.1',
					),
					2 => array(
						 1 => '2',
						 3 => '6.0',
						20 => '4.0',
						21 => '2.0',
					),
					3 => array(
						01 => '3',
						20 => '5.0',
					),
					4 => array(
						 1 => '4',
						02 => '4',
						20 => '5.5',
					),
				),
			),
			'1#030' => array(
				'label' => 'Service Identifier',
				'maxlen' => 10, //minimum of 0
				'filter' => null,
				'mandatory' => true,
			),
			'1#040' => array(
				'label' => 'Envelope Number',
				'maxlen' => 8,
				'filter' => 'digits',
				'mandatory' => true,
			),
			'1#050' => array(
				'label' => 'Poduct I.D.',
				'maxlen' => 32, //minimum of 0, consists of graphic characters
				'filter' => null,
			),
			'1#060' => array(
				'label' => 'Envelope Priority',
				'maxlen' => 1,
				'options' => array(
					0 => 'Reserved',
					1 => 'Most Urgent',
					2 => 'Second Most Urgent',
					3 => 'Third Most Urgent',
					4 => 'Fourth Most Urgent',
					5 => 'Normal Urgency',
					6 => 'Third Least Urgent',
					7 => 'Second Least Urgent',
					8 => 'Least Urgent',
					9 => 'User-Defined Priority',
				),
			),
			'1#070' => array(
				'label' => 'Date Sent',
				'maxlen' => 8,
				'filter' => 'date',	//CCYYMMDD
				'mandatory' => true,
			),
			'1#080' => array(
				'label' => 'Time Sent',
				'maxlen' => 11,
				'filter' => 'time',	//HHMMSS+HHMM or HHMMSS-HHMM
			),
			'1#090' => array(
				'label' => 'Coded Character Set',
				'maxlen' => 32,
				'options' => array(
					"\x1b\x25\x47" => 'UTF-8',
				),
			),
			'1#100' => array(
				'label' => 'Unique Name of Object',
				'maxlen' => 80, //minimum of 14
				'filter' => 'custom',	//* and ? not allowed, : and / allowed only as specified
			),
			'1#120' => array(
				'label' => 'ARM Identifier',
				'maxlen' => 2,
				'binary' => true,
				'filter' => 'digits',
			),
			'1#122' => array(
				'label' => 'ARM Version',
				'maxlen' => 2,
				'binary' => true,
				'filter' => 'digits',
			),
			'2#000' => array(
				'label' => 'Application Record Version',
				'maxlen' => 2,
				'binary' => true,
				'filter' => 'digits',
			),
			'2#003' => array(
				'label' => 'Object Type Reference',
				'maxlen' => 67,	//minimum is 3
				'filter' => 'custom',	//2 numeric characters, followed by a colon, followed by optional text
			),
			'2#004' => array(
				'label' => 'Intellectual Genre',	//Object Attribute Reference in original IPTC
				'maxlen' => 68,	//minimum is 4
				'filter' => 'custom',	//3 numeric characters, followed by a colon, followed by optional text
			),
			'2#005' => array(
				'label' => 'Title',	//Object Name in original IPTC
				'maxlen' => 64,	//minimum is 0
				'filter' => null,
			),
			'2#007' => array(
				'label' => 'Edit Status',
				'maxlen' => 64,	//minimum is 0
				'filter' => 'custom',	//consisting of graphic characters plus spaces
			),
			'2#008' => array(
				'label' => 'Editorial Update',
				'maxlen' => 2,
				'options' => array(
					01 => 'Additional language',
				),
			),
			'2#010' => array(
				'label' => 'Urgency',
				'maxlen' => 1,
				'options' => array(
					0 => 'Reserved',
					1 => 'Most Urgent',
					2 => 'Second Most Urgent',
					3 => 'Third Most Urgent',
					4 => 'Fourth Most Urgent',
					5 => 'Normal Urgency',
					6 => 'Third Least Urgent',
					7 => 'Second Least Urgent',
					8 => 'Least Urgent',
					9 => 'User-Defined Priority',
				),
			),
			'2#012' => array(
				'label' => 'Subject Code',	//Subject Reference in original IPTC
				'maxlen' => 236,	//minimum of 13
				'filter' => 'custom',	//see doc for pattern
			),
			'2#015' => array(
				'label' => 'Category',
				'maxlen' => 3,	//minimum is 0
				'filter' => 'alpha',
			),
			'2#020' => array(
				'label' => 'Supplemental Categories',
				'maxlen' => 32,	//minimum is 0
				'filter' => 'custom',	//graphic characters plus spaces
			),
			'2#022' => array(
				'label' => 'Fixture Identifier',
				'maxlen' => 32,	//minimum is 0
				'filter' => 'custom',	//graphic characters
			),
			'2#025' => array(
				'label' => 'Keywords',	//subject in XMP
				'maxlen' =>64,	//minimum is 0
				'filter' => 'striptags',	//graphic characters plus spaces
			),
			'2#026' => array(
				'label' => 'Content Location Code',
				'maxlen' => 3,
				'filter' => 'alpha',	//country code, some in Appendix D; should correspond to full name in 2#027
			),
			'2#027' => array(
				'label' => 'Content Location Name',
				'maxlen' => 64,	//minimum is 0
				'filter' => 'custom',	//graphic characters plus spaces; should correspond to country code in 2#026
			),
			'2#030' => array(
				'label' => 'Release Date',
				'maxlen' => 8,
				'filter' => 'date',	//CCYYMMDD
			),
			'2#035' => array(
				'label' => 'Release Time',
				'maxlen' => 11,
				'filter' => 'time',	//HHMMSS+HHMM or HHMMSS-HHMM
			),
			'2#037' => array(
				'label' => 'Expiration Date',
				'maxlen' => 8,
				'filter' => 'date',	//CCYYMMDD
			),
			'2#038' => array(
				'label' => 'Expiration Time',
				'maxlen' => 11,
				'filter' => 'time',	//HHMMSS+HHMM or HHMMSS-HHMM
			),
			'2#040' => array(
				'label' => 'Special Instructions',
				'maxlen' => 256,	//minimum is 0
				'filter' => 'custom',	//graphic characters plus spaces
			),
			'2#042' => array(
				'label' => 'Action Advised',
				'maxlen' => 2,
				'options' => array(
					1 => 'Object Kill',
					2 => 'Object Replace',
					3 => 'Object Append',
				),
			),
			//2#045, 2#047 and 2#050, when repeated, will be repeated together, i.e. in sequential triplets
			'2#045' => array(	//format identical with 1#030
				'label' => 'Reference Service',
				'maxlen' => 10, //minimum of 0
			),
			'2#047' => array(	//format identical with 1#070
				'label' => 'Reference Date',
				'maxlen' => 8,
				'filter' => 'date',	//CCYYMMDD
				'mandatory' => false,	//mandatory if 2#045 exists, otherwise not allowed
			),
			'2#050' => array(	//format identical with 1#040
				'label' => 'Reference Number',
				'maxlen' => 8,
				'filter' => 'digits',
				'mandatory' => false,	//mandatory if 2#045 exists, otherwise not allowed
			),
			'2#055' => array(
				'label' => 'Date Created',
				'maxlen' => 8,
				'filter' => 'date', //CCYYMMDD
			),
			'2#060' => array(
				'label' => 'Time Created',
				'maxlen' => 11,
				'filter' => 'time',	//HHMMSS+HHMM or HHMMSS-HHMM
			),
			'2#062' => array(
				'label' => 'Digital Creation Date',
				'maxlen' => 8,
				'filter' => 'date', //CCYYMMDD
			),
			'2#063' => array(
				'label' => 'Digital Creation Time',
				'maxlen' => 11,
				'filter' => 'time',	//HHMMSS+HHMM or HHMMSS-HHMM
			),
			'2#065' => array(
				'label' => 'Originating Program',
				'maxlen' => 32,	//minimum is 0
				'filter' => 'custom',	//graphic characters plus spaces
			),
			'2#070' => array(
				'label' => 'Program Version',
				'maxlen' => 10,	//minimum is 0
				'filter' => 'custom',	//graphic characters plus spaces
			),
			'2#075' => array(
				'label' => 'Object Cycle',
				'maxlen' => 1,
				'options' => array(
					'a' => 'Morning',
					'b' => 'Evening',
					'c' => 'Both',
				),
			),
			'2#080' => array(
				'label' => 'Creator',	//Byline in original IPTC
				'maxlen' => 32,	//minimum 0
				'filter' => 'custom',	//graphic characters plus spaces
			),
			'2#085' => array(
				'label' => 'Author\'s Position',	//Byline Title in original IPTC
				'maxlen' => 32,	//minimum 0
				'filter' => 'custom',	//graphic characters plus spaces
			),
			'2#090' => array(
				'label' => 'City',
				'maxlen' => 32,	//minimum 0
				'filter' => 'custom',	//graphic characters plus spaces
			),
			'2#092' => array(
				'label' => 'Location',	//Sublocation in original IPTC
				'maxlen' => 32,	//minimum 0
				'filter' => 'custom',	//graphic characters plus spaces
				'mandatory' => false,
			),
			'2#095' => array(
				'label' => 'Province/State',	//State in XMP
				'maxlen' => 32,	//minimum 0
				'filter' => 'custom',	//graphic characters plus spaces
			),
			'2#100' => array(
				'label' => 'Country Code',
				'maxlen' => 3,
				'filter' => 'alpha',
			),
			'2#101' => array(
				'label' => 'Country',
				'maxlen' => 64,	//minimum 0
				'filter' => 'custom',	//graphic characters plus spaces
			),
			'2#103' => array(
				'label' => 'Transmission Reference',	//Original Transmission Reference in original IPTC
				'maxlen' => 32,	//minimum 0
				'filter' => 'custom',	//graphic characters plus spaces
			),
			'2#105' => array(
				'label' => 'Headline',
				'maxlen' => 256,	//minimum 0
				'filter' => 'custom',	//graphic characters plus spaces
				'mandatory' => false,
			),
			'2#110' => array(
				'label' => 'Credit',
				'maxlen' => 32,	//minimum 0
				'filter' => 'custom',	//graphic characters plus spaces
			),
			'2#115' => array(
				'label' => 'Source',
				'maxlen' => 32,	//minimum 0
				'filter' => 'custom',	//graphic characters plus spaces
			),
			'2#116' => array(
				'label' => 'Rights',	//Copyright String in original IPTC
				'maxlen' => 128,	//minimum 0
				'filter' => 'custom',	//graphic characters plus spaces
			),
			'2#118' => array(
				'label' => 'Contact Info Details',	//Contact in original IPTC
				'maxlen' => 128,	//minimum 0
				'filter' => 'custom',	//graphic characters plus spaces
			),
			'2#120' => array(
				'label' => 'Description',	//Caption in original IPTC
				'maxlen' => 2000,	//minimum 0
				'filter' => 'custom',	//graphic characters plus carriage-returns, linefeeds and spaces
			),
			//not in specs. Used in Exiftool
			'2#121' => array(
				'label' => 'Local Caption',
				'maxlen' => 256,	//minimum 0
			),
			'2#122' => array(
				'label' => 'Caption Writer',	//Writer-Editor in original IPTC
				'maxlen' => 32,	//minimum 0
				'filter' => 'custom',	//graphic characters plus spaces
			),
			'2#125' => array(
				'label' => 'Rasterized Caption',
				'maxlen' => 7360,
				'binary' => true,
				'filter' => 'striptags',
			),
			'2#130' => array(
				'label' => 'Image Type',
				'maxlen' => 2,
				'options' => array(
					//first character options
					0 => array(
						0 => 'No Object Data',
						1 => 'Single Component',
						2 => 'Multiple components for a color project',
						3 => 'Multiple components for a color project',
						4 => 'Multiple components for a color project',
						9 => 'Supplemental objects related to other object data.',
					),
					//second character options
					1 => array(
						'W' => 'Monochrome',
						'Y' => 'Yellow component',
						'M' => 'Magenta component',
						'C' => 'Cyan component',
						'K' => 'Black component',
						'R' => 'Red component',
						'G' => 'Green component',
						'B' => 'Blue component',
						'T' => 'Text only',
						'F' => 'Full colour composite, frame sequential',
						'L' => 'Full colour composite, line sequential',
						'P' => 'Full colour composite, pixel sequential',
						'S' => 'Full colour composite, special interleaving',
					),
				),
			),
			'2#131' => array(
				'label' => 'Image Orientation',
				'maxlen' => 1,
				'options' => array(
					'P' => 'Portrait',
					'L' => 'Landscape',
					'S' => 'Square',
				),
			),
			'2#135' => array(
				'label' => 'Language Identifier',
				'maxlen' => 3,	//minimum 2
				'filter' => 'alpha',
			),
			'2#150' => array(
				'label' => 'Audio Type',
				'maxlen' => 2,
				'options' => array(
					//first character options
					0 => array(
						0 => 'No Object Data',
						1 => 'Mono',
						2 => 'Stereo',
					),
					//second character options
					1 => array(
						'A' => 'Actuality',
						'C' => 'Question and answer session',
						'M' => 'Music, transmitted by itself',
						'Q' => 'Response to a question',
						'R' => 'Raw sound',
						'S' => 'Scener',
						'T' => 'Text only',
						'V' => 'Voicer',
						'W' => 'Wrap',
					),
				),
			),
			'2#151' => array(
				'label' => 'Audio Sampling Rate',
				'maxlen' => 6,
				'filter' => 'digits',	//Hz with leading zeros. Render 011025 as 11025 Hz
			),
			'2#152' => array(
				'label' => 'Audio Sampling Resolution',
				'maxlen' => 2,
				'filter' => 'digits',	//Bits with leading zeros. Render 08 as 8 bits
			),
			'2#153' => array(
				'label' => 'Audio Duration',
				'maxlen' => 6,
				'filter' => 'time',	//HHMMSS
			),
			'2#154' => array(
				'label' => 'Audio Outcue',
				'maxlen' => 64,	//minimum 0
				'filter' => 'custom',	//graphic characters plus spaces
			),
			'2#200' => array(
				'label' => 'Preview File Format',
				'maxlen' => 2,
				'binary' => true,
				'mandatory' => false,	//mandatory if 2#202 exists
				//same as 1#020
				'options' => array(
					 0 => 'No ObjectData',
					 1 => 'IPTC-NAA Digital Newsphoto Parameter Record',
					 2 => 'IPTC7901 Recommended Message Format',
					 3 => 'Tagged Image File Format (Adobe/Aldus Image data)',
					 4 => 'Illustrator (Adobe Graphics data)',
					 5 => 'AppleSingle (Apple Computer Inc)',
					 6 => 'NAA 89-3 (ANPA 1312)',
					 7 => 'MacBinary II',
					 8 => 'IPTC Unstructured Character Oriented File Format (UCOFF)',
					 9 => 'United Press International ANPA 1312 variant',
					10 => 'United Press International Down-Load Message',
					11 => 'JPEG File Interchange (JFIF)',
					12 => 'Photo-CD Image-Pac (Eastman Kodak)',
					13 => 'Microsoft Bit Mapped Graphics File [*.BMP]',
					14 => 'Digital Audio File [*.WAV] (Microsoft & Creative Labs)',
					15 => 'Audio plus Moving Video [*.AVI] (Microsoft)',
					16 => 'PC DOS/Windows Executable Files [*.COM][*.EXE]',
					17 => 'Compressed Binary File [*.ZIP] (PKWare Inc)',
					18 => 'Audio Interchange File Format AIFF (Apple Computer Inc)',
					19 => 'RIFF Wave (Microsoft Corporation)',
					20 => 'Freehand (Macromedia/Aldus)',
					21 => 'Hypertext Markup Language - HTML (The Internet Society)',
					22 => 'MPEG 2 Audio Layer 2 (Musicom), ISO/IEC',
					23 => 'MPEG 2 Audio Layer 3, ISO/IEC',
					24 => 'Portable Document File (*.PDF) Adobe',
					25 => 'News Industry Text Format (NITF)',
					26 => 'Tape Archive (*.TAR)',
					27 => 'Tidningarnas Telegrambyra NITF version (TTNITF DTD)',
					28 => 'Ritzaus Bureau NITF version (RBNITF DTD)',
					29 => 'Corel Draw [*.CDR]',
				),
			),
			'2#201' => array(
				'label' => 'Preview File Format Version',
				'maxlen' => 2,
				'binary' => true,
				'mandatory' => false,	//mandatory if 2#202 exists
				'options' => array(
					0 => 1,
					1 => array(
						//mapping from appendix A of http://www.iptc.org/std/IIM/4.1/specification/IIMV4.1.pdf
						//if 1#022 = 01 and 1#020 = one of the following
						 1 => '1',
						 3 => '5.0',
						 4 => '1.40',
						 5 => '2',
						 6 => '1',
						11 => '1.02',
						20 => '3.1',
					),
					2 => array(
						 1 => '2',
						 3 => '6.0',
						20 => '4.0',
						21 => '2.0',
					),
					3 => array(
						 1 => '3',
						20 => '5.0',
					),
					4 => array(
						 1 => '4',
						 2 => '4',
						20 => '5.5',
					),
				),
			),
			'2#202' => array(
				'label' => 'Preview Data',
				'maxlen' => 256000,	//minimum 0
			),
		),
	);

	/**
	 * Process raw IPTC data by converting binary or hex information, replacing legend codes with their meanings,
	 * fixing labels, etc.
	 *
	 * @param		array		$exifraw		Array of raw IPTC data
	 *
	 * @return 		array		$iptc			Array of processed IPTC data, including label, newval and suffix values
	 * 												for each field
	 */
	function processRawData($iptcraw)
	{
		foreach ($iptcraw as $group => $fields) {
			foreach ($fields as $name => $field) {

				//store field name and raw values
				$iptc[$group][$name]['rawval'] = $field;
				$iptc[$group][$name]['locator'] = $name;

				//convert binary values to decimal
				if (isset($this->specs[$group][$name]['binary']) && $this->specs[$group][$name]['binary']) {
					$iptc[$group][$name]['rawval'] = hexdec(bin2hex($iptc[$group][$name]['rawval']));
				}

				//get option labels for fields that are option values
				if (isset($this->specs[$group][$name]['options'])) {
					$iptc[$group][$name]['newval'] = $this->specs[$group][$name]['options'][$iptc[$group][$name]['rawval']];
				} else {
					$iptc[$group][$name]['newval'] = $iptc[$group][$name]['rawval'];
				}

				//deal with arrays
				if (is_array($iptc[$group][$name]['newval'])) {
					$iptc[$group][$name]['newval'] = implode('; ', $iptc[$group][$name]['newval']);
				}

				//add labels and max field length
				if (isset($this->specs[$group][$name]['label'])) {
					$iptc[$group][$name]['label'] = $this->specs[$group][$name]['label'];
				} else {
					$iptc[$group][$name]['label'] = tra('Unknown Field: ') . $name;
				}
				if (isset($this->specs[$group][$name]['maxlen'])) {
					$iptc[$group][$name]['maxlen'] = $this->specs[$group][$name]['maxlen'];
				}
			}
		}
		//these options are more complex and will be handled singly
		//label depends on another field for these next two
		if (array_key_exists('1#022', $iptc['iptc']) && $iptc['iptc']['1#022']['newval']
			!= '00' && array_key_exists('1#020', $iptc['iptc'])) {
			//haven't tested
			$iptc['iptc']['1#022']['newval'] =
				$this->specs['iptc']['1#022']['options'][$iptc['iptc']['1#022']['newval']][$iptc['iptc']['1#020']['newval']];
		}
		if (array_key_exists('2#201', $iptc['iptc']) && $iptc['iptc']['2#201']['newval']
			!= '00' && array_key_exists('2#200', $iptc['iptc'])) {
			//haven't tested
			$iptc['iptc']['2#201']['newval'] =
				$this->specs['iptc']['2#201']['options'][$iptc['iptc']['2#201']['newval']][$iptc['iptc']['2#200']['newval']];
		}
		//individual characters need to be matched with these
		if (array_key_exists('2#130', $iptc['iptc'])) {
			$char1 = substr($iptc['iptc']['2#130']['newval'], 0, 1);
			$char2 = substr($iptc['iptc']['2#130']['newval'], 1, 1);
			$iptc['iptc']['2#130']['newval'] = $this->specs['iptc']['2#130']['options'][0][$char1] . '; '
				. $this->specs['iptc']['2#130']['options'][0][$char2];
		}
		if (array_key_exists('2#150', $iptc)) {
			$char1 = substr($iptc['iptc']['2#150']['newval'], 0, 1);
			$char2 = substr($iptc['iptc']['2#150']['newval'], 1, 1);
			$iptc['iptc']['2#150']['newval'] = $this->specs['iptc']['2#150']['options'][0][$char1] . '; '
				. $this->specs['iptc']['2#150']['options'][0][$char2];
		}
		return $iptc;
	}
}
