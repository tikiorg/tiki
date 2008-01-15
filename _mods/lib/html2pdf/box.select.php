<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/box.select.php,v 1.1 2008-01-15 09:20:27 mose Exp $

class SelectBox extends InlineControlBox {
  function &create(&$root) {
    $box =& new SelectBox($root);
    return $box;
  }

  function SelectBox(&$root) {
    // Call parent constructor
    $this->InlineBox();

    // Determine the option to be shown
    $child = $root->first_child();
    $content = "";
    $size = 0;
    while ($child) {
      if ($child->node_type() == XML_ELEMENT_NODE) {
        $size = max($size, strlen($child->get_content()));
        if (empty($content) || $child->has_attribute("selected")) { $content = $child->get_content(); };
      };
      $child = $child->next_sibling();
    };

    $content = str_pad($content, $size*SIZE_SPACE_KOEFF, " ");
    
    // TODO: international symbols! need to use somewhat similar to 'process_word' in InlineBox
    push_css_text_defaults();
    $this->add_child(TextBox::create($content, 
                                        'iso-8859-1'));
    pop_css_defaults();
  }

//   function reflow(&$parent, &$context) {  
//     GenericBox::reflow($parent, $context);
    
//     // Check if we need a line break here
//     $this->maybe_line_break($parent, $context);

//     // append to parent line box
//     $parent->append_line($this);

//     // Determine coordinates of upper-left _margin_ corner
//     $this->guess_corner($parent);

//     // Determine the box width
//     $this->put_full_width($this->get_max_width($context));

//     $this->reflow_content($context);

// //     $context->pop_collapsed_margin();
// //     $context->push_collapsed_margin( 0 );
    
//     // center the button text vertically inside the button
//     $text =& $this->content[0];
//     $delta = ($text->get_top() - $text->get_height()/2) - ($this->get_top() - $this->get_height()/2);
//     $text->offset(0,-$delta);

//     // Offset parent current X coordinate
//     $parent->_current_x += $this->get_full_width();

//     // Extends parents height
//     $parent->extend_height($this->get_bottom_margin());

// //     $this->baseline = 
// //       $this->content[0]->baseline + 
// //       $this->get_extra_top();
    
// //     $this->default_baseline = $this->baseline;

// //     // Offset parent current X coordinate
// //     $parent->_current_x += $this->get_full_width();

// //     // Extends parents height
// //     $parent->extend_height($this->get_bottom_margin());
//   }

  function show(&$viewport) {   
    // Now set the baseline of a button box to align it vertically when flowing isude the 
    // text line
    $this->default_baseline = $this->content[0]->baseline + $this->get_extra_top();
    $this->baseline         = $this->content[0]->baseline + $this->get_extra_top();

    if (is_null(GenericContainerBox::show($viewport))) {
      return null;
    };

    $button_height = $this->get_height() + $this->padding->top->value + $this->padding->bottom->value;

    // Show arrow button box
    $viewport->setrgbcolor(0.93, 0.93, 0.93);
    $viewport->moveto($this->get_right_padding(), $this->get_top_padding());
    $viewport->lineto($this->get_right_padding() - $button_height, $this->get_top_padding());
    $viewport->lineto($this->get_right_padding() - $button_height, $this->get_bottom_padding());
    $viewport->lineto($this->get_right_padding(), $this->get_bottom_padding());
    $viewport->closepath();
    $viewport->fill();

    // Show box boundary
    $viewport->setrgbcolor(0,0,0);
    $viewport->moveto($this->get_right_padding(), $this->get_top_padding());
    $viewport->lineto($this->get_right_padding() - $button_height, $this->get_top_padding());
    $viewport->lineto($this->get_right_padding() - $button_height, $this->get_bottom_padding());
    $viewport->lineto($this->get_right_padding(), $this->get_bottom_padding());
    $viewport->closepath();
    $viewport->stroke();
  
    // Show arrow
    $viewport->setrgbcolor(0,0,0);
    $viewport->moveto($this->get_right_padding() - SELECT_BUTTON_TRIANGLE_PADDING,
                      $this->get_top_padding() - SELECT_BUTTON_TRIANGLE_PADDING);
    $viewport->lineto($this->get_right_padding() - $button_height + SELECT_BUTTON_TRIANGLE_PADDING, 
                      $this->get_top_padding() - SELECT_BUTTON_TRIANGLE_PADDING);
    $viewport->lineto($this->get_right_padding() - $button_height/2, $this->get_bottom_padding() + SELECT_BUTTON_TRIANGLE_PADDING);
    $viewport->closepath();
    $viewport->fill();

    return true;
  }

  function to_ps(&$psdata) {
    $psdata->write("box-select-create\n");
    $this->to_ps_common($psdata);
    $this->to_ps_css($psdata);
    $this->to_ps_content($psdata);
    $psdata->write("add-child\n");
  }
}
?>