<?php

require_once('config.inc.php');

require_once('utils_array.php');
require_once('utils_graphic.php');
require_once('utils_url.php');
require_once('utils_text.php');
require_once('utils_units.php');
require_once('utils_number.php');

require_once('color.php');

require_once('config.parse.php');
require_once('systemcheck.php');

require_once('flow_context.class.inc.php');
require_once('flow_viewport.class.inc.php');

require_once('output._interface.class.php');
require_once('output._generic.class.php');
require_once('output._generic.pdf.class.php');
require_once('output._generic.ps.class.php');
require_once('output.ps.class.php');
require_once('output.pdflib.class.php');
require_once('output.fpdf.class.php');
require_once('output.fastps.class.php');

require_once('stubs.common.inc.php');

require_once('media.layout.inc.php');

require_once('box.php');
require_once('box.generic.php');
require_once('box.container.php');
require_once('box.generic.inline.php');
require_once('box.inline.php');
require_once('box.inline.control.php');

require_once('font.class.php');
require_once('font_factory.class.php');

require_once('box.br.php');
require_once('box.block.php');
require_once('box.block.inline.php');
require_once('box.button.php');
require_once('box.checkbutton.php');
require_once('box.frame.php');
require_once('box.iframe.php');
require_once('box.input.text.php');
require_once('box.legend.php');
require_once('box.list-item.php');
require_once('box.null.php');
require_once('box.radiobutton.php');
require_once('box.select.php');
require_once('box.table.php');
require_once('box.table.cell.php');
require_once('box.table.row.php');
require_once('box.table.section.php');

require_once('box.text.php');
require_once('box.text.string.php');
require_once('box.field.pageno.php');
require_once('box.field.pages.php');

require_once('box.whitespace.php');

require_once('box.img.php'); // Inherited from the text box!

require_once('box.utils.text-align.inc.php');

require_once('manager.encoding.php');
require_once('encoding.inc.php');
require_once('encoding.entities.inc.php');
require_once('encoding.glyphs.inc.php');
require_once('encoding.iso-8859-1.inc.php');
require_once('encoding.iso-8859-2.inc.php');
require_once('encoding.iso-8859-3.inc.php');
require_once('encoding.iso-8859-4.inc.php');
require_once('encoding.iso-8859-5.inc.php');
require_once('encoding.iso-8859-7.inc.php');
require_once('encoding.iso-8859-9.inc.php');
require_once('encoding.iso-8859-10.inc.php');
require_once('encoding.iso-8859-11.inc.php');
require_once('encoding.iso-8859-13.inc.php');
require_once('encoding.iso-8859-14.inc.php');
require_once('encoding.iso-8859-15.inc.php');
require_once('encoding.koi8-r.inc.php');
require_once('encoding.cp866.inc.php');
require_once('encoding.windows-1250.inc.php');
require_once('encoding.windows-1251.inc.php');
require_once('encoding.windows-1252.inc.php');
require_once('encoding.dingbats.inc.php');
require_once('encoding.symbol.inc.php');

require_once('ps.unicode.inc.php');
require_once('ps.utils.inc.php');
require_once('ps.whitespace.inc.php');
require_once('ps.text.inc.php');

require_once('ps.image.encoder.inc.php');
require_once('ps.image.encoder.simple.inc.php');
require_once('ps.l2.image.encoder.stream.inc.php');
require_once('ps.l3.image.encoder.stream.inc.php');
require_once('ps.image.encoder.imagemagick.inc.php');

require_once('tag.body.inc.php');
require_once('tag.font.inc.php');
require_once('tag.frame.inc.php');
require_once('tag.input.inc.php');
require_once('tag.img.inc.php');
require_once('tag.select.inc.php');
require_once('tag.span.inc.php');
require_once('tag.table.inc.php');
require_once('tag.td.inc.php');
require_once('tag.utils.inc.php');
require_once('tag.ulol.inc.php');

require_once('tree.navigation.inc.php');

require_once('html.attrs.inc.php');
require_once('html.list.inc.php');

require_once('xhtml.autoclose.inc.php');
require_once('xhtml.utils.inc.php');
require_once('xhtml.tables.inc.php');
require_once('xhtml.p.inc.php');
require_once('xhtml.lists.inc.php');
require_once('xhtml.deflist.inc.php');
require_once('xhtml.script.inc.php');
require_once('xhtml.entities.inc.php');
require_once('xhtml.comments.inc.php');
require_once('xhtml.style.inc.php');
require_once('xhtml.selects.inc.php');

require_once('background.php');
require_once('background.image.php');
require_once('background.position.php');

require_once('height.php');
require_once('width.php');

require_once('css.inc.php');
require_once('css.utils.inc.php');
require_once('css.parse.inc.php');
require_once('css.parse.media.inc.php');
require_once('css.apply.inc.php');

require_once('css.background.color.inc.php');
require_once('css.background.image.inc.php');
require_once('css.background.repeat.inc.php');
require_once('css.background.position.inc.php');
require_once('css.background.inc.php');

require_once('css.border.inc.php');
require_once('css.border.style.inc.php');
require_once('css.border.collapse.inc.php');
require_once('css.bottom.inc.php');
require_once('css.clear.inc.php');
require_once('css.color.inc.php');
require_once('css.colors.inc.php');
require_once('css.content.inc.php');
require_once('css.display.inc.php');
require_once('css.float.inc.php');
require_once('css.font.inc.php');
require_once('css.height.inc.php');
require_once('css.left.inc.php');
require_once('css.line-height.inc.php');

require_once('css.list-style-image.inc.php');
require_once('css.list-style-position.inc.php');
require_once('css.list-style-type.inc.php');
require_once('css.list-style.inc.php');

require_once('css.margin.inc.php');
require_once('css.overflow.inc.php');
require_once('css.padding.inc.php');

require_once('css.page-break.inc.php');
require_once('css.page-break-after.inc.php');

require_once('css.position.inc.php');
require_once('css.right.inc.php');
require_once('css.rules.inc.php');
require_once('css.selectors.inc.php');
require_once('css.text-align.inc.php');
require_once('css.text-decoration.inc.php');
require_once('css.text-indent.inc.php');
require_once('css.top.inc.php');
require_once('css.vertical-align.inc.php');
require_once('css.visibility.inc.php');
require_once('css.white-space.inc.php');
require_once('css.width.inc.php');
require_once('css.z-index.inc.php');

require_once('css.pseudo.add.margin.inc.php');
require_once('css.pseudo.align.inc.php');
require_once('css.pseudo.cellspacing.inc.php');
require_once('css.pseudo.cellpadding.inc.php');
require_once('css.pseudo.form.action.inc.php');
require_once('css.pseudo.form.radiogroup.inc.php');
require_once('css.pseudo.link.destination.inc.php');
require_once('css.pseudo.link.target.inc.php');
require_once('css.pseudo.listcounter.inc.php');
require_once('css.pseudo.localalign.inc.php');
require_once('css.pseudo.nowrap.inc.php');
require_once('css.pseudo.table.border.inc.php');

// After all CSS utilities and constants have been initialized, load the default (precomiled) CSS stylesheet
require_once('css.defaults.inc.php');

require_once('localalign.inc.php');

require_once('converter.class.php');

require_once('treebuilder.class.php');
require_once('image.class.php');

require_once('fetched_data._interface.class.php');
require_once('fetched_data._html.class.php');
require_once('fetched_data.url.class.php');
require_once('fetched_data.file.class.php');
require_once('fetched_data.string.class.php');

require_once('fetcher._interface.class.php');
require_once('fetcher.url.class.php');
require_once('fetcher.local.class.php');

require_once('filter.data._interface.class.php');
require_once('filter.data.doctype.class.php');
require_once('filter.data.utf8.class.php');
require_once('filter.data.html2xhtml.class.php');
require_once('filter.data.xhtml2xhtml.class.php');

require_once('parser._interface.class.php');
require_once('parser.xhtml.class.php');

require_once('filter.pre._interface.class.php');
require_once('filter.pre.fields.class.php');
require_once('filter.pre.headfoot.class.php');

require_once('layout._interface.class.php');
require_once('layout.default.class.php');
require_once('layout.ps.class.php');

require_once('filter.post._interface.class.php');

require_once('filter.output._interface.class.php');
require_once('filter.output.ps2pdf.class.php');
require_once('filter.output.gzip.class.php');

require_once('destination._interface.class.php');
require_once('destination._http.class.php');
require_once('destination.browser.class.php');
require_once('destination.download.class.php');
require_once('destination.file.class.php');

require_once('pipeline.class.php');

require_once('dom.php5.inc.php');
require_once('dom.activelink.inc.php');

require_once('xml.validation.inc.php');

require_once('content_type.class.php');

class PipelineFactory {
  function create_default_pipeline($encoding, $filename) {
    $pipeline = new Pipeline(); 

    $pipeline->fetchers[] = new FetcherURL();

    $pipeline->data_filters[] = new DataFilterUTF8($encoding);
    $pipeline->data_filters[] = new DataFilterHTML2XHTML();

    $pipeline->parser = new ParserXHTML();

    $pipeline->pre_tree_filters = array();

    $pipeline->layout_engine = new LayoutEngineDefault();

    $pipeline->post_tree_filters = array();

    $pipeline->output_driver = new OutputDriverFPDF();
    
    $pipeline->output_filters = array();

    $pipeline->destination = new DestinationDownload($filename, ContentType::pdf());

    return $pipeline;
  }
}

?>