<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-export_pdf.php,v 1.20.2.2 2007-11-08 02:01:31 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//include_once("tiki-setup_base.php");
include_once ("tiki-setup.php");

if ($prefs['feature_wiki_pdf'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_wiki_pdf");
	$smarty->display("error.tpl");
	die;
}

check_ticket('pdf');

require_once ('lib/html2pdf/config.inc.php');
require_once('lib/html2pdf/pipeline.factory.class.php');

/**
 * Handles the saving generated PDF to user-defined output file on server
 */
class MyDestinationFile extends Destination {
  /**
   * @var String result file name / path
   * @access private
   */
  var $_dest_filename;

  function MyDestinationFile($dest_filename) {
    $this->_dest_filename = $dest_filename;
  }

  function process($tmp_filename, $content_type) {
    copy($tmp_filename, $this->_dest_filename);
  }
}

class MyFetcherLocalFile extends Fetcher {
  var $_content;

  function MyFetcherLocalFile($file) {
    $this->_content = file_get_contents($file);
  }

  function get_data($dummy1) {
    return new FetchedDataURL($this->_content, array(), "");
  }

  function get_base_url() {
    return "file:///C:/rac/html2ps/test/";
  }

}

//
//Default settings for html2pdf
//

// Works only with safe mode off; in safe mode it generates a warning message
@set_time_limit(-1);

// Title of styleshee to use (empty if no preferences are set)
if(!isset($_REQUEST['renderforms'])){
	$_REQUEST['renderforms'] = '';
}
$g_config = array(
                  'cssmedia'      => isset($_REQUEST['cssmedia']) ? $_REQUEST['cssmedia'] : "screen",
                  'convert'       => isset($_REQUEST['convert']),
                  'media'         => isset($_REQUEST['media']) ? $_REQUEST['media'] : "A4",
                  'scalepoints'   => $_REQUEST['scalepoints'],
                  'renderimages'  => isset($_REQUEST['renderimages']) ? $_REQUEST['renderimages'] : 1,
                  'renderfields'  => isset($_REQUEST['renderfields']),
                  'renderforms'   => $_REQUEST['renderforms'],
                  'pslevel'       => isset($_REQUEST['pslevel']) ? $_REQUEST['pslevel'] : 2,
                  'renderlinks'   => $_REQUEST['renderlinks'],
                  'pagewidth'     => isset($_REQUEST['pixels']) ? (int)$_REQUEST['pixels'] : 800,
                  'landscape'     => $_REQUEST['landscape'],
                  'method'        => isset($_REQUEST['method']) ? $_REQUEST['method'] : "fpdf" ,
                  'margins'       => array(
                                           'left'   => isset($_REQUEST['leftmargin'])   ? (int)$_REQUEST['leftmargin']   : 15,
                                           'right'  => isset($_REQUEST['rightmargin'])  ? (int)$_REQUEST['rightmargin']  : 15,
                                           'top'    => isset($_REQUEST['topmargin'])    ? (int)$_REQUEST['topmargin']    : 15,
                                           'bottom' => isset($_REQUEST['bottommargin']) ? (int)$_REQUEST['bottommargin'] : 15
                                           ),
                  'encoding'      => isset($_REQUEST['encoding']) ? $_REQUEST['encoding'] : "",
                  'ps2pdf'        => isset($_REQUEST['ps2pdf'])   ? $_REQUEST['ps2pdf']   : 0,
                  'compress'      => isset($_REQUEST['compress']) ? $_REQUEST['compress'] : 0,
                  'output'        => isset($_REQUEST['output']) ? $_REQUEST['output'] : 0,
                  'pdfversion'    => isset($_REQUEST['pdfversion']) ? $_REQUEST['pdfversion'] : "1.2",
                  'transparency_workaround' => isset($_REQUEST['transparency_workaround']),
                  'imagequality_workaround' => isset($_REQUEST['imagequality_workaround']),
                  'draw_page_border'        => $_REQUEST['pageborder'],
                  'debugbox'      => isset($_REQUEST['debugbox']),
                  'html2xhtml'    => !isset($_REQUEST['html2xhtml']),
                  'mode'          => 'html'
                  );

parse_config_file('lib/html2pdf/html2ps.config');
//
//End of default settings for html2pdf
//

if (!isset($_REQUEST["convertpages"])) {
	$convertpages = array();

	if (isset($_REQUEST["page"]) && $tikilib->page_exists($_REQUEST["page"])) {
		$convertpages[] = $_REQUEST["page"];
	}
} else {
	$convertpages = unserialize(urldecode($_REQUEST['convertpages']));
}

//Adding header to PDF page /only in the first page/ basically it could be the same as header.tpl
//The reason: if you want sustom header or different ofnts, colors etc - use different CSS file and header image

if(is_file("templates/header-pdf.tpl")){
	$data = $smarty->fetch("header-pdf.tpl");
} else {
	$data = $smarty->fetch("header.tpl");
}

//Fetchinf the data in HTML and put it into a temp file
foreach (array_values($convertpages)as $page) {
	$tikilib->get_perm_object($page, 'wiki page', $info, true);
	if ($tiki_p_view != 'y') {
		$smarty->assign('msg', tra("Permission denied you cannot view this page"));
		$smarty->display("error.tpl");
		die;
	}

	if(isset($_REQUEST["page_ref_id"])){
		$page_ref_id = $structlib->get_struct_ref_id($page);
		
		$page_info = $structlib->s_get_page_info($page_ref_id);
		
		$structure = 'y';
    $smarty->assign('structure',$structure);
    $page_info = $structlib->s_get_page_info($page_ref_id);
    
    $smarty->assign('page_info', $page_info);

    $structure_path = $structlib->get_structure_path($page_ref_id);
    $smarty->assign('structure_path', $structure_path);
		
	}
	$info = $tikilib->get_page_info($page);
	if($tikilib->user_has_perm_on_object($user,$page,'wiki page','tiki_p_view')) {
	  $data .= $tikilib->parse_data($info["data"]);
	} else {
	   $data .= tra("No permission to view the page")."<br />\n";
	}
}

//saving the HTML file in the temp directory
$filename = md5(rand().time()).".html";
//File write operations
if (!$fp = fopen("temp/".$filename, 'a')) {
	echo "Cannot open file for PDF generation - check ./temp/ dir or file:($filename)";
	die();
}

// Write $somecontent to our opened file.
if (fwrite($fp, $data) === FALSE) {
	echo "Cannot write to file - check ./temp/ dir or file:($filename)";
	die();
}
fclose($fp);
chmod("temp/$filename", 0644); // seems necessary on some systems with suphp security module from apache installed


//Getting url for parsing
$g_baseurl = "http://".$_REQUEST["HTTP_HOST"].str_replace("tiki-export_pdf.php","",$_REQUEST["SCRIPT_NAME"])."tiki-export_pdf_reader.php?file=".$filename;

//
// HTML2PDF
//
// code snipet from html2pdf lib

$g_media = Media::predefined($g_config['media']);
$g_media->set_landscape($g_config['landscape']);
$g_media->set_margins($g_config['margins']);
$g_media->set_pixels($g_config['pagewidth']);

$g_px_scale = mm2pt($g_media->width() - $g_media->margins['left'] - $g_media->margins['right']) / $g_media->pixels;
if ($g_config['scalepoints']) {
  $g_pt_scale = $g_px_scale * 1.43; // This is a magic number, just don't touch it, or everything will explode!
} else {
  $g_pt_scale = 1.0;
};

// Initialize the coversion pipeline
$pipeline = PipelineFactory::create_default_pipeline("", // Attempt to auto-detect encoding
                                                       "");

// Configure the fetchers
$pipeline->fetchers[] = new MyFetcherLocalFile("temp/".$filename);

// Configure the data filters
$pipeline->data_filters[] = new DataFilterDoctype();
$pipeline->data_filters[] = new DataFilterUTF8($g_config['encoding']);
if ($g_config['html2xhtml']) {
  $pipeline->data_filters[] = new DataFilterHTML2XHTML();
} else {
  $pipeline->data_filters[] = new DataFilterXHTML2XHTML();
};

$pipeline->parser = new ParserXHTML();

$pipeline->pre_tree_filters = array();
if ($g_config['renderfields']) {
  $pipeline->pre_tree_filters[] = new PreTreeFilterHTML2PSFields("","","");
};

if ($g_config['method'] === 'ps') {
  $pipeline->layout_engine = new LayoutEnginePS();
} else {
  $pipeline->layout_engine = new LayoutEngineDefault();
};

$pipeline->post_tree_filters = array();

// Configure the output format
if ($g_config['pslevel'] == 3) {
  $image_encoder = new PSL3ImageEncoderStream();
} else {
  $image_encoder = new PSL2ImageEncoderStream();
};

switch ($g_config['method']) {
 case 'ps':
   $pipeline->output_driver = new OutputDriverPS($g_config['scalepoints'],
                                                 $g_config['transparency_workaround'],
                                                 $g_config['imagequality_workaround'],
                                                 $image_encoder);
   break;
 case 'fastps':
   $pipeline->output_driver = new OutputDriverFastPS($image_encoder);
   break;
 case 'pdflib':
   $pipeline->output_driver = new OutputDriverPDFLIB($g_config['pdfversion']);
   break;
 case 'fpdf':
   $pipeline->output_driver = new OutputDriverFPDF();
   break;
 default:
   die("Unknown output method");
};

if ($g_config['debugbox']) {
  $pipeline->output_driver->set_debug_boxes(true);
}

if ($g_config['draw_page_border']) {
  $pipeline->output_driver->set_show_page_border(true);
}

if ($g_config['ps2pdf']) {
  $pipeline->output_filters[] = new OutputFilterPS2PDF($g_config['pdfversion']);
}

if ($g_config['compress']) {
  $pipeline->output_filters[] = new OutputFilterGZip();
}

switch ($g_config['output']) {
 case 0:
   $pipeline->destination = new DestinationBrowser($g_baseurl);
   break;
 case 1:
   $pipeline->destination = new DestinationDownload($g_baseurl);
   break;
 case 2:
   $pipeline->destination = new DestinationFile($g_baseurl);
   break;
};
// Start the conversion
$status = $pipeline->process($g_baseurl, $g_media);
if ($status == null) {
  print($pipeline->error_message());
  error_log("Error in conversion pipeline");
  die();
}
// end code snipet from html2pdf lib

//unlink temp file when it's ready
unlink("temp/".$filename);
?>
