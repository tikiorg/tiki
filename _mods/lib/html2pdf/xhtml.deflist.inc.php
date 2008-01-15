<?php 
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/xhtml.deflist.inc.php,v 1.1 2008-01-15 09:21:15 mose Exp $

function process_dd(&$sample_html, $offset) {
  return autoclose_tag($sample_html, $offset, "(dt|dd|dl|/dl|/dd)", array("dl" => "process_dl"), "/dd");
}

function process_dt(&$sample_html, $offset) {
  return autoclose_tag($sample_html, $offset, "(dt|dd|dl|/dl|/dd)", array("dl" => "process_dl"), "/dt");  
}

function process_dl(&$sample_html, $offset) {
  return autoclose_tag($sample_html, $offset, "(dt|dd|/dl)", 
                       array("dt" => "process_dt",
                             "dd" => "process_dd"), 
                       "/dl");  
};

function process_deflists(&$sample_html, $offset) {
  return autoclose_tag($sample_html, $offset, "(dl)", 
                       array("dl" => "process_dl"),
                       "");
};

?>