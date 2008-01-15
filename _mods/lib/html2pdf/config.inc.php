<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/config.inc.php,v 1.1 2008-01-15 09:20:29 mose Exp $

$fonts_dir = 'lib/html2pdf/fonts/';

define('EPSILON',0.001);

define('DEFAULT_USER_AGENT',"Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7) Gecko/20040803 Firefox/0.9.3");

define('DEFAULT_SUBMIT_TEXT','Submit');
define('DEFAULT_RESET_TEXT' ,'Reset');
define('DEFAULT_BUTTON_TEXT','Send request');

define('CHECKBOX_SIZE','15px');

define('BROKEN_IMAGE_DEFAULT_SIZE_PX',24);
define('BROKEN_IMAGE_ALT_SIZE_PT',10);

define('DEFAULT_TEXT_SIZE',20);

// Horizontal offset of a legend box (points, virtual)
define('LEGEND_HORIZONTAL_OFFSET','5pt');

define('BULLET_SIZE_KOEFF',0.15);
define('HEIGHT_KOEFF',0.7);

define('MAX_FRAME_NESTING_LEVEL',4);

define('RADIOBUTTON_SIZE','15px');

define('SELECT_BUTTON_TRIANGLE_PADDING',1.5);

define('MAX_JUSTIFY_FRACTION',0.33);

define('HILIGHT_COLOR_ALPHA',0.6);

define('BASE_FONT_SIZE_PT',11);

define('MAX_REDIRECTS',5);

define("FONT_DIR",$fonts_dir);

// Directory where cached files should be stored
// BACKSLASH AT THE END REQUIRED
define('CACHE_DIR','temp/cache/');

define('OUTPUT_FILE_ALLOWED',true);
// Note you'll need to create this directory manually
define('OUTPUT_FILE_DIRECTORY','./out');
define('OUTPUT_DEFAULT_NAME','unnamed');

define('IMAGE_MAGICK_CONVERT_EXECUTABLE',"C:\\Program Files\\ImageMagick-6.0.0-Q16\\convert.exe");

// Maximal length of line inside the stream data
// (we need to limit this, as most postscript interpreters will complain
// on long strings)
//
// Note it is measured in BYTES! Each byte will be represented by TWO characters
// in the hexadecimal form
//
define("MAX_LINE_LENGTH", 100);

define('MAX_IMAGE_ROW_LEN',16);
define('MAX_TRANSPARENT_IMAGE_ROW_LEN',16);

define('SIZE_SPACE_KOEFF',1.5);

define('EM_KOEFF',1);
define('EX_KOEFF',0.60);

// Note that WRITER_TEMPDIR !REQUIRES! slash on the end (unless you want to get
// some files like tempPS_jvckxlvjl in your working directory).
define('WRITER_TEMPDIR','./temp/');
define('WRITER_FILE_PREFIX','PS_');

// number of retries to generate unique filename in case we have had troubles with
// tempnam function
define('WRITER_RETRIES',10);
define('WRITER_CANNOT_CREATE_FILE',"Cannot create unique temporary filename, sorry");

// Path to ps2pdf
// define('GS_PATH','c:\gs\gs8.51\bin\gswin32c.exe');
// define('GS_PATH','c:\gs\gs8.14\bin\gswin32c.exe');
// define('GS_PATH','c:\gs\gs7.05\bin\gswin32c.exe');
define('GS_PATH','gs');

define('PDFLIB_DL_PATH','../html2ps/pdflib.so');

// This variable defines the path to PDFLIB configuration file; in particular, it contains
// information about the supported encodings.
//
// define('PDFLIB_UPR_PATH',"c:/php/php4.4.0/pdf-related/pdflib.upr");
// define('PDFLIB_UPR_PATH',"c:/php/pdf-related/pdflib.upr");

// Path to directory containing fonts used by PDFLIB
// Trailing backslash required
// Default value: the path where the script is executed + '/fonts/'
//$basepath = dirname(isset($_SERVER['PATH_TRANSLATED']) ? $_SERVER['PATH_TRANSLATED'] : "");
//if ($basepath == '') { $basepath = 'lib/html2pdf'; }
define('PDFLIB_TTF_FONTS_REPOSITORY',$fonts_dir);

// Trailing backslash required
define('TYPE1_FONTS_REPOSITORY',$fonts_dir);

define('FPDF_PATH','./fpdf/');

define('DEFAULT_CHAR_WIDTH', 600);

define('WHITESPACE_FONT_SIZE_FRACTION', 0.25);

define('DEFAULT_ENCODING', 'iso-8859-1');

?>
