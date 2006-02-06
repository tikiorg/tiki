<?php
class OutputDriverPS extends OutputDriverGenericPS {
  var $stream;

  function close() {
    fwrite($this->stream, file_get_contents('postscript/footer.ps') );
    fclose($this->stream);
  }

  function encoding($encoding) {
    $encoding = trim(strtolower($encoding));

    $translations = array(
                          'iso-8859-1'   => "ISOLatin1Encoding",
                          'iso-8859-2'   => "ISO-8859-2-Encoding",
                          'iso-8859-3'   => "ISO-8859-3-Encoding",
                          'iso-8859-4'   => "ISO-8859-4-Encoding",
                          'iso-8859-5'   => "ISO-8859-5-Encoding",
                          'iso-8859-7'   => "ISO-8859-7-Encoding",
                          'iso-8859-9'   => "ISO-8859-9-Encoding",
                          'iso-8859-10'  => "ISO-8859-10-Encoding",
                          'iso-8859-11'  => "ISO-8859-11-Encoding",
                          'iso-8859-13'  => "ISO-8859-13-Encoding",
                          'iso-8859-14'  => "ISO-8859-14-Encoding",
                          'iso-8859-15'  => "ISO-8859-15-Encoding",
                          'dingbats'     => "Dingbats-Encoding",
                          'symbol'       => "Symbol-Encoding",
                          'koi8-r'       => "KOI8-R-Encoding",
                          'cp1250'       => "Windows-1250-Encoding",
                          'cp1251'       => "Windows-1251-Encoding",
                          'windows-1250' => "Windows-1250-Encoding",
                          'windows-1251' => "Windows-1251-Encoding",
                          'windows-1252' => "Windows-1252-Encoding"
                           );

    if (isset($translations[$encoding])) { return $translations[$encoding]; };
    return $encoding;
  }

  function font_ascender($name, $encoding) { return 0; }
  function font_descender($name, $encoding) { return 0; }

  function stringwidth($string, $font, $size) { return 0; }
  
  function OutputDriverPS($scalepoints, $transparency_workaround, $quality_workaround, $image_encoder) {
    $this->OutputDriverGenericPS($image_encoder);

    $this->scalepoints             = $scalepoints;
    $this->transparency_workaround = $transparency_workaround;
    $this->quality_workaround      = $quality_workaround;
  }

  function reset($media) {
    OutputDriverGenericPS::reset($media);

    $this->stream = fopen($this->get_filename(), "wb");

    $header = file_get_contents("./postscript/header.ps");
    $header = preg_replace("/##PAGE##/",$media->to_ps(),$header);
    if ($this->scalepoints) {
      $header = preg_replace("/##PT##/","/pt {px 1.4 mul} def",$header);
    } else {
      $header = preg_replace("/##PT##/","/pt {} def",$header);
    };
    $header = preg_replace("/##PS2PDF##/",
                           $this->transparency_workaround ? "/ps2pdf-transparency-hack true def" : "/ps2pdf-transparency-hack false def",$header);
    $header = preg_replace("/##TRANSPARENCY##/",
                           $this->transparency_workaround ? "/no-transparency-output true def" : "/no-transparency-output false def",$header);
    if ($this->quality_workaround) {
      $header = preg_replace("/##IMAGEQUALITY##/", "<< /ColorACSImageDict << /QFactor 0.2 /Blend 1 /HSamples [1 1 1 1] /VSamples [1 1 1 1] >> >> setdistillerparams", $header);
    } else {
      $header = preg_replace("/##IMAGEQUALITY##/", "", $header);
    };

    $header = preg_replace("/##PAGEBORDER##/",($this->is_show_page_border()) ? "true" : "false",$header);
    $header = preg_replace("/##DEBUGBOX##/",($this->is_debug_boxes()) ? "true" : "false",$header);

    fwrite($this->stream,  $header ); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/array.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/background.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/background.image.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/border.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/box.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/box.block.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/box.block.inline.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/box.break.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/box.button.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/box.checkbutton.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/box.container.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/box.frame.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/box.generic.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/box.generic.inline.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/box.iframe.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/box.image.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/box.inline.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/box.input.check.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/box.input.radio.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/box.input.text.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/box.inline.whitespace.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/box.list-item.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/box.radiobutton.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/box.select.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/box.span.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/box.table.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/box.table.row.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/box.table.cell.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/box.table.cell.fake.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/box.text.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/box.whitespace.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/cellspan.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/class.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/color.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/containing_block.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/context.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/flow.block.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/flow.box.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/flow.float.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/flow.inline.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/flow.inline.block.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/flow.legend.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/flow.table.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/flow_viewport.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/font.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/geometry.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/height.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/image.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/position.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/predicates.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/table.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/table.row.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/text-align.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/vertical-align.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/viewport.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/width.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/encoding.iso-8859-2.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/encoding.iso-8859-3.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/encoding.iso-8859-4.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/encoding.iso-8859-5.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/encoding.iso-8859-7.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/encoding.iso-8859-9.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/encoding.iso-8859-10.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/encoding.iso-8859-11.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/encoding.iso-8859-13.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/encoding.iso-8859-14.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/encoding.iso-8859-15.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/encoding.windows-1250.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/encoding.windows-1251.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/encoding.windows-1252.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/encoding.koi8-r.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/encoding.dingbats.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/encoding.symbol.ps')); fwrite($this->stream, "\n");
    fwrite($this->stream, file_get_contents('postscript/init.ps')); fwrite($this->stream, "\n");
  }

  function write($data) {
    fwrite($this->stream, $data);
  }
}
?>