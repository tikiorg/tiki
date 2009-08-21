<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     kaltura_media_type
 * Purpose:  Extracts username of who modified the roughcut from xml data
 * Input:    string: input xml data
 *           
 * -------------------------------------------------------------
 */
function smarty_modifier_kaltura_remix_user($xml) {
	$domdoc = new DOMDocument;
 	$domdoc->loadXML($xml); 
 	$xpath = new DOMXpath($domdoc);
 	$elements = $xpath->query("/xml/MetaData/PuserId");
 	foreach ($elements as $element) {
    	$nodes = $element->childNodes;
    	foreach ($nodes as $node) {
      		return $node->nodeValue;
    	}
 	}	
}
