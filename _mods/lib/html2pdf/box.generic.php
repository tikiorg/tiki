<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/box.generic.php,v 1.1 2008-01-15 09:20:26 mose Exp $

require_once('doc.anchor.class.php');

class GenericBox {
  var $uid;

  function collapse_margin(&$parent, &$context) {
    // Do margin collapsing

    // Margin collapsing is done as follows:
    // 1. If previous sibling was an inline element (so, parent line box was not empty),
    //    then no collapsing will take part
    // 2. If NO previous element exists at all, then collapse current box top margin
    //    with parent's collapsed top margin.
    // 2.1. If parent element was float, no collapsing should be
    // 3. If there's previous block element, collapse current box top margin 
    //    with previous elemenent's collapsed bottom margin
      
    // Check if current parent line box contains inline elements only. In this case the only 
    // margin will be current box margin 

    if (!$parent->line_box_empty()) {
      // Case (1). Previous element was inline element; no collapsing
      $vmargin = $this->margin->top->value;
      $context->push_collapsed_margin($vmargin);
      $parent->close_line($context);

      // Determine the base Y coordinate of box margin edge
      $y = $parent->_current_y - $vmargin;
    } else {   
      // Case (2). No previous block element at all; Collapse with parent margins
      // Case (3). There's a previous block element
      // We can process both cases at once, as context object collapsed margin stack
      // allows us to track collapsed margins value

      // Calculate the value to offset current box vertically due margin collapsing
      // note that we'll get non-negative value - the value to increate collapsed margin size, 
      // but we must offset box to the bottom
      //
      if ($this->margin->top->value >= 0) {
        $vmargin = $this->margin->top->value - min($this->margin->top->value, $context->get_collapsed_margin());
        $context->push_collapsed_margin(max($this->margin->top->value, $context->get_collapsed_margin()));
      } else {
        $vmargin = $this->margin->top->value;
        $context->push_collapsed_margin(0);
      };

      // Offset parent, if current box is the first child, as we should not get
      // vertical gaps before the first child during margin collapsing
      //
      if ($parent->uid != $context->container_uid()) {
        if (!$parent->offset_if_first($this, 0, -$vmargin)) {
          $parent->_current_y -= $vmargin;
        };

        // Determine the base Y coordinate of box margin edge
        // We do not need to add vmargin to _current_y value, as 
        // we've used offset function, which modified current flow point
        $y = $parent->_current_y;
      } else {
        $y = $parent->_current_y - $vmargin;
      };
    };
    return $y;
  }

  function copy_style(&$box) {
    $this->background  = $box->background->copy();
    $this->border      = $box->border;
    $this->cellpadding = $box->cellpadding;
    $this->cellspacing = $box->cellspacing;
    $this->clear       = $box->clear;
    $this->color       = $box->color;
    $this->content_pseudoelement = $box->content_pseudoelement;
    $this->display     = $box->display;
    $this->float       = $box->float;
    $this->font_size   = $box->font_size;
    $this->family      = $box->family;
    $this->weight      = $box->weight;
    $this->style       = $box->style;
    $this->height      = $box->height;
    $this->line_height = $box->line_height;
    $this->list_style  = $box->list_style;
    $this->margin      = $box->margin->copy();
    $this->overflow    = $box->overflow;
    $this->padding     = $box->padding->copy();
    $this->page_break_after = $box->page_break_after;
    $this->position    = $box->position;
    $this->text_align  = $box->text_align;
    $this->text_indent = $box->text_indent->copy();
    $this->decoration  = $box->decoration;
    $this->vertical_align = $box->vertical_align;
    $this->visibility  = $box->visibility;
    $this->width       = $box->width;
    $this->white_space = $box->white_space;
    $this->left        = $box->left;
    $this->top         = $box->top;
    $this->bottom      = $box->bottom;
    $this->right       = $box->right;
    $this->pseudo_align = $box->pseudo_align;
    $this->pseudo_link_destination = $box->pseudo_link_destination;
    $this->pseudo_link_target = $box->pseudo_link_target;
    $this->pseudo_nowrap = $box->pseudo_nowrap;
  }

  function GenericBox() {
    $this->_left   = 0;
    $this->_top    = 0;

    $this->baseline = 0;
    $this->default_baseline = 0;

    // Generic CSS properties
    // Save CSS property values
    $base_font_size = get_base_font_size();

    // 'background'
    $handler = get_css_handler('background');
    $this->background = $handler->get();
    $this->background = $this->background->copy();
    $this->background->units2pt($base_font_size);

    // 'border'
    $this->border = new BorderPDF(get_border());

    // '-cellpadding'
    $handler = get_css_handler('-cellpadding');
    $this->cellpadding = units2pt($handler->get(), $base_font_size);

    // '-cellspacing'
    $handler = get_css_handler('-cellspacing');
    $this->cellspacing = units2pt($handler->get(), $base_font_size);

    // 'clear'
    $handler = get_css_handler('clear');
    $this->clear = $handler->get();

    // 'color'
    $handler = get_css_handler('color');
    $this->color = $handler->get();

    // 'content'
    $handler = get_css_handler('content');
    $this->content_pseudoelement = $handler->get();

    // 'display'
    $handler = get_css_handler('display');
    $this->display = $handler->get();

    // 'float'
    $handler = get_css_handler('float');
    $this->float = $handler->get();

    // 'font-size'
    $this->font_size = units2pt(get_font_size(), $base_font_size);
    
    // 'font-family'
    $this->family = get_font_family();

    // 'font-weight'
    $this->weight = get_font_weight();

    // 'font-style'
    $this->style  = get_font_style();

    // 'height'
    $this->_height_constraint = HCConstraint::create($this);
    $this->_height_constraint->units2pt($base_font_size);
    $this->height = $this->_height_constraint->apply(0, $this);

    // 'line-height'
    $this->line_height = get_line_height();
    $this->line_height->units2pt($base_font_size);

    // 'list-style'
    $handler = get_css_handler('list-style');
    $this->list_style = $handler->get();

    // 'margin'
    $handler = get_css_handler('margin');
    $this->margin = $handler->get();
    $this->margin = $this->margin->copy();
    $this->margin->units2pt($base_font_size);

    // 'overflow'
    $handler = get_css_handler('overflow');
    $this->overflow = $handler->get();

    // 'padding'
    $handler = get_css_handler('padding');
    $this->padding = $handler->get();
    $this->padding = $this->padding->copy();
    $this->padding->units2pt($base_font_size);

    // 'page-break-after'
    $handler = get_css_handler('page-break-after');
    $this->page_break_after = $handler->get();

    // 'position'
    $handler = get_css_handler('position');
    $this->position = $handler->get();

    // 'text-align'
    $handler = get_css_handler('text-align');
    $this->text_align = $handler->get();

    // 'text-indent'
    $handler = get_css_handler('text-indent');
    $this->text_indent = $handler->get();
    $this->text_indent = $this->text_indent->copy();
    $this->text_indent->units2pt($base_font_size);

    // 'text-decoration'
    $handler = get_css_handler('text-decoration');
    $this->decoration = $handler->get();

    // 'vertical-align'
    $handler = get_css_handler('vertical-align');
    $this->vertical_align = $handler->get();

    // 'visibility'
    $handler = get_css_handler('visibility');
    $this->visibility = $handler->get();

    // 'width'
    $handler = get_css_handler('width');
    $this->_width_constraint = $handler->get();
    $this->_width_constraint = $this->_width_constraint->copy();
    $this->_width_constraint->units2pt($base_font_size);   
    $this->width = $this->_width_constraint->apply(0,0);

    // 'white-space'
    $handler = get_css_handler('white-space');
    $this->white_space = $handler->get();

    // CSS positioning properties

    // 'left'
    $handler = get_css_handler('left');
    $value = $handler->get();
    $this->left = $value;

    // 'top'
    $handler = get_css_handler('top');
    $this->top = $handler->get();

    // 'bottom'
    // TODO: automatic height calculation
    $handler = get_css_handler('bottom');
    $this->bottom = $handler->get();
    if (!is_null($this->bottom)) { $this->bottom = units2pt($this->bottom, $base_font_size); };

    // 'right'
    // TODO: automatic width calculation
    $handler = get_css_handler('right');
    $this->right = $handler->get();

    $handler = get_css_handler('z-index');
    $this->z_index = $handler->get();

    // 'PSEUDO-CSS' properties
    // '-align'
    $handler = get_css_handler('-align');
    $this->pseudo_align = $handler->get();   

    // '-html2ps-link-destination'
    global $g_config;
    if ($g_config["renderlinks"]) {
      $handler = get_css_handler('-html2ps-link-destination');
      $this->pseudo_link_destination = $handler->get();
    } else {
      $this->pseudo_link_destination = "";
    };

    // '-html2ps-link-target'
    global $g_config;
    if ($g_config["renderlinks"]) {
      $handler = get_css_handler('-html2ps-link-target');
      $this->pseudo_link_target = $handler->get();
    } else {
      $this->pseudo_link_target = "";
    };

    // '-localalign'
    $handler = get_css_handler('-localalign');
    switch ($handler->get()) {
    case LA_LEFT:
      break;
    case LA_RIGHT:
      $this->margin->left->auto = true;
      break;
    case LA_CENTER:
      $this->margin->left->auto  = true;
      $this->margin->right->auto = true;
      break;
    };

    // '-nowrap'
    $handler = get_css_handler('-nowrap');
    $this->pseudo_nowrap = $handler->get();

    // Layout data
    $this->baseline = 0;
    $this->_last = null;
    $this->parent = null;

    // Unique box identifier
    global $g_box_uid;
    $g_box_uid ++;
    $this->uid = $g_box_uid;

    // As PHP in most cases passes a copy of an object instead
    // of reference and it is pretty hard to track (especially between different versions
    // of PHP), we'll keep references to all boxes in the global array

    global $g_boxes;
    $g_boxes[$this->uid] =& $this;
  }

  /**
   * @todo percentage margin values should refer to the containing block width
   */
  function _calc_percentage_margins(&$parent) {
    if (!is_null($this->margin->left->percentage)) {
      $this->margin->left->value = $parent->get_width() * $this->margin->left->percentage / 100;
    };

    if (!is_null($this->margin->right->percentage)) {
      $this->margin->right->value = $parent->get_width() * $this->margin->right->percentage / 100;
    };
  }

  /**
   * @todo percentage padding values should refer to the containing block width
   */
  function _calc_percentage_padding(&$parent) {
    if (!is_null($this->padding->left->percentage)) {
      $this->padding->left->value = $parent->get_width() * $this->padding->left->percentage / 100;
    };

    if (!is_null($this->padding->right->percentage)) {
      $this->padding->right->value = $parent->get_width() * $this->padding->right->percentage / 100;
    };
  }

  // CSS 2.1:
  // 10.2 Content width: the 'width' property
  // Values have the following meanings:
  // <percentage> Specifies a percentage width. The percentage is calculated with respect to the width of the generated box's containing block.
  //
  // If the containing block's width depends on this element's width, 
  // then the resulting layout is undefined in CSS 2.1. 
  //
  function _calc_percentage_width(&$parent, &$context) {
    if (is_a($this->_width_constraint, "WCFraction")) { 
      $containing_block = $this->_get_containing_block();

      // Calculate actual width
      $width = $this->_width_constraint->apply($this->width, $containing_block['right'] - $containing_block['left']);
        
      // Check if calculated width is less than minimal width
      // Note that get_min_width will return the width including the extra horizontal space!
      $width = max($this->get_min_width($context) - $this->_get_hor_extra(), $width);
        
      // Assign calculated width
      $this->put_width($width);
        
      // Remove any width constraint
      $this->_width_constraint = new WCConstant($width);
    }
  }

  function _calc_auto_width_margins(&$parent) {
    if ($this->float !== FLOAT_NONE) {
      $this->_calc_auto_width_margins_float($parent);
    } else {
      $this->_calc_auto_width_margins_normal($parent);
    }
  }

  // 'auto' margin value became 0, 'auto' width is 'shrink-to-fit'
  function _calc_auto_width_margins_float(&$parent) {
    // If 'width' is set to 'auto' the used value is the "shrink-to-fit" width
    // TODO
    if (false) {
      // Calculation of the shrink-to-fit width is similar to calculating the
      // width of a table cell using the automatic table layout
      // algorithm. Roughly: calculate the preferred width by formatting the
      // content without breaking lines other than where explicit line breaks
      // occur, and also calculate the preferred minimum width, e.g., by trying
      // all possible line breaks. CSS 2.1 does not define the exact
      //  algorithm. Thirdly, find the available width: in this case, this is
      // the width of the containing block minus minus the used values of
      // 'margin-left', 'border-left-width', 'padding-left', 'padding-right',
      //  'border-right-width', 'margin-right', and the widths of any relevant
      // scroll bars.
      
      // Then the shrink-to-fit width is: min(max(preferred minimum width, available width), preferred width).
      
      // Store used value
    };
  
    // If 'margin-left', or 'margin-right' are computed as 'auto', their used value is '0'.
    if ($this->margin->left->auto) { $this->margin->left->value = 0; }
    if ($this->margin->right->auto) { $this->margin->right->value = 0; }
  }

  // 'margin-left' + 'border-left-width' + 'padding-left' + 'width' + 'padding-right' + 'border-right-width' + 'margin-right' = width of containing block
  function _calc_auto_width_margins_normal(&$parent) {
    // get the containing block width
    $parent_width = $parent->get_width();
    
    // If 'width' is set to 'auto', any other 'auto' values become '0'  and 'width' follows from the resulting equality.
    // TODO
//     if (false) {
//       // we may not modify margin values here, because the numerical values of margin 
//       // already will be 0 this case due the way of PHP part parses the CSS
//     } {
      // If both 'margin-left' and 'margin-right' are 'auto', their used values are equal. 
      // This horizontally centers the element with respect to the edges of the containing block.
      if ($this->margin->left->auto && $this->margin->right->auto) {
        $margin_value = ($parent_width - $this->get_full_width()) / 2;
        $this->margin->left->value = $margin_value;
        $this->margin->right->value = $margin_value;
      } else {
        // If there is exactly one value specified as 'auto', its used value follows from the equality.
        if ($this->margin->left->auto) {
          $this->margin->left->value = $parent_width - $this->get_full_width();
        } elseif ($this->margin->right->auto) {
          $this->margin->right->value = $parent_width - $this->get_full_width();
        };
      };
//     };
  }

  /**
   * Check if we need to generate a page break after this element
   *
   *
   */
  function check_page_break_after(&$parent, &$context) {
    // No sense in forcing page breaks after the top-level box
    if (!$parent) { return; };

    // Check if we should make a forced page break after this box
    if ($this->page_break_after != PAGE_BREAK_AVOID &&
        $this->page_break_after != PAGE_BREAK_AUTO) {

      // Calculate the value to offset the next box vertically
      //
      $viewport = $context->get_viewport();

      if ($viewport->get_top() == $parent->_current_y) {
        $page_fraction = -1;
      } else {
        $page_fraction = 
          ($viewport->get_top() - $parent->_current_y) / $viewport->get_height() -
          ceil(($viewport->get_top() - $parent->_current_y) / $viewport->get_height());
      };

      $parent->_current_y += $viewport->get_height() * $page_fraction;

      $context->pop_collapsed_margin();
      $context->push_collapsed_margin(0);

      $parent->extend_height($parent->_current_y-10);
    };
  }

  function get_descender() {
    return 0;
  }

  function get_ascender() {
    return 0;
  }

  function get_css_left_value() {
    if (is_null($this->left)) { return 0; }
    return $this->left;
  }

  function _get_vert_extra() {
    return
      $this->get_extra_top() +
      $this->get_extra_bottom();
  }

  function _get_hor_extra() {
    return
      $this->get_extra_left() + 
      $this->get_extra_right();
  }
  
  // Width:
  // 'get-min-width' stub
  function get_min_width(&$context) {
    die("OOPS! Unoverridden get_min_width called in class ".get_class($this)." inside ".get_class($this->parent));
  }

  // 'get-max-width' stub
  function get_max_width(&$context) {
    die("OOPS! Unoverridden get_max_width called in class ".get_class($this)." inside ".get_class($this->parent));
  }

  function get_max_width_natural(&$context) {
    return $this->get_max_width($context);
  }

  function get_full_width() { 
    // TODO: constraints
    return $this->get_width() + $this->_get_hor_extra(); 
  }

  function put_full_width($value) {
    // Calculate value of additional horizontal space consumed by margins and padding
    $extra = $this->_get_hor_extra();
    $this->width = $value - $extra;
  }

  // Width constraint 
  function put_width_constraint(&$wc) {
    $this->_width_constraint = $wc;
  }

  function _get_containing_block() {
    if ($this->position == POSITION_ABSOLUTE) {
      return $this->_get_containing_block_absolute();
    } else {
      return $this->_get_containing_block_static();
    };
  }

  // Get the position and size of containing block for current 
  // ABSOLUTE POSITIONED element. It is assumed that this function
  // is called for ABSOLUTE positioned boxes ONLY
  //
  // @return associative array with 'top', 'bottom', 'right' and 'left' 
  // indices in data space describing the position of containing block
  //
  function _get_containing_block_absolute() {
    $parent =& $this->parent;

    // No containing block at all... 
    // How could we get here?
    if (is_null($parent)) { die("No containing block found for absolute-positioned element"); };

    // CSS 2.1:
    // If the element has 'position: absolute', the containing block is established by the 
    // nearest ancestor with a 'position' of 'absolute', 'relative' or 'fixed', in the following way:
    // - In the case that the ancestor is inline-level, the containing block depends on 
    //   the 'direction' property of the ancestor:
    //   1. If the 'direction' is 'ltr', the top and left of the containing block are the top and left 
    //      content edges of the first box generated by the ancestor, and the bottom and right are the 
    //      bottom and right content edges of the last box of the ancestor.
    //   2. If the 'direction' is 'rtl', the top and right are the top and right edges of the first
    //      box generated by the ancestor, and the bottom and left are the bottom and left content 
    //      edges of the last box of the ancestor.
    // - Otherwise, the containing block is formed by the padding edge of the ancestor.
    // TODO: inline-level ancestors
    while ((!is_null($parent->parent)) && 
           ($parent->position === POSITION_STATIC)) { $parent =& $parent->parent; }

    // Note that initial containg block (containig BODY element) will be formed by BODY margin edge,
    // unlike other blocks which are formed by content edges
    
    if ($parent->parent) {
      // Normal containing block
      $containing_block = array();
      $containing_block['left']   = $parent->get_left();
      $containing_block['right']  = $parent->get_right();
      $containing_block['top']    = $parent->get_top();
      $containing_block['bottom'] = $parent->get_bottom();
    } else {
      // Initial containing block 
      $containing_block = array();
      $containing_block['left']   = $parent->get_left_margin();
      $containing_block['right']  = $parent->get_right_margin();
      $containing_block['top']    = $parent->get_top_margin();
      $containing_block['bottom'] = $parent->get_bottom_margin();
    };

    return $containing_block;
  }

  // CSS 2.1:
  // 9.2.1 Block-level elements and block boxes
  // Block-level elements are those elements of the source document that are formatted visually as blocks 
  // (e.g., paragraphs). Several values of the 'display' property make an element block-level: 
  // 'block', 'list-item', 'compact' and 'run-in' (part of the time; see compact and run-in boxes), and 'table'. 
  //
  // Note from author: I've added the table-cell here, as parcentage width of the elements inside the cell 
  // should be calculated baing on the cell width, not containing table width!
  //
  function is_block_level() {
    return 
      $this->display == 'block' ||
      $this->display == 'list-item' ||
      $this->display == 'compact' ||
      $this->display == 'run-in' ||
      $this->display == 'table' ||
      $this->display == 'table-cell';
  }

  function _get_containing_block_static() {
    $parent =& $this->parent;

    // No containing block at all... 
    // How could we get here?
    if (is_null($parent)) { die("No containing block found for static-positioned element"); };

    while (($parent->parent !== null) && 
           (!$parent->is_block_level())) { $parent =& $parent->parent; }

    // Note that initial containg block (containig BODY element) will be formed by BODY margin edge,
    // unlike other blocks which are formed by content edges
    
    $containing_block = array();
    $containing_block['left']   = $parent->get_left();
    $containing_block['right']  = $parent->get_right();
    $containing_block['top']    = $parent->get_top();
    $containing_block['bottom'] = $parent->get_bottom();

    return $containing_block;
  }

  // Height constraint 
  function get_height_constraint() {
    return $this->_height_constraint;
  }
  
  function put_height_constraint(&$wc) {
    $this->_height_constraint = $wc;
  }

  function no_width_constraint() {
    return !((bool)$this->_width_constraint);
  }

  // Extends the box height to cover the given Y coordinate
  // If box height is already big enough, no changes will be made
  //
  // @param $y_coord Y coordinate should be covered by the box
  //
  function extend_height($y_coord) {
    $this->put_height(max($this->get_height(), $this->get_top() - $y_coord));
  }

  function extend_width($x_coord) {
    $this->put_width(max($this->get_width(), $x_coord - $this->get_left()));
  }

  function get_extra_bottom() {
    $border = $this->border->bottom;
    return 
      $this->get_margin_bottom() + 
      $border->get_width() + 
      $this->get_padding_bottom();
  }

  function get_extra_left() {
    $border = $this->border->left;
    return 
      $this->get_margin_left() + 
      $border->get_width() + 
      $this->get_padding_left();
  }

  function get_extra_right() {
    $border = $this->border->right;
    return 
      $this->get_margin_right() + 
      $border->get_width() + 
      $this->get_padding_right();
  }

  function get_extra_top() {
    $border = $this->border->top;
    return 
      $this->get_margin_top() + 
      $border->get_width() + 
      $this->get_padding_top();
  }

  function get_extra_line_left() { return 0; }
  function get_extra_line_right() { return 0; }

  function get_margin_bottom() { return $this->margin->bottom->value; }
  function get_margin_left() { return $this->margin->left->value; }
  function get_margin_right() { return $this->margin->right->value; }
  function get_margin_top() { return $this->margin->top->value; }

  function get_padding_right() { return $this->padding->right->value; }

  function get_padding_left() { return $this->padding->left->value; }

  function get_padding_top() { return $this->padding->top->value; }
  function get_border_top_width() { return $this->border->top->width; }

  function get_padding_bottom() { return $this->padding->bottom->value; }

  function get_left_border()    { return $this->get_left() - $this->padding->left->value - $this->border->left->get_width(); }
  function get_right_border()   { return $this->get_left() + 
                                    $this->get_width() + 
                                    $this->padding->right->value + 
                                    $this->border->right->get_width(); }
  function get_top_border()     { return $this->get_top_padding() + $this->border->top->get_width();  }
  function get_bottom_border()  { return $this->get_bottom_padding()  - 
                                    $this->border->bottom->get_width();  }

  function get_left_padding()   { return $this->get_left()- $this->padding->left->value; }
  function get_right_padding()  { return $this->get_left()+ $this->get_width() + $this->padding->right->value; }
  function get_top_padding()    { return $this->get_top() + $this->padding->top->value; }
  function get_bottom_padding() { return $this->get_bottom() - $this->padding->bottom->value; }

  function get_left_margin()    { return $this->get_left() - 
                                    $this->padding->left->value - 
                                    $this->border->left->get_width() - 
                                    $this->margin->left->value; }
  function get_right_margin()   { return $this->get_right() + 
                                    $this->padding->right->value + 
                                    $this->border->right->get_width() + 
                                    $this->margin->right->value; }
  function get_bottom_margin()  { 
    return 
      $this->get_bottom() - 
      $this->get_extra_bottom();
  }
  function get_top_margin()     { return $this->get_top_border() + $this->margin->top->value; }

  function put_top($value)  { $this->_top = $value + $this->baseline_offset(); }
  function put_left($value) { $this->_left = $value; }

  function get_top() { 
    return $this->_top - $this->baseline_offset(); 
  }

  function baseline_offset() {
    return $this->baseline - $this->default_baseline;
  }

  function get_bottom() { 
    return $this->get_top() - $this->get_height();
  }

  function get_right() { 
    return $this->get_left() + $this->get_width(); 
  }

  function get_left() { 
    return $this->_left; 
  }

  // Geometry
  function contains_point_margin($x, $y) {
    // Actually, we treat a small area around the float as "inside" float;
    // it will help us to prevent incorrectly positioning float due the rounding errors
    $eps = 0.1;
    return 
      $this->get_left_margin()-$eps   <= $x &&
      $this->get_right_margin()+$eps  >= $x &&
      $this->get_top_margin()+$eps    >= $y &&
      $this->get_bottom_margin()      <  $y;
  }

  function get_baseline() { 
    return $this->baseline;
  }

  function get_width() {
    if ($this->parent) {
      return $this->_width_constraint->apply($this->width, $this->parent->width);
    } else {
      return $this->_width_constraint->apply($this->width, $this->width);
    }
  }
  
  // Unlike real/constrained width, or min/max width,
  // expandable width shows the size current box CAN be expanded;
  // it is pretty obvious that width-constrained boxes will never be expanded;
  // any other box can be expanded up to its parent _expandable_ width - 
  // as parent can be expanded too. 
  //
  function get_expandable_width() {
    if (is_a($this->_width_constraint,"wcnone") && $this->parent) {
      return $this->parent->get_expandable_width();
    } else {
      return $this->get_width();
    };
  }

  function put_width($value) {
    // TODO: constraints
    $this->width = $value;
  }

  function get_height() {
    if ($this->_height_constraint->applicable($this)) {
      return $this->_height_constraint->apply($this->height, $this);
    } else {
      return $this->height;
    };
  }

  function get_height_padded() {
    return $this->get_height() + $this->get_padding_top() + $this->get_padding_bottom();
  }

  function put_height($value) {
    if ($this->_height_constraint->applicable($this)) {
      $this->height = $this->_height_constraint->apply($value, $this);
    } else {
      $this->height = $value;
    };
  }

  function put_full_height($value) {
    $this->put_height($value - $this->_get_vert_extra());
  }

  // Returns total height of current element: 
  // top padding + top margin + content + bottom padding + bottom margin + top border + bottom border
  function get_full_height() {
    return $this->get_height() + 
      $this->get_extra_top() +
      $this->get_extra_bottom();
  }

  function get_baseline_offset() { return $this->baseline - $this->default_baseline; }

  function get_real_full_height() { return $this->get_full_height(); }

  function offset($dx, $dy) {
    // TODO: absolute-positioned boxes
    $this->_left += $dx;
    $this->_top  += $dy;
  }

  function out_of_flow() {
    return 
      $this->position == POSITION_ABSOLUTE ||
      $this->position == POSITION_FIXED ||
      $this->display == 'none';
  }

  function moveto($x, $y) { $this->offset($x - $this->get_left(), $y - $this->get_top()); }

  function _get_last_box() { return $this->_last; }
  function put_last(&$box) { $this->_last =& $box; }

  function reflow(&$parent, &$context) {
    if ($parent) { $parent->put_last($this); }
  }

  // Note that unlike PS, we have upside-down coordinate system, starting at the ver 
  //
  function reflow_anchors(&$viewport, &$anchors) {
    if ($this->pseudo_link_destination !== "") {
      $page = 2 - round(($this->get_bottom() - 
                         mm2pt($viewport->media->margins['bottom'])) / mm2pt($viewport->media->real_height()));
      $x = $this->get_left();
      $y = 
        ($this->get_bottom() - mm2pt($viewport->media->margins['bottom'])) +
        ($page - 1) * mm2pt($viewport->media->real_height()) + 
        mm2pt($viewport->media->margins['bottom']);
        $anchors[$this->pseudo_link_destination] = new Anchor($this->pseudo_link_destination, $page, $x, $y);
    };
  }

  function reflow_inline() { }

  function reflow_text() { return true; }

  function show(&$viewport) {
    if (CSSPseudoLinkTarget::is_external_link($this->pseudo_link_target)) {
      $viewport->add_link($this->get_left(), 
                          $this->get_top(), 
                          $this->get_width(), 
                          $this->get_height(), 
                          $this->pseudo_link_target);
    };

    if (CSSPseudoLinkTarget::is_local_link($this->pseudo_link_target)) {
      if (isset($viewport->anchors[substr($this->pseudo_link_target,1)])) {
        $anchor = $viewport->anchors[substr($this->pseudo_link_target,1)];
        $viewport->add_local_link($this->get_left(), 
                                  $this->get_top(), 
                                  $this->get_width(), 
                                  $this->get_height(), 
                                  $anchor);
      };
    };

    // Draw border of the box
    $this->border->show($viewport, $this);

    // Render background of the box
    $this->background->show($viewport, $this);

    // If debugging mode is on, draw the box outline
    global $g_config;
    if ($g_config['debugbox']) {
      // Copy the border object of current box
      $viewport->setlinewidth(0.1);
      $viewport->setrgbcolor(0,0,0);
      $viewport->rect($this->get_left(), $this->get_top(), $this->get_width(), -$this->get_height());
      $viewport->stroke();
    }

    // Set current text color
    // Note that text color is used not only for text drawing (for example, list item markers 
    // are drawn with text color)
    $this->color->apply($viewport);

    return true;
  }

  function show_fixed(&$viewport) {
    return $this->show($viewport);
  }

  /**
   * Note that linebox is started by any non-whitespace inline element; all whitespace elements before
   * that moment should be ignored.
   *
   * @param boolean $linebox_started Flag indicating that a new line box have just started and it already contains 
   * some inline elements 
   * @param boolean $previous_whitespace Flag indicating that a previous inline element was an whitespace element.
   */
  function reflow_whitespace(&$linebox_started, &$previous_whitespace) {
    return;
  }

  function is_null() { 
    return false; 
  }

  // Calculate the content upper-left corner position in curent flow
  function guess_corner(&$parent) {
    $this->put_left($parent->_current_x + $this->get_extra_left());
    $this->put_top($parent->_current_y - $this->get_extra_top());
  }

  // Calculate the vertical offset of current box due the 'clear' CSS property
  // 
  // @param $y initial Y coordinate to begin offset from
  // @param $context flow context containing the list of floats to interact with
  // @return updated value of Y coordinate
  //
  function apply_clear($y, &$context) {
    // Check if we need to offset box vertically due the 'clear' property
    if ($this->clear == CLEAR_BOTH || $this->clear == CLEAR_LEFT) {
      $floats =& $context->current_floats();
      for ($cf = 0; $cf < count($floats); $cf++) {
        $current_float =& $floats[$cf];
        if ($current_float->float == FLOAT_LEFT) {
          // Float vertical margins are never collapsed
          //
          $y = min($y, $current_float->get_bottom_margin() - $this->margin->top->value);
        };
      }
    };
    
    if ($this->clear == CLEAR_BOTH || $this->clear == CLEAR_RIGHT) {
      $floats =& $context->current_floats();
      for ($cf = 0; $cf < count($floats); $cf++) {
        $current_float =& $floats[$cf];
        if ($current_float->float == FLOAT_RIGHT) {
          // Float vertical margins are never collapsed
          $y = min($y, $current_float->get_bottom_margin() - $this->margin->top->value);
        };
      }
    };
    
    return $y;
  }

  function line_break_allowed() { 
    return 
      $this->white_space === WHITESPACE_NORMAL && 
      $this->pseudo_nowrap === NOWRAP_NORMAL;
  }

  function pre_reflow_images() {}

  function to_ps(&$psdata) {
    die("Unoverridden 'to_ps' method called in ".get_class($this));
  }

  function to_ps_common(&$psdata) {
    $psdata->write($this->uid." 1 index put-uid \n");
  }

  function to_ps_css(&$psdata) {
    // Save CSS properties to PS file
    // 'background'
    if (!$this->background->is_default()) {
      $psdata->write("dup /background ".$this->background->to_ps($psdata)." put-css-value\n");
    };

    // 'border'
    if (!$this->border->is_default()) {
      $psdata->write("dup /border ".$this->border->to_ps()." put-css-value\n");
    };

    // 'cellpadding'
    if ($this->cellpadding !== units2pt(CSSCellPadding::default_value())) {
      $psdata->write("dup /cellpadding ".$this->cellpadding." put-css-value\n");
    };

    // 'cellspacing'
    if ($this->cellspacing !== units2pt(CSSCellSpacing::default_value())) {
      $psdata->write("dup /cellspacing ".$this->cellspacing." put-css-value\n");
    };

    // 'clear'
    if ($this->clear !== CSSClear::default_value()) {
      $psdata->write("dup /clear ".CSSClear::value2ps($this->clear)." put-css-value\n");
    };

    // 'color'
    if (!$this->color->equals(CSSColor::default_value())) {
      $psdata->write("dup /color ".$this->color->to_ps()." put-css-value\n");
    };

    // 'content' 
    // required only at the moment of tree building; not needed when flowing

    // 'display'
    if ($this->display !== CSSDisplay::default_value()) {
      $psdata->write("dup /display "."/".$this->display." put-css-value\n");
    };

    // 'float'
    if ($this->float !== CSSFloat::default_value()) {
      $psdata->write("dup ".CSSFloat::value2ps($this->float)." /float exch put-css-value\n");
    };
    
    // 'font-size'
    if ($this->font_size !== units2pt(default_font_size())) {
      $psdata->write("dup /font-size ".$this->font_size." put-css-value\n");
    };

    // 'font-family'
    // 'font-weight'
    // 'font-style'
    // are written in box.text.php
    
    // 'height'
    if (!$this->_height_constraint->is_null()) {
      $psdata->write($this->_height_constraint->to_ps() . " 1 index put-height-constraint\n");
    };

    // 'line-height'
    if (!$this->line_height->is_default()) {
      $psdata->write("dup /line-height ".$this->line_height->to_ps()." put-css-value\n");
    };

    // 'list-style'
    if (!$this->list_style->is_default()) {
      $psdata->write("dup /list-style ".$this->list_style->to_ps()." put-css-value\n");
    };

    // 'margin'
    if (!$this->margin->is_default()) {
      $psdata->write("dup /margin ".$this->margin->to_ps()." put-css-value\n");
    };

    // 'overflow'
    if ($this->overflow !== CSSOverflow::default_value()) {
      $psdata->write("dup /overflow ".CSSOverflow::value2ps($this->overflow)." put-css-value\n");
    };

    // 'padding'
    if (!$this->padding->is_default()) {
      $psdata->write("dup /padding ".$this->padding->to_ps()." put-css-value\n");
    };

    // 'page-break-after'
    if ($this->page_break_after !== CSSPageBreakAfter::default_value()) {
      $psdata->write("dup /page-break-after ".CSSPageBreakAfter::value2ps($this->page_break_after)." put-css-value\n");
    };
    
    // 'position'
    if ($this->position !== CSSPosition::default_value()) {
      $psdata->write("dup /position ".CSSPosition::value2ps($this->position)." put-css-value\n");
    };
    
    // 'text-align'
    if ($this->text_align !== CSSTextAlign::default_value()) {
      $psdata->write("dup /text-align ".CSSTextAlign::value2ps($this->text_align)." put-css-value\n");
    };

    // 'text-indent'
    if (!$this->text_indent->is_default()) {
      $psdata->write("dup /text-indent ".$this->text_indent->to_ps()." put-css-value\n");
    };

    // 'text-decoration'
    if ($this->decoration !== CSSTextDecoration::default_value()) {
      $psdata->write("dup /text-decoration ".CSSTextDecoration::value2ps($this->decoration)." put-css-value\n");
    };

    // 'vertical-align'
    if ($this->vertical_align !== CSSVerticalAlign::default_value()) {
      $psdata->write("dup /vertical-align ".CSSVerticalAlign::value2ps($this->vertical_align)." put-css-value\n");
    };

    // 'visibility'
    if ($this->visibility !== CSSVisibility::default_value()) {
      $psdata->write("dup /visibility ".CSSVisibility::value2ps($this->visibility)." put-css-value\n");
    };
    
    // 'width'
    if (!is_a($this->_width_constraint,"WCNone")) {
      $psdata->write($this->_width_constraint->to_ps() . " 1 index put-width-constraint\n");
    };

    // 'white-space'
    if ($this->white_space !== CSSWhiteSpace::default_value()) {
      $psdata->write("dup /white-space ".CSSWhiteSpace::value2ps($this->white_space)." put-css-value\n");
    };

    // CSS positioning properties
    // 'left'
    if ($this->get_css_left_value() !== CSSLeft::default_value()) {
      $psdata->write("dup /left ".CSSLeft::value2ps($this->get_css_left_value())." put-css-value\n");
    };
    
    // 'top'
    if ($this->top !== CSSTop::default_value()) {
      $psdata->write("dup /top ".CSSTop::value2ps($this->top)." put-css-value\n");
    };
    
//     // 'bottom'
//     // TODO: automatic height calculation
//     $handler =& get_css_handler('bottom');
//     $this->bottom = $handler->get();
//     // 'right'
//     // TODO: automatic width calculation
//     $handler =& get_css_handler('right');
//     $this->right = $handler->get();

    // 'PSEUDO-CSS' properties
    // '-align'
    if ($this->pseudo_align !== CSSPseudoAlign::default_value()) {
      $psdata->write("dup /pseudo-align ".CSSPseudoAlign::value2ps($this->pseudo_align)." put-css-value\n");
    };

    // '-localalign'
    // used during box initialization; affected the 'auto' margin values

    // '-html2ps-link-destination'
    if ($this->pseudo_link_destination !== "") {
      $psdata->write("dup /pseudo-link-destination ".CSSPseudoLinkDestination::value2ps($this->pseudo_link_destination)." put-css-value\n");
    };

    // '-html2ps-link-target'
    if ($this->pseudo_link_target !== "") {
      $psdata->write("dup /pseudo-link-target ".CSSPseudoLinkTarget::value2ps($this->pseudo_link_target)." put-css-value\n");
    };

    // '-nowrap'
    if ($this->pseudo_nowrap !== CSSPseudoNoWrap::default_value()) {
      $psdata->write("dup /pseudo-nowrap ".CSSPseudoNoWrap::value2ps($this->pseudo_nowrap)." put-css-value\n");
    };
  }
}
?>