<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/box.list-item.php,v 1.1 2008-01-15 09:20:27 mose Exp $

class ListItemBox extends BlockBox {
  var $size;

  function &create(&$root) {
    $box = new ListItemBox($root);
    $box->create_content($root);
    return $box;
  }

  function ListItemBox(&$root) {
    // Call parent constructor
    $this->BlockBox($root);

    // Pseudo-CSS properties
    // '-list-counter'
    $counter =& get_css_handler('-list-counter');
    $background_color =& get_css_handler('background-color');
    $background_color->push_css('transparent');

    $this->str_number_box = TextBox::create(CSSListStyleType::format_number($this->list_style->type,$counter->get()), 
                                            'iso-8859-1');
    $this->str_number_box->baseline = $this->str_number_box->default_baseline;

    $background_color->pop();

    // increase counter value
    $counter->pop(); // remove inherited value
    $counter->replace($counter->get() + 1);
    $counter->push($counter->get());

    // open the marker image if specified
    if ($this->list_style->image !== null) {
      $this->marker_image = Image::get($this->list_style->image);      
    } else {
      $this->marker_image = null;
    };

  }

  function reflow(&$parent, &$context) {
    // If list-style-position is inside, we'll need to move marker box inside the 
    // list-item box and offset all content by its size;
    if ($this->list_style->position === LSP_INSIDE) {
      // Add marker box width to text-indent value
      $this->_additional_text_indent = $this->get_marker_box_width();
    };

    // Procees with normal block box flow algorithm
    BlockBox::reflow($parent, $context);
  }

  function reflow_text(&$driver) {
    if (is_null($this->str_number_box->reflow_text($driver))) {
      return null;
    };

    return GenericContainerBox::reflow_text($driver);
  }
  
  function show(&$viewport) {
    // draw generic block box
    if (is_null(BlockBox::show($viewport))) {
      return null;
    };

    // Draw marker 
    // Determine the marker box base X coordinate 
    $x = $this->get_left();

    // Determine the base Y coordinate of marker box
    $element = $this->get_first_data();

    if ($element) {
      $y = $element->get_top() - $element->default_baseline;
    } else {
      $y = $this->get_top();
    }

    // If list-style-position is inside, we'll need to move marker box inside the 
    // list-item box and offset all content by its size;
    if ($this->list_style->position === LSP_INSIDE) {
      $x += $this->get_marker_box_width();
    };

    if ($this->marker_image) {
      $this->mb_image($viewport, $x, $y);
    } else {
      switch ($this->list_style->type) {
      case LST_NONE:
        // No marker at all
        break;
      case LST_DISC:
        $this->mb_disc($viewport, $x, $y);
        break;
      case LST_CIRCLE:
        $this->mb_circle($viewport, $x, $y);
        break;
      case LST_SQUARE:
        $this->mb_square($viewport, $x, $y);
        break;
      default:
        $this->mb_string($viewport, $x, $y);
        break;
      }
    };

    return true;
  }

  function get_marker_box_width() {
    switch ($this->list_style->type) {
    case LST_NONE:
      // no marker box will be rendered at all
      return 0;
    case LST_DISC:
    case LST_CIRCLE:
    case LST_SQUARE:
      //  simple graphic marker
      return $this->font_size;
    default:
      // string marker. Return the width of the marker text
      return $this->str_number_box->get_full_width();
    };
  }

  function mb_string(&$viewport, $x, $y) {
    $this->str_number_box->put_top($y + $this->str_number_box->default_baseline);
    $this->str_number_box->put_left($x - $this->str_number_box->get_full_width());

    $this->str_number_box->show($viewport);
  }

  function mb_disc(&$viewport, $x, $y) {
    $this->color->apply($viewport);
    $viewport->circle( $x - $this->font_size*0.5, $y + $this->font_size*0.4*HEIGHT_KOEFF, $this->font_size * BULLET_SIZE_KOEFF);
    $viewport->fill();
  }
  
  function mb_circle(&$viewport, $x, $y) {
    $this->color->apply($viewport);
    $viewport->setlinewidth(0.1);
    $viewport->circle( $x - $this->font_size*0.5, $y + $this->font_size*0.4*HEIGHT_KOEFF, $this->font_size * BULLET_SIZE_KOEFF);
    $viewport->stroke();
  }

  function mb_square(&$viewport, $x, $y) {
    $this->color->apply($viewport);
    $viewport->rect($x - $this->font_size*0.512, $y + $this->font_size*0.3*HEIGHT_KOEFF, $this->font_size * 0.25, $this->font_size * 0.25);
    $viewport->fill();
  }

  function mb_image(&$viewport, $x, $y) {
    $imagebox = new ImgBox($this->marker_image);
    $imagebox->moveto($x - $imagebox->get_width(), $y + $imagebox->get_height());
    $imagebox->show($viewport);
  }
}

?>