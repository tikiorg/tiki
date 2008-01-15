<?php 
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/xhtml.selects.inc.php,v 1.1 2008-01-15 09:21:16 mose Exp $

function process_option(&$sample_html, $offset) {
  return autoclose_tag($sample_html, $offset, "(option|/select|/option)", 
                       array(), 
                       "/option");  
};

function process_select(&$sample_html, $offset) {
  return autoclose_tag($sample_html, $offset, "(option|/select)", 
                       array("option" => "process_option"), 
                       "/select");  
};

function process_selects(&$sample_html, $offset) {
  return autoclose_tag($sample_html, $offset, "(select)", 
                       array("select" => "process_select"), 
                       "");  
};

?>