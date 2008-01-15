<?php
class PreTreeFilterHeaderFooter extends PreTreeFilter {
  var $header_html;
  var $footer_html;
  var $watermark_html;

  function PreTreeFilterHeaderFooter($header_html, $footer_html, $watermark_html) {
    $this->header_html = $header_html;
    $this->footer_html = $footer_html;
    $this->watermark_html = $watermark_html;
  }

  function process(&$tree) {
  }
}
?>