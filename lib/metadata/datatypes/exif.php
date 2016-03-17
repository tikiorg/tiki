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
 * Manipulates EXIF metadata included within a file
 */
class Exif
{
	/**
	 * Legend, label, suffix and format information for each field
	 * See www.cipa.jp/english/hyoujunka/kikaku/pdf/DC-008-2010_E.pdf for specification source for Exif version 2.3
	 *
	 * @var array
	 */
	var	$specs = array(
		//the PHP function exif_read_data adds the FILE group when returning exif data
		'FILE' => array(
			'FileDateTime'	=> array(
				'label'		=> 'Data Extraction Time',
			),
			'FileSize' => array(
				'suffix'	=> 'bytes',
			),
			'FileType' => array(
				//from http://php.net/manual/function.exif-imagetype.php
				'options'	=> array(
					'1'		=> 'GIF',
					'2'		=> 'JPEG',
					'3'		=> 'PNG',
					'4'		=> 'SWF',
					'5'		=> 'PSD',
					'6'		=> 'BMP',
					'7'		=> 'TIFF II (Intel byte order)',
					'8'		=> 'TIFF MM (Motorola byte order)',
					'9'		=> 'JPC',
					'10'	=> 'JP2',
					'11'	=> 'JPX',
					'12'	=> 'JB2',
					'13'	=> 'SWC',
					'14'	=> 'IFF',
					'15'	=> 'WBMP',
					'16'	=> 'XBM',
					'17'	=> 'ICO',
				),
			),
		),
		'COMPUTED' => array(
			'html' => array(
				'label' => 'HTML',
			),
			'Height' => array(
				'suffix' => 'pixels',
			),
			'Width' => array(
				'suffix' => 'pixels',
			),
			'IsColor' => array(
				'options' => array(
					0		=> 'No',
					1		=> 'Yes',
				),
			),
			'ByteOrderMotorola' => array(
				'options' => array(
					0		=> 'No',
					1		=> 'Yes',
				),
			),
			'Thumbnail.FileType' => array(
				'label'		=> 'Thumbnail File Type',
				//from http://php.net/manual/function.exif-imagetype.php
				'options' => array(
					'1'		=> 'GIF',
					'2'		=> 'JPEG',
					'3'		=> 'PNG',
					'4'		=> 'SWF',
					'5'		=> 'PSD',
					'6'		=> 'BMP',
					'7'		=> 'TIFF II (Intel byte order)',
					'8'		=> 'TIFF MM (Motorola byte order)',
					'9'		=> 'JPC',
					'10'	=> 'JP2',
					'11'	=> 'JPX',
					'12'	=> 'JB2',
					'13'	=> 'SWC',
					'14'	=> 'IFF',
					'15'	=> 'WBMP',
					'16'	=> 'XBM',
					'17'	=> 'ICO',
				),
			),
			'Thumbnail.MimeType' => array(
				'label'		=> 'Thumbnail Mime Type',
			),
		),
		'IFD0' => array(
			'ImageWidth' => array(
				'suffix'	=> 'pixels',
			),
			'ImageLength' => array(
				'suffix'	=> 'pixels',
			),
			'Compression' => array(
				'options' => array(
					'1'		=> 'Uncompressed',
					'2'		=> 'CCITT 1D',
					'3'		=> 'T4/Group 3 Fax',
					'4'		=> 'T6/Group 4 Fax',
					'5'		=> 'LZW',
					'6'		=> 'JPEG (old-style)',
					'7'		=> 'JPEG',
					'8'		=> 'Adobe Deflate',
					'9'		=> 'JBIG B&W',
					'10'	=> 'JBIG Color',
					'99'	=> 'JPEG',
					'262'	=> 'Kodak 262',
					'32766'	=> 'Next',
					'32767'	=> 'Sony ARW Compressed',
					'32769'	=> 'Packed RAW',
					'32770'	=> 'Samsung SRW Compressed',
					'32771'	=> 'CCIRLEW',
					'32773'	=> 'PackBits',
					'32809'	=> 'Thunderscan',
					'32867'	=> 'Kodak KDC Compressed',
					'32895'	=> 'IT8CTPAD',
					'32896'	=> 'IT8LW',
					'32897'	=> 'IT8MP',
					'32898'	=> 'IT8BL',
					'32908'	=> 'PixarFilm',
					'32909'	=> 'PixarLog',
					'32946'	=> 'Deflate',
					'32947'	=> 'DCS',
					'34661'	=> 'JBIG',
					'34676'	=> 'SGILog',
					'34677'	=> 'SGILog24',
					'34712'	=> 'JPEG 2000',
					'34713'	=> 'Nikon NEF Compressed',
					'34715'	=> 'JBIG2 TIFF FX',
					'34718'	=> 'Microsoft Document Imaging (MDI) Binary Level Codec',
					'34719'	=> 'Microsoft Document Imaging (MDI) Progressive Transform Codec',
					'34720'	=> 'Microsoft Document Imaging (MDI) Vector',
					'65000'	=> 'Kodak DCR Compressed',
					'65535'	=> 'Pentax PEF Compressed)',
				),
			),
			'PhotometricInterpretation' => array(
				'options' => array(
					'0'		=> 'WhiteIsZero',
					'1'		=> 'BlackIsZero',
					'2'		=> 'RGB',
					'3'		=> 'RGB Palette',
					'4'		=> 'Transparency Mask',
					'5'		=> 'CMYK',
					'6'		=> 'YCbCr',
					'8'		=> 'CIELab',
					'9'		=> 'ICCLab',
					'10'	=> 'ITULab',
					'32803'	=> 'Color Filter Array',
					'32844'	=> 'Pixar LogL',
					'32845'	=> 'Pixar LogLuv',
					'34892'	=> 'Linear Raw',
				),
			),
			'Orientation' => array(
				'options' => array(
					'1'		=> 'Horizontal (normal)',
					'2'		=> 'Mirror horizontal',
					'3'		=> 'Rotate 180',
					'4'		=> 'Mirror vertical',
					'5'		=> 'Mirror horizontal and rotate 270 CW',
					'6'		=> 'Rotate 90 CW',
					'7'		=> 'Mirror horizontal and rotate 90 CW',
					'8'		=> 'Rotate 270 CW',
				),
			),
			'XResolution' => array(
				'format'	=> 'rational',
				'suffix'	=> 'pixels (dots) per unit',
			),
			'YResolution' => array(
				'format'	=> 	'rational',
				'suffix'	=> 'pixels (dots) per unit',
			),
			'PlanarConfiguration' => array(
				'options' => array(
					'1'		=> 'Chunky',
					'2'		=> 'Planar',
				),
			),
			'ResolutionUnit' => array(
				'options' => array(
					'2'		=> 'inch',
					'3'		=> 'cm',
				),
			),
			'WhitePoint' => array(
				'format'	=> 'rational',
			),
			'PrimaryChromaticities' => array(
				'format'	=> 'rational',
			),
			'YCbCrCoefficients' => array(
				'format'	=> 'rational',
			),
			'YCbCrSubSampling' => array(
				'options' => array(
					'1 1'	=> 'YCbCr4:4:4 (1 1)',
					'1 2'	=> 'YCbCr4:4:0 (1 2)',
					'1 4'	=> 'YCbCr4:4:1 (1 4)',
					'2 1'	=> 'YCbCr4:2:2 (2 1)',
					'2 2'	=> 'YCbCr4:2:0 (2 2)',
					'2 4'	=> 'YCbCr4:2:1 (2 4)',
					'4 1'	=> 'YCbCr4:1:1 (4 1)',
					'4 2'	=> 'YCbCr4:1:0 (4 2)',
				),
			),
			'YCbCrPositioning' => array(
				'options' => array(
					1		=> 'Centered',
					2		=> 'Co-sited',
				),
			),
			'ReferenceBlackWhite' => array(
				'format'	=> 'rational',
			),
			'JPEGInterchangeFormat' => array(
				'label'		=> 'Thumbnail Offset',
			),
			'JPEGInterchangeFormatLength' => array(
				'label'		=> 'Thumbnail Length',
			),
			'Exif_IFD_Pointer' => array(
				'label'		=> 'EXIF IFD Pointer',
			),
			'GPS_IFD_Pointer' => array(
				'label'		=> 'GPS IFD Pointer',
			),		),
		'EXIF' => array(
			'ExposureTime' => array(
				'format'	=> 'rational',
				'suffix'	=> 'seconds',
			),
			'FNumber' => array(
				'format'	=> 'rational',
			),
			'ExposureProgram' => array(
				'options' => array(
					'0'		=> 'Not Defined',
					'1'		=> 'Manual',
					'2'		=> 'Program AE',
					'3'		=> 'Aperture-priority AE',
					'4'		=> 'Shutter speed priority AE',
					'5'		=> 'Creative (Slow speed)',
					'6'		=> 'Action (High speed)',
					'7'		=> 'Portrait',
					'8'		=> 'Landscape',
					'9'		=> 'Bulb',
				),
			),
			'SensitivityType' => array(
				'options' => array(
					'0'		=> 'Unknown',
					'1'		=> 'Standard Output Sensitivity',
					'2'		=> 'Recommended Exposure Index',
					'3'		=> 'ISO Speed',
					'4'		=> 'Standard Output Sensitivity and Recommended Exposure Index',
					'5'		=> 'Standard Output Sensitivity and ISO Speed',
					'6'		=> 'Recommended Exposure Index and ISO Speed',
					'7'		=> 'Standard Output Sensitivity, Recommended Exposure Index and ISO Speed',
				),
			),
			'ComponentsConfiguration' => array(
				'binary' => true,
				'options' => array(
					'00'	=> '- ',
					'01'	=> 'Y ',
					'02'	=> 'Cb',
					'03'	=> 'Cr',
					'04'	=> 'R ',
					'05'	=> 'G ',
					'06'	=> 'B ',
				),
			),
			'CompressedBitsPerPixel' => array(
				'format'	=> 'rational',
			),
			'ShutterSpeedValue' => array(
				'format'	=> 'rational',
				'suffix'	=> 'APEX'
			),
			'ApertureValue' => array(
				'format'	=> 'rational',
				'suffix'	=> 'APEX'
			),
			'BrightnessValue' => array(
				'format'	=> 'rational',
				'suffix'	=> 'APEX'
			),
			'ExposureBiasValue' => array(
				'format'	=> 'rational',
				'suffix'	=> 'APEX'
			),
			'MaxApertureValue' => array(
				'format'	=> 'rational',
				'suffix'	=> 'APEX'
			),
			'SubjectDistance' => array(
				'format'	=> 'rational',
				'suffix'	=> 'meters'
			),
			'MeteringMode' => array(
				'options' => array(
					'0'		=> 'Unknown',
					'1'		=> 'Average',
					'2'		=> 'Center-weighted average',
					'3'		=> 'Spot',
					'4'		=> 'Multi-spot',
					'5'		=> 'Multi-segment',
					'6'		=> 'Partial',
					'255'	=> 'Other',
				),
			),
			'LightSource' => array(
				'options' => array(
					'0'		=> 'Unknown',
					'1'		=> 'Daylight',
					'2'		=> 'Fluorescent',
					'3'		=> 'Tungsten (Incandescent)',
					'4'		=> 'Flash',
					'9'		=> 'Fine Weather',
					'10'	=> 'Cloudy',
					'11'	=> 'Shade',
					'12'	=> 'Daylight Fluorescent',
					'13'	=> 'Day White Fluorescent',
					'14'	=> 'Cool White Fluorescent',
					'15'	=> 'White Fluorescent',
					'16'	=> 'Warm White Fluorescent',
					'17'	=> 'Standard Light A',
					'18'	=> 'Standard Light B',
					'19'	=> 'Standard Light C',
					'20'	=> 'D55',
					'21'	=> 'D65',
					'22'	=> 'D75',
					'23'	=> 'D50',
					'24'	=> 'ISO Studio Tungsten',
					'255'	=> 'Other',
				),
			),
			'Flash' => array(
				'options' => array(
					'0'		=> 'No Flash',
					'1'		=> 'Fired',
					'5'		=> 'Fired, Return not detected',
					'7'		=> 'Fired, Return detected',
					'8'		=> 'On, Did not fire',
					'9'		=> 'On, Fired',
					'13'	=> 'On, Return not detected',
					'15'	=> 'On, Return detected',
					'16'	=> 'Off, Did not fire',
					'20'	=> 'Off, Did not fire, Return not detected',
					'24'	=> 'Auto, Did not fire',
					'25'	=> 'Auto, Fired',
					'29'	=> 'Auto, Fired, Return not detected',
					'31'	=> 'Auto, Fired, Return detected',
					'32'	=> 'No flash function',
					'48'	=> 'Off, No flash function',
					'65'	=> 'Fired, Red-eye reduction',
					'69'	=> 'Fired, Red-eye reduction, Return not detected',
					'71'	=> 'Fired, Red-eye reduction, Return detected',
					'73'	=> 'On, Red-eye reduction',
					'77'	=> 'On, Red-eye reduction, Return not detected',
					'79'	=> 'On, Red-eye reduction, Return detected',
					'80'	=> 'Off, Red-eye reduction',
					'88'	=> 'Auto, Did not fire, Red-eye reduction',
					'89'	=> 'Auto, Fired, Red-eye reduction',
					'93'	=> 'Auto, Fired, Red-eye reduction, Return not detected',
					'95'	=> 'Auto, Fired, Red-eye reduction, Return detected',
				),
			),
			'FocalLength' => array(
				'format'	=> 'rational',
				'suffix'	=> 'mm',
			),
			/*				//don't convert since already converted by PHP
			   'UserComment' => array(
				   'binary'	=> true,
			   ),*/
			'ColorSpace' => array(
				'options' => array(
					'1'		=> 'sRGB',
					'65533'	=> 'Wide Gamut RGB',
					'65534'	=> 'ICC Profile',
					'65535'	=> 'Uncalibrated',
				),
			),
			'ExifImageWidth' => array(
				'label'		=> 'Image Width',
				'suffix'	=> 'pixels',
			),
			'ExifImageLength' => array(
				'label'		=> 'Image Height',
				'suffix'	=> 'pixels',
			),
			'FlashEnergy'	=> array(
				'format'	=> 'rational',
				'suffix'	=> 'BCPS',
			),
			'FocalPlaneXResolution' => array(
				'format'	=> 'rational',
				'suffix'	=> 'pixels per unit',
			),
			'FocalPlaneYResolution' => array(
				'format'	=> 'rational',
				'suffix'	=> 'pixels per unit',
			),
			'FocalPlaneResolutionUnit' => array(
				'options'	=> array(
					1		=> 'None',
					2		=> 'inch',
					3		=> 'cm',
					4		=> 'mm',
					5		=> 'um',
				),
			),
			'ExposureIndex' => array(
				'format'	=> 'rational',
			),
			'SensingMethod' => array(
				'options'	=> array(
					1		=> 'Not defined',
					2		=> 'One-chip color area',
					3		=> 'Two-chip color area',
					4		=> 'Three-chip color area',
					5		=> 'Color sequential area',
					7		=> 'Trilinear',
					8		=> 'Color sequential linear',
				),
			),
			'FileSource'	=> array(
				'binary'	=> true,
				'options'	=> array(
					'00'	=> 'Others',
					'01'	=> 'Film Scanner ',
					'02'	=> 'Reflection Print Scanner',
					'03'	=> 'Digital Camera',
				),
			),
			'SceneType'		=> array(
				'binary'	=> true,
				'options'	=> array(
					'01'	=> 'Directly photographed',
				),
			),
			'CFAPattern'	=> array(
				'binary'	=> true,
				'options'	=> array(
					'00'	=> 'Red',
					'01'	=> 'Green',
					'02'	=> 'Blue',
					'03'	=> 'Cyan',
					'04'	=> 'Magenta',
					'05'	=> 'Yellow',
					'06'	=> 'White',
				),
			),
			'CustomRendered' => array(
				'options' => array(
					'0'	=> 'Normal',
					'1'	=> 'Custom',
				),
			),
			'ExposureMode' => array(
				'options' => array(
					'0'	=> 'Auto',
					'1'	=> 'Manual',
					'2'	=> 'Auto bracket',
				),
			),
			'WhiteBalance' => array(
				'options' => array(
					'0'	=> 'Auto',
					'1'	=> 'Manual',
				),
			),
			'SceneCaptureType'	=> array(
				'options'	=> array(
					'0'		=> 'Standard',
					'1'		=> 'Landscape',
					'2'		=> 'Portrait',
					'3'		=> 'Night',
				),
			),
			'GainControl' => array(
				'options' => array(
					'0'	=> 'None',
					'1'	=> 'Low gain up',
					'2'	=> 'High gain up',
					'3'	=> 'Low gain down',
					'4'	=> 'High gain down',
				),
			),
			'Contrast' => array(
				'options' => array(
					'0'	=> 'Normal',
					'1'	=> 'Low',
					'2'	=> 'High',
				),
			),
			'Saturation' => array(
				'options' => array(
					'0'	=> 'Normal',
					'1'	=> 'Low',
					'2'	=> 'High',
				),
			),
			'Sharpness' => array(
				'options' => array(
					'0'	=> 'Normal',
					'1'	=> 'Soft',
					'2'	=> 'Hard',
				),
			),
			'SubjectDistanceRange' => array(
				'options' => array(
					'0'	=> 'Unknown',
					'1'	=> 'Macro',
					'2'	=> 'Close',
					'3'	=> 'Distant',
				),
			),
			//some legitimate exif fields are apparently only partially handled by php
			//an undefined tag is returned instead of the label
			'UndefinedTag:0xA431' => array(
				'label'	=> 'Serial Number',
			),
			'UndefinedTag:0xA432' => array(
				'label'	=> 'Lens Specification',
				'suffix'=> 'Min - max focal length in mm; Min - max Fnumber'
			),
			'UndefinedTag:0xA434' => array(
				'label'	=> 'Lens Model',
			),
		),
		'GPS' => array(
			'GPSVersion' => array(
				'binary'	=> true,
			),
			'GPSLatitudeRef' => array(
				'options' => array(
					'N'	=> 'North (+)',
					'S'	=> 'South (-)',
				),
			),
			'GPSLatitude' => array(
				'format'	=> 'rational',
			),
			'GPSLongitudeRef' => array(
				'options' => array(
					'E'	=> 'East (+)',
					'W'	=> 'West (-)',
				),
			),
			'GPSLongitude' => array(
				'format'	=> 'rational',
			),
			'GPSAltitudeRef' => array(
				'binary' => true,
				'options' => array(
					'00'	=> 'Meters above sea level',
					'01'	=> 'Meters below sea level',
				),
			),
			'GPSAltitude' => array(
				'format'	=> 'rational',
			),
			'GPSTimeStamp' => array(
				'format'	=> 'rational',
				'suffix'	=> '(24-hour clock)',
			),
			'GPSStatus' => array(
				'options' => array(
					'A'	=> 'Measurement Active',
					'V'	=> 'Measurement Void',
				),
			),
			'GPSMeasureMode' => array(
				'options' => array(
					'2'	=> '2-Dimensional Measurement',
					'3'	=> '3-Dimensional Measurement',
				),
			),
			'GPSSpeedRef' => array(
				'options' => array(
					'K'	=> 'km/h',
					'M'	=> 'mph',
					'N'	=> 'knots',
				),
			),
			'GPSSpeed' => array(
				'format'	=> 'rational',
			),
			'GPSTrackRef' => array(
				'options' => array(
					'M'	=> 'Magnetic North',
					'T'	=> 'True North',
				),
			),
			'GPSTrack' => array(
				'format'	=> 'rational',
			),
			'GPSImgDirectionRef' => array(
				'options' => array(
					'M'	=> 'Magnetic North',
					'T'	=> 'True North',
				),
			),
			'GPSImgDirection' => array(
				'format'	=> 'rational',
			),
			'GPSDifferential' => array(
				'options' => array(
					'0'	=> 'No Corection',
					'T'	=> 'Differential Corrected',
				),
			),
			'GPSDestLatitudeRef' => array(
				'options' => array(
					'N'	=> 'North (+)',
					'S'	=> 'South (-)',
				),
			),
			'GPSDestLatitude' => array(
				'format'	=> 'rational',
			),
			'GPSDestLongitudeRef' => array(
				'options' => array(
					'E'	=> 'East (+)',
					'W'	=> 'West (-)',
				),
			),
			'GPSDestLongitude' => array(
				'format'	=> 'rational',
			),
			'GPSDestBearingRef' => array(
				'options' => array(
					'M'	=> 'Magnetic North',
					'T'	=> 'True North',
				),
			),
			'GPSDestBearing' => array(
				'format'	=> 'rational',
			),
			'GPSDestDistanceRef' => array(
				'options' => array(
					'K'	=> 'Kilometers',
					'M'	=> 'Miles',
					'N'	=> 'Nautical Miles',
				),
			),
			'GPSDestDistance' => array(
				'format'	=> 'rational',
			),
			'GPSHPositioningError' => array(
				'format'	=> 'rational',
			),
		),
	);

	/**
	 * Process raw EXIF data by converting binary or hex information, replacing legend codes with their meanings,
	 * fixing labels, etc.
	 *
	 * @param		array		$exifraw		Array of raw EXIF data
	 *
	 * @return 		array		$exif			Array of processed EXIF data, including label, newval and suffix values
	 * 												for each field
	 */
	function processRawData($exifraw)
	{
		$filter   = new Zend\Filter\Word\CamelCaseToSeparator();
		//array of tags to match exif array from file
		foreach ($exifraw as $group => $fields) {
			foreach ($fields as $name => $field) {
				if (isset($field)) {
					//store raw value
					$exif[$group][$name]['rawval'] = $field;

					//thumbnail and ifd0 groups share the same specifications
					$groupmask = $group == 'THUMBNAIL' ? 'IFD0' : $group;

					//get tag data from $specs array
					if (isset($this->specs[$groupmask][$name])) {
						//shorten the variable
						$specname = $this->specs[$groupmask][$name];

						//convert binary values
						if (isset($specname['binary']) && $specname['binary']) {
							$exif[$group][$name]['rawval'] = bin2hex($exif[$group][$name]['rawval']);
						}

						//start processing rawval into newval
						if (is_array($exif[$group][$name]['rawval'])) {
							$exif[$group][$name]['newval'] = $this->processArray($exif[$group][$name]['rawval'], $name);
						//perform division for rational fields, but only if not an array
						} elseif (isset($specname['format']) && $specname['format'] == 'rational') {
							$exif[$group][$name]['newval'] = $this->divide($field);
						//move rest of rawvals into newvals
						} else {
							$exif[$group][$name]['newval'] = $exif[$group][$name]['rawval'];
						}

						//now determine display values using option values where they exist
						if (isset($specname['options'])) {
							//first handle special cases
							if ($name == 'ComponentsConfiguration') {
								$str = $exif[$group][$name]['newval'];
								$opt = $specname['options'];
								$disp = $opt[substr($str, 0, 2)] . ' ' . $opt[substr($str, 2, 2)] . ' '
										. $opt[substr($str, 4, 2)] . ' ' . $opt[substr($str, 6, 2)];
								$exif[$group][$name]['newval'] = $disp;
							} elseif ($name == 'CFAPattern') {
								$str = $exif[$group][$name]['newval'];
								$opt = $specname['options'];
								$disp = '[' . $opt[substr($str, 8, 2)] . ', ' . $opt[substr($str, 10, 2)] . '] ['
										. $opt[substr($str, 12, 2)] . ', ' . $opt[substr($str, 14, 2)] . ']';
								$exif[$group][$name]['newval'] =$disp;
							} else {
								$exif[$group][$name]['newval'] = $specname['options'][$exif[$group][$name]['newval']];
							}
						}

						//fix labels
						if (isset($specname['label'])) {
							$exif[$group][$name]['label'] = $specname['label'];
						} else {
							//create reading-friendly labels from camel case tags
							$exif[$group][$name]['label'] = $filter->filter($name);
						}

						if (isset($specname['suffix']) && !is_array($exif[$group][$name]['newval'])) {
							$exif[$group][$name]['suffix'] = $specname['suffix'];
						}
					} else {
						//those not covered in $specs
						$exif[$group][$name]['newval'] = $exif[$group][$name]['rawval'];
						//create reading-friendly labels from camel case tags
						$exif[$group][$name]['label'] = $filter->filter($name);
					}
				}
			}
		}
		//*******Special Handling*********//
		//file name is computed by PHP and is meaningless when file is stored in tiki,
		//and dialog box has real name in title so not needed
		unset($exif['FILE']['FileName']);
		//file date is also computed by PHP and represents the time the metadata was extracted. This data is included
		//elsewhere and is not needed here
		unset($exif['FILE']['FileDateTime']);
		//No processing of maker notes yet as specific code is needed for each manufacturer
		//Blank out field since it is very long and will distort the dialog box
		if (!empty($exif['EXIF']['MakerNote']['newval'])) {
			$exif['EXIF']['MakerNote']['newval'] = '(Not processed)';
			unset($exif['EXIF']['MakerNote']['rawval']);
		}
		if (isset($exif['MAKERNOTE'])) {
			$exif['MAKERNOTE'] = "";
			$exif['MAKERNOTE']['Note']['label'] = "";
			$exif['MAKERNOTE']['Note']['newval'] = "(Not processed)";
		}
		//Interpret GPSVersion field
		if (isset($exif['GPS']['GPSVersion'])) {
			$exif['GPS']['GPSVersion']['newval'] = '';
			$len = strlen($exif['GPS']['GPSVersion']['rawval']);
			for ($i = 0; $i < $len; $i = $i + 2) {
				if ($i > 0) {
					$exif['GPS']['GPSVersion']['newval'] .= '.';
				}
				$exif['GPS']['GPSVersion']['newval'] .= (int) substr($exif['GPS']['GPSVersion']['rawval'], $i, 2);
			}
		}
		//PHP already converts UserComment in the Computed group so use that value
		if (isset($exif['EXIF']['UserComment']) && isset($exif['COMPUTED']['UserComment'])) {
			$exif['EXIF']['UserComment']['newval'] = $exif['COMPUTED']['UserComment']['rawval'];
			unset($exif['COMPUTED']['UserComment']);
		}
		//PHP already converts the FNumber in the Computed group so use that value
		if (isset($exif['EXIF']['FNumber']) && isset($exif['COMPUTED']['ApertureFNumber'])) {
			$exif['EXIF']['FNumber']['newval'] = $exif['COMPUTED']['ApertureFNumber']['rawval'];
			unset($exif['COMPUTED']['ApertureFNumber']);
		}

		return $exif;
	}

	/**
	 * Perform division on values in a rational format, e.g. '100/5'. Accepts arrays or single values
	 *
	 * @param 		string or array		$fractionString
	 *
	 * @return 		array|float
	 */
	function divide($fractionString)
	{
		if (!is_array($fractionString)) {
			$fraction = explode('/', $fractionString);
			$ret = $fraction[0] / $fraction[1];
		} else {
			foreach ($fractionString as $fs) {
				$fraction = explode('/', $fs);
				$ret[] = $fraction[0] / $fraction[1];
			}
		}
		return $ret;
	}

	/**
	 * Deal with field values that are arrays, giving unique treatment to certain unique fields, otherwise
	 * converting to a string
	 *
	 * @param 		array		$array			field value
	 * @param 		string		$fieldname		field name used to identify where unique treatment should be applied
	 *
	 * @return		string		$ret			field array converted into a string
	 */
	function processArray ($array, $fieldname)
	{
		$ret = '';
		if ($fieldname == 'GPSLatitude' || $fieldname == 'GPSLongitude') {
			$calcarray = $this->divide($array);
			$ret = $calcarray[0] + (($calcarray[1] + ($calcarray[2] / 60)) / 60);
		} elseif ($fieldname == 'GPSTimeStamp') {
			$array = $this->divide($array);
			$ret = $array[0] . ':' . $array[1] . ':' . $array[2];
		} elseif ($fieldname == 'UndefinedTag:0xA432') {
			$array = $this->divide($array);
			$ret = $array[0] . ' - ' . $array[1] . '; ' . $array[2] . ' - ' .  $array[3];
		} else {
			foreach ($array as $value) {
				$ret .= $value . '; ';
			}
			$ret .= tra('(values not interpreted)');
		}
		return $ret;
	}
}
