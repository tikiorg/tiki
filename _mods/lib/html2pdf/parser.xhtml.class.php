<?php
class ParserXHTML {
  function &process($html) {
    // Run the XML parser on the XHTML we've prepared
    $dom_tree = TreeBuilder::build($html);
    
    // Check if parser returned valid document
    if (is_null($dom_tree)) {
      readfile('templates/cannot_parse.html');
      error_log("Cannot parse document: $g_baseurl");
      die();
    }
    
    scan_styles($dom_tree);
    // Temporary hack: convert CSS rule array to CSS object
    global $g_css;
    global $g_css_obj;
    global $g_media;
    $g_css_obj = new CSSObject;
    foreach ($g_css as $rule) {
      $g_css_obj->add_rule($rule);
    }
   
    $body = traverse_dom_tree_pdf($dom_tree);

    $box =& create_pdf_box($body);
    
    return $box;
  }
}
?>