<?php

class CSSPseudoLinkTarget extends CSSProperty {
  function CSSPseudoLinkTarget() { $this->CSSProperty(true, true); }

  function default_value() { return ""; }

  function is_external_link($value) {
    return (strlen($value) > 0 && $value{0} != "#");
  }

  function is_local_link($value) {
    return (strlen($value) > 0 && $value{0} == "#");
  }

  function parse($value) { 
    // Keep local links (starting with sharp sign) as-is
    if (CSSPseudoLinkTarget::is_local_link($value)) { return $value; }

    $data = @parse_url($value);
    if (!isset($data['scheme']) || $data['scheme'] == "" || $data['scheme'] == "http") {
      global $g_baseurl;
      return guess_url($value, $g_baseurl);
    } else {
      return $value;
    };
  }

  function value2ps($value) {
    if ($value === "") {
      // No link
      return "<< /type /none >>";
    } elseif (CSSPseudoLinkTarget::is_local_link($value)) {
      // Local link
      return "<< /type /local /value /".substr($value,1)." >>";
    } else {
      // External link
      return "<< /type /uri /value (".$value.") >>";
    };
  }
}

register_css_property('-html2ps-link-target', new CSSPseudoLinkTarget);

?>