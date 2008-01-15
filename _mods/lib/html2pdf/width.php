<?php
function merge_width_constraint($wc1, $wc2) {
  if (is_a($wc1, "WCNone")) { return $wc2; }
  if (is_a($wc1, "WCConstant") && !is_a($wc2, "WCNone")) {
    return $wc2;
  };
  if (is_a($wc1, "WCFraction") && is_a($wc2, "WCFraction")) {
    return $wc2;
  };
  return $wc1;
}

// the second parameter of 'apply' method may be null; it means that 
// parent have 'fit' width and depends on the current constraint itself

class WCNone {
  function applicable(&$box) { return false; }

  function apply($w, $pw) { return $w; }
  function apply_inverse($w, $pw) { return $pw; }

  function to_ps() { return "wc-create-none"; }

  function copy() { return new WCNone(); }
  function units2pt($base) { return; }
}

class WCConstant {
  var $width;

  function applicable(&$box) { return true; }

  function WCConstant($width) {
    $this->width = $width;
  }

  function apply($w, $pw) {
    return $this->width;
  }

  function apply_inverse($w, $pw) { return $pw; }

  function to_ps() { return $this->width." wc-create-constant"; }

  function copy() { return new WCConstant($this->width); }

  function units2pt($base) { 
    $this->width = units2pt($this->width, $base); 
  }
}

class WCFraction {
  var $fraction;

  function applicable(&$box) {
    if (is_null($box->parent)) { return false; };
    return is_a($box, "TableCellBox") || $box->parent->_width_constraint->applicable($box->parent);
  }

  function WCFraction($fraction) { 
    $this->fraction = $fraction;
  } 

  function apply($w, $pw) {
    if (!is_null($pw)) {
      return $pw * $this->fraction;
    } else {
      return $w;
    };
  }

  function apply_inverse($w, $pw) { 
    if ($this->fraction > 0) { return $w / $this->fraction; } else { return 0; }; 
  }

  function to_ps() { return $this->fraction." wc-create-fraction"; }

  function copy() { return new WCFraction($this->fraction); }
  function units2pt($base) { }
}
?>