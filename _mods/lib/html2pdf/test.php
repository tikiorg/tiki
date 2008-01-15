<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/test.php,v 1.1 2008-01-15 09:21:14 mose Exp $

// Works only with safe mode off; in safe mode it generates a warning message
@set_time_limit(1800);

require('config.inc.php');

require('utils_array.php');
require('utils_graphic.php');
require('utils_url.php');
require('utils_text.php');
require('utils_units.php');
require('utils_number.php');

require('color.php');

require('config.parse.php');
require('systemcheck.php');

require('flow_context.class.inc.php');
require('flow_viewport.class.inc.php');

require('output._interface.class.php');
require('output._generic.class.php');
require('output._generic.pdf.class.php');
require('output._generic.ps.class.php');
require('output.ps.class.php');
require('output.pdflib.class.php');
require('output.fpdf.class.php');
require('output.fastps.class.php');

require('stubs.common.inc.php');

require('media.layout.inc.php');

require('box.php');
require('box.generic.php');
require('box.container.php');
require('box.generic.inline.php');
require('box.inline.php');
require('box.inline.control.php');

require('font.class.php');
require('font_factory.class.php');

require('box.br.php');
require('box.block.php');
require('box.block.inline.php');
require('box.button.php');
require('box.checkbutton.php');
require('box.frame.php');
require('box.iframe.php');
require('box.input.text.php');
require('box.legend.php');
require('box.list-item.php');
require('box.null.php');
require('box.radiobutton.php');
require('box.select.php');
require('box.table.php');
require('box.table.cell.php');
require('box.table.row.php');
require('box.table.section.php');

require('box.text.php');
require('box.field.pageno.php');
require('box.field.pages.php');
require('box.whitespace.php');
require('box.img.php');

require('box.utils.text-align.inc.php');

require('manager.encoding.php');
require('encoding.inc.php');
require('encoding.entities.inc.php');
require('encoding.glyphs.inc.php');
require('encoding.iso-8859-1.inc.php');
require('encoding.iso-8859-2.inc.php');
require('encoding.iso-8859-3.inc.php');
require('encoding.iso-8859-4.inc.php');
require('encoding.iso-8859-5.inc.php');
require('encoding.iso-8859-7.inc.php');
require('encoding.iso-8859-9.inc.php');
require('encoding.iso-8859-10.inc.php');
require('encoding.iso-8859-11.inc.php');
require('encoding.iso-8859-13.inc.php');
require('encoding.iso-8859-14.inc.php');
require('encoding.iso-8859-15.inc.php');
require('encoding.koi8-r.inc.php');
require('encoding.cp866.inc.php');
require('encoding.windows-1250.inc.php');
require('encoding.windows-1251.inc.php');
require('encoding.windows-1252.inc.php');
require('encoding.dingbats.inc.php');
require('encoding.symbol.inc.php');

require('ps.unicode.inc.php');
require('ps.utils.inc.php');
require('ps.whitespace.inc.php');
require('ps.text.inc.php');

require('ps.image.encoder.inc.php');
require('ps.image.encoder.simple.inc.php');
require('ps.l2.image.encoder.stream.inc.php');
require('ps.l3.image.encoder.stream.inc.php');
require('ps.image.encoder.imagemagick.inc.php');

require('tag.body.inc.php');
require('tag.font.inc.php');
require('tag.frame.inc.php');
require('tag.input.inc.php');
require('tag.img.inc.php');
require('tag.select.inc.php');
require('tag.span.inc.php');
require('tag.table.inc.php');
require('tag.td.inc.php');
require('tag.utils.inc.php');
require('tag.ulol.inc.php');

require('tree.navigation.inc.php');

require('html.attrs.inc.php');
require('html.list.inc.php');

require('xhtml.autoclose.inc.php');
require('xhtml.utils.inc.php');
require('xhtml.tables.inc.php');
require('xhtml.p.inc.php');
require('xhtml.lists.inc.php');
require('xhtml.deflist.inc.php');
require('xhtml.script.inc.php');
require('xhtml.entities.inc.php');
require('xhtml.comments.inc.php');
require('xhtml.style.inc.php');
require('xhtml.selects.inc.php');

require('background.php');
require('background.image.php');
require('background.position.php');

require('height.php');
require('width.php');

require('css.inc.php');
require('css.utils.inc.php');
require('css.parse.inc.php');
require('css.parse.media.inc.php');
require('css.apply.inc.php');

require('css.background.color.inc.php');
require('css.background.image.inc.php');
require('css.background.repeat.inc.php');
require('css.background.position.inc.php');
require('css.background.inc.php');

require('css.border.inc.php');
require('css.border.style.inc.php');
require('css.border.collapse.inc.php');
require('css.bottom.inc.php');
require('css.clear.inc.php');
require('css.color.inc.php');
require('css.colors.inc.php');
require('css.content.inc.php');
require('css.display.inc.php');
require('css.float.inc.php');
require('css.font.inc.php');
require('css.height.inc.php');
require('css.left.inc.php');
require('css.line-height.inc.php');

require('css.list-style-image.inc.php');
require('css.list-style-position.inc.php');
require('css.list-style-type.inc.php');
require('css.list-style.inc.php');

require('css.margin.inc.php');
require('css.overflow.inc.php');
require('css.padding.inc.php');

require('css.page-break.inc.php');
require('css.page-break-after.inc.php');

require('css.position.inc.php');
require('css.right.inc.php');
require('css.rules.inc.php');
require('css.selectors.inc.php');
require('css.text-align.inc.php');
require('css.text-decoration.inc.php');
require('css.text-indent.inc.php');
require('css.top.inc.php');
require('css.vertical-align.inc.php');
require('css.visibility.inc.php');
require('css.white-space.inc.php');
require('css.width.inc.php');
require('css.z-index.inc.php');

require('css.pseudo.add.margin.inc.php');
require('css.pseudo.align.inc.php');
require('css.pseudo.cellspacing.inc.php');
require('css.pseudo.cellpadding.inc.php');
require('css.pseudo.link.destination.inc.php');
require('css.pseudo.link.target.inc.php');
require('css.pseudo.listcounter.inc.php');
require('css.pseudo.localalign.inc.php');
require('css.pseudo.nowrap.inc.php');
require('css.pseudo.table.border.inc.php');

// After all CSS utilities and constants have been initialized, load the default (precomiled) CSS stylesheet
require('css.defaults.inc.php');

require('localalign.inc.php');

require('converter.class.php');

require('treebuilder.class.php');
require('image.class.php');

require('anchor.inc.php');

require('fetched_data._interface.class.php');
require('fetched_data._html.class.php');
require('fetched_data.url.class.php');
require('fetched_data.file.class.php');

require('fetcher._interface.class.php');
require('fetcher.url.class.php');
require('fetcher.local.class.php');

require('filter.data._interface.class.php');
require('filter.data.doctype.class.php');
require('filter.data.utf8.class.php');
require('filter.data.html2xhtml.class.php');
require('filter.data.xhtml2xhtml.class.php');

require('parser._interface.class.php');
require('parser.xhtml.class.php');

require('filter.pre._interface.class.php');
require('filter.pre.fields.class.php');
require('filter.pre.headfoot.class.php');

require('layout._interface.class.php');
require('layout.default.class.php');
require('layout.ps.class.php');

require('filter.post._interface.class.php');

require('filter.output._interface.class.php');
require('filter.output.ps2pdf.class.php');
require('filter.output.gzip.class.php');

require('destination._interface.class.php');
require('destination._http.class.php');
require('destination.browser.class.php');
require('destination.download.class.php');
require('destination.file.class.php');

require('pipeline.class.php');
require('pipeline.factory.class.php');

require('dom.php5.inc.php');
require('dom.activelink.inc.php');

require('content_type.class.php');

require('xml.validation.inc.php');

$g_css_index        = 0;

// $g_image_encoder = new PSL3ImageEncoderStream();
$g_image_encoder = new PSL2ImageEncoderStream();

// Title of styleshee to use (empty if no preferences are set)
$g_stylesheet_title = "";

$g_config = array(
                  'cssmedia'      => "screen",
                  'convert'       => true,
                  'media'         => "A4",
                  'scalepoints'   => true,
                  'renderimages'  => true,
                  'renderlinks'   => true,
                  'pagewidth'     => 1024,
                  'landscape'     => false,
                  'method'        => 'fpdf',
                  'margins'       => array(
                                           'left'   => 10,
                                           'right'  => 10,
                                           'top'    => 10,
                                           'bottom' => 10
                                           ),
                  'encoding'      => 'iso-8859-1',
                  'ps2pdf'        => false,
                  'compress'      => false,
                  'output'        => 2,
                  'pdfversion'    => "1.3",
                  'transparency_workaround' => false,
                  'imagequality_workaround' => false,
                  'draw_page_border' => false,
                  'debugbox'      => false,
                  'html2xhtml'    => true
                  );

                  // ========== Entry point
                  parse_config_file('./.html2ps.config');


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
$pipeline = new Pipeline();

// Configure the fetchers
$pipeline->fetchers[] = new FetcherURL();

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
if ($g_config['method'] === 'ps') {
  $pipeline->layout_engine = new LayoutEnginePS();
} else {
  $pipeline->layout_engine = new LayoutEngineDefault();
};
$pipeline->post_tree_filters = array();

// Configure the output format
switch ($g_config['method']) {
 case 'ps':
   $pipeline->output_driver = new OutputDriverPS($g_config['scalepoints'],
                                                 $g_config['transparency_workaround'], 
                                                 $g_config['imagequality_workaround']); 
   break;
 case 'fastps':
   $pipeline->output_driver = new OutputDriverFastPS(); 
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

$urls = array(
/*              'http://247realmedia.com',
          //              'http://888.com',
              'http://abetterinternet.com',
              'http://alphadg.com',
              'http://aol.com',
              'http://bbc.co.uk',
              'http://benews.net',
              'http://casalemedia.com',
              'http://cnn.com',
              'http://cra-arc.gc.ca/menu-e.html',
              'http://crux.nu',
              'http://cs.wisc.edu/~ghost/',
              'http://download.com',
              'http://ebay.com',
              'http://ewizard.com',
              'http://exactsearch.net',
              'http://exitexchange.com',
              'http://www.falkag.net',           
              'http://geocities.com',
              'http://go.com',                   
              'http://google.com/about.html',
              'http://google.com/froogle',
              'http://google.com/services/',
              'http://hamster.sazco.net',
              'http://internet-optimizer.com',
              'http://jakpsatweb.cz/css/css-vertical-center-solution.html',
              'http://johnlewis.com', 
              'http://microsoft.com',
              'http://msn.com',
              'http://myblog.de',
              'http://myway.com',
              'http://mywebsearch.com', 
              'http://net-offers.net',  
              'http://netscape.com',
              'http://netvenda.com',
              'http://offeroptimizer.com',
              'http://onet.pl',
              'http://papajohns.com',     
              'http://partypoker.com',    // !!
              'http://passport.com',    
              'http://php.net',
              'http://pilger.carlton.com',
              'http://python.org/~guido/',
              'http://realmedia.com',
              'http://rentacoder.com',
              'http://revenue.net',       // 
              'http://sage.com/local/regionNorthAmerica.aspx',
              'http://www.searchscout.com',
              'http://smarty.php.net',
              'http://stallman.org',
              'http://thefacebook.com',
              'http://tickle.com',      
              'http://trafficmp.com',
              'http://tufat.com',
              'http://user.it.uu.se/~jan/html2ps.html',
              'http://vianet.com.pl',   
              'http://whenu.com',       */
              'http://whitehouse.gov',
              'http://yahoo.com',
              'http://zango.com' // !!
              );

foreach ($urls as $url) {
  $g_baseurl = $url;

  $pipeline->destination->set_filename($g_baseurl);
  $status = $pipeline->process($g_baseurl, $g_media);
  print("<br/>");
  print("Processed $url<br/>");
  if ($status == null) {
    print($pipeline->error_message());
    print("Error in conversion pipeline<br/>");
  };
};

?>