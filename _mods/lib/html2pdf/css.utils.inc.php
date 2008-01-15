<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/css.utils.inc.php,v 1.1 2008-01-15 09:20:39 mose Exp $

// TODO: make an OO-style selectors interface instead of switches

// Searches the CSS rule selector for pseudoelement selectors 
// (assuming that there can be only one) and returns its value
//
// note that there's not sence in applying pseudoelement to any chained selector except the last
// (the deepest descendant)
// 
function css_find_pseudoelement($selector) {
  $selector_type = selector_get_type($selector);
  switch ($selector_type) {
  case SELECTOR_PSEUDOELEMENT_BEFORE:
  case SELECTOR_PSEUDOELEMENT_AFTER:
    return $selector_type;
  case SELECTOR_SEQUENCE:
    foreach ($selector[1] as $subselector) {
      $pe = css_find_pseudoelement($subselector);
      if ($pe !== null) { return $pe; };
    }
    return null;
  default:
    return null;
  }
}

function _fix_tag_display($default_display) {
  // In some cases 'display' CSS property should be ignored for element-generated boxes
  // Here we will use the $default_display stored above
  // Note that "display: none" should _never_ be changed
  //
  $handler =& get_css_handler('display');
  if ($handler->get() === "none") {
    return;
  };

  switch ($default_display) {
  case 'table-cell':
    // TD will always have 'display: table-cell'
    $handler->css('table-cell');
    break;
    
  case '-button':
    // INPUT buttons will always have 'display: -button' (in latter case if display = 'block', we'll use a wrapper box)
    if ($handler->get() === 'block') {
      $need_block_wrapper = true;
    };
    $handler->css('-button');
    break;
  };
}

function is_percentage($value) { return $value{strlen($value)-1} == "%"; }

function css_remove_value_quotes($value) {
  if (strlen($value) == 0) { return $value; };

  if ($value{0} === "'" || $value{0} === "\"") {
    $value = substr($value, 1, strlen($value)-2);
  };
  return $value;
}

function css_import($src) {
// Update the base url; 
// all urls will be resolved relatively to the current stylesheet url
  global $g_baseurl;
  $url = guess_url($src, $g_baseurl);            

  $old_base = $g_baseurl;
  $g_baseurl = $url;

  $css = @file_get_contents($url);

  if (!empty($css)) { 
    parse_css($css); 
  };

  $g_baseurl = $old_base;
};

?>