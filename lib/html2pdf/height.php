<?php
// Height constraint "merging" function.
// 
// Constraints have the following precedece:
// 1. constant constraint
// 2. diapason constraint 
// 3. no constraint
//
// If both constraints are constant, the first one is choosen;
//
// If both constraints are diapason constraints the first one is choosen
//
function merge_height_constraint($hc1, $hc2) {
  // First constraint is constant; return this, as second constraint 
  // will never override it
  if ($hc1->constant !== null) { return $hc1; };

  // Second constraint is constant; first is not constant;
  // return second, as it is more important
  if ($hc2->constant !== null) { return $hc2; };

  // Ok, both constraints are not constant. Check if there's any diapason 
  // constraints

  // Second constraint is free constraint, return first one, as 
  // if it is a non-free it should have precedence, otherwise 
  // it will be free constraint too
  if ($hc2->min === null && $hc2->max === null) { return $hc1; };
  
  // The same rule applied if the first constraint is free constraint
  if ($hc1->min === null && $hc1->max === null) { return $hc2; };

  // If we got here it means both constraints are diapason constraints.
  return $hc1;
}

// Height constraint class
// 
// Height could be constrained as a percentage of the parent height OR 
// as a constant value. Note that in most cases percentage constraint 
// REQUIRE parent height to be constrained. 
//
// Note that constraint can be given as a diapason from min to max height
// It is applied only of no strict height constraint is given
//
class HCConstraint {
  var $constant;
  var $min;
  var $max;

  function applicable(&$box) {
    if ($this->constant !== null) { return $this->applicable_value($this->constant, $box); }

    $applicable_min = false;
    if ($this->min !== null) {
      $applicable_min = $this->applicable_value($this->min, $box);
    };

    $applicable_max = false;
    if ($this->max !== null) {
      $applicable_max = $this->applicable_value($this->max, $box);
    };

    return $applicable_min || $applicable_max;
  }

  function applicable_value($value, &$box) {
    // Constant constraints always applicable
    if (!$value[1]) { return true; };

    if (!$box->parent) { return false; };
    return $box->parent->_height_constraint->applicable($box->parent);
  }
   
  function _fix_value($value, &$box, $default) {
    // A percentage or immediate value?
    if ($value[1]) {
      // CSS 2.1: The percentage  is calculated with respect to the height of the generated box's containing block.
      // If the height of the containing  block is not specified explicitly (i.e., it  depends on  content height),
      // and this  element is  not absolutely positioned, the value is interpreted like 'auto'.

      // Check if parent exists
      //
      if (!isset($box->parent)) { return null; }
      if (!$box->parent) { return null; }

      // if parent does not have constrained height, return null - no height constraint can be applied
      // Table cells should be processed separately
      if (!is_a($box->parent,"TableCellBox") &&
          $box->parent->_height_constraint->constant === null &&
          $box->parent->_height_constraint->min === null &&
          $box->parent->_height_constraint->max === null) {
        return $default;
      };

      if (is_a($box->parent,"TableCellBox")) {
        $rhc = $box->parent->parent->get_rhc($box->parent->row);
        if ($rhc->is_null()) { return $default; };

        return $rhc->apply($box->parent->get_height(), $box);
      };

      return $box->parent->get_height() * $value[0] / 100;
    } else {
      // Immediate
      return $value[0];
    }
  }

  function create($box) {   
    // Determine if there's constant restriction
    $handler =& get_css_handler('height');
    if (!$handler->is_default($handler->get())) {
      $constant = $handler->get();
    } else {
      $constant = null;
    };

    // Determine if there's min restriction
    $handler =& get_css_handler('min-height');
    if (!$handler->is_default($handler->get())) {
      $min = $handler->get();
    } else {
      $min = null;
    };

    // Determine if there's max restriction
    $handler =& get_css_handler('max-height');
    if (!$handler->is_default($handler->get())) {
      $max = $handler->get();
    } else {
      $max = null;
    };

    return new HCConstraint($constant, $min, $max);
  }

  // Height constraint constructor
  //
  // @param $constant value of constant constraint or null of none
  // @param $min value of minimal box height or null if none
  // @param $max value of maximal box height or null if none
  //
  function HCConstraint($constant, $min, $max) {
    $this->constant = $constant;
    $this->min = $min;
    $this->max = $max;
  }

  function apply_min($value, &$box) {
    if ($this->min === null) {
      return $value;
    } else {
      return max($this->_fix_value($this->min, $box, $value), $value);
    }
  }

  function apply_max($value, &$box) {
    if ($this->max === null) {
      return $value;
    } else {
      return min($this->_fix_value($this->max, $box, $value), $value);
    }
  }

  function apply($value, &$box) {
    if ($this->constant !== null) {
      $height = $this->_fix_value($this->constant, $box, $value);
    } else {
      $height =  $this->apply_min($this->apply_max($value, $box), $box);
    }

    // Table cells contained in tables with border-collapse: separate
    // have padding included in the 'height' value. So, we'll need to subtract
    // vertical-extra from the current value to get the actual content height
    // TODO
    
    return $height;
  }

  function is_null() {
    return 
      $this->max === null && 
      $this->min == null && 
      $this->constant == null;
  }

  function to_ps_item($item) {
    return "<< /percentage ".($item[1] ? "true" : "false" )." /value ".ps_units($item[0])." >>";
  }

  function to_ps() {
    return 
      ($this->max      !== null ? $this->to_ps_item($this->max)      : "/null") . " " .
      ($this->min      !== null ? $this->to_ps_item($this->min)      : "/null") . " " .
      ($this->constant !== null ? $this->to_ps_item($this->constant) : "/null") . " " .
      " hc-create";
  }

  function units2pt($base) {
    $this->units2pt_value($this->max, $base);
    $this->units2pt_value($this->min, $base);
    $this->units2pt_value($this->constant, $base);
  }

  function units2pt_value(&$value, $base) {
    if (is_null($value)) { return; };

    if (!$value[1]) {
      //      print($value[0]."/".units2pt($value[0])."/".units2pt("1em")."<br/>");
      $value[0] = units2pt($value[0], $base);
    };
  }
}
?>