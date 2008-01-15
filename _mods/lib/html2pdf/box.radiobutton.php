<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/box.radiobutton.php,v 1.1 2008-01-15 09:20:27 mose Exp $

class RadioBox extends GenericBox {
  var $checked;

  /**
   * @var String name of radio button group
   * @access private
   */
  var $_group_name;

  function &create(&$root) {
    $checked = $root->has_attribute('checked');
    $box =& new RadioBox($checked);
    return $box;
  }

  function RadioBox($checked) {
    // Call parent constructor
    $this->GenericBox();

    // Check the box state
    $this->checked = $checked;

    $handler =& get_css_handler('-html2ps-form-radiogroup');
    $this->_group_name = $handler->get();

    // Setup box size:
    $this->default_baseline = units2pt(CHECKBOX_SIZE);
    $this->height           = units2pt(CHECKBOX_SIZE);
    $this->width            = units2pt(CHECKBOX_SIZE);
  }

  // Inherited from GenericBox
  function get_min_width(&$context) { return $this->get_full_width($context); }
  function get_max_width(&$context) { return $this->get_full_width($context); }

  function reflow(&$parent, &$context) {  
    GenericBox::reflow($parent, $context);
    
    // set default baseline
    $this->baseline = $this->default_baseline;
    
//     // Vertical-align
//     $this->_apply_vertical_align($parent);

    // append to parent line box
    $parent->append_line($this);

    // Determine coordinates of upper-left _margin_ corner
    $this->guess_corner($parent);

    // Offset parent current X coordinate
    $parent->_current_x += $this->get_full_width();

    // Extends parents height
    $parent->extend_height($this->get_bottom_margin());
  }

  function show(&$driver) {   
    // Cet check center
    $x = ($this->get_left() + $this->get_right()) / 2;
    $y = ($this->get_top() + $this->get_bottom()) / 2;

    // Calculate checkbox size
    $size = $this->get_width() / 3;

    // Draw checkbox
    $driver->setlinewidth(0.25);
    $driver->circle($x, $y, $size);
    $driver->stroke();

    // Draw checkmark if needed
    if ($this->checked) { 
      $check_size = $this->get_width() / 6;

      $driver->circle($x, $y, $check_size);
      $driver->fill();
    }

    /**
     * Render the interactive button (if requested and possible)
     */
    global $g_config;
    if ($g_config['renderforms']) {
      $driver->field_radio($x - $size, 
                           $y + $size, 
                           2*$size, 
                           2*$size,
                           $this->_group_name);
    };

    return true;
  }
}
?>