<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/box.php,v 1.1 2008-01-15 09:20:27 mose Exp $

// This variable is used to track the reccurrent framesets
// they can be produced by inaccurate or malicious HTML-coder 
// or by some cookie- or referrer- based identification system
//
$g_frame_level = 0;

// Called when frame node  is to be processed 
function inc_frame_level() {
  global $g_frame_level;
  $g_frame_level ++;

  if ($g_frame_level > MAX_FRAME_NESTING_LEVEL) {
    die("Frame nesting too deep\n");
  };
}

// Called when frame (and all nested frames, of course) processing have been completed
//
function dec_frame_level() {
  global $g_frame_level;
  $g_frame_level --;
}

// Calculate 'display' CSS property according to CSS 2.1 paragraph 9.7 
// "Relationships between 'display', 'position', and 'float'" 
// (The last table in that paragraph)
//
// @return flag indication of current box need a block box wrapper
//
function _fix_display_position_float() {
  // Specified value -> Computed value
  // inline-table -> table
  // inline, run-in, table-row-group, table-column, table-column-group, table-header-group, 
  // table-footer-group, table-row, table-cell, table-caption, inline-block -> block
  // others-> same as specified
  
  $handler =& get_css_handler('display');
  $display = $handler->get();
  $handler->pop();

  switch ($display) {
  case "inline-table":
    $handler->push('table');
    return false;
  case "inline":
  case "run-in":
  case "table-row-group":
  case "table-column":
  case "table-column-group":
  case "table-header-group":
  case "table-footer-group":
  case "table-row":
  case "table-cell":
  case "table-caption":
  case "inline-block":
    // Note that as we're using some non-standard display values, we need to add them to translation table
    $handler->push('block');
    return false;

    // There are display types that cannot be directly converted to block; in this case we need to create a "wrapper" floating 
    // or positioned block box and put our real box into it.
  case "-button":
  case "-iframe":
  case "-radio":
  case "-select":
  case "-text":
  case "-image":
    $handler->push($display);
    return true;

    // Display values that are not affected by "float" property
  case "-button":
  case "-checkbox":
  case "-frame":
  case "-frameset":
  case "-legend":
    // 'block' is assumed here
  default:
    $handler->push($display);
    return false;
  }
}

function &create_pdf_box(&$root) {
  switch ($root->node_type()) {
  case XML_DOCUMENT_NODE:
    // TODO: some magic from traverse_dom_tree
    $box =& BlockBox::create($root);
    break;
  case XML_ELEMENT_NODE:
    // Determine CSS proerty value for current child
    push_css_defaults();

    global $g_css_defaults_obj;
    $g_css_defaults_obj->apply($root);

    // Store the default 'display' value; we'll need it later when checking for impossible tag/display combination
    $handler =& get_css_handler('display');
    $default_display = $handler->get();
    
    // Initially generated boxes do not require block wrappers
    // Block wrappers are required in following cases:
    // - float property is specified for non-block box which cannot be directly converted to block box
    //   (a button, for example)
    // - display set to block for such box 
    $need_block_wrapper = false;

    // TODO: some inheritance magic

    // Order is important. Items with most priority should be applied last
    // Tag attributes
    execute_attrs_before($root);

    // CSS stylesheet
    global $g_css_obj;
    $g_css_obj->apply($root);

    // values from 'style' attribute
    if ($root->has_attribute("style")) { parse_style_attr(null, $root); };
    
    _fix_tag_display($default_display);

    // TODO: do_tag_specials
    // TODO: execute_attrs_after_styles

    // CSS 2.1:
    // 9.7 Relationships between 'display', 'position', and 'float'
    // The three properties that affect box generation and layout — 
    // 'display', 'position', and 'float' — interact as follows:
    // 1. If 'display' has the value 'none', then 'position' and 'float' do not apply. 
    //    In this case, the element generates no box.
    $position_handler =& get_css_handler('position');
    $float_handler    =& get_css_handler('float');

    // 2. Otherwise, if 'position' has the value 'absolute' or 'fixed', the box is absolutely positioned, 
    //    the computed value of 'float' is 'none', and display is set according to the table below. 
    //    The position of the box will be determined by the 'top', 'right', 'bottom' and 'left' properties and 
    //    the box's containing block.
    $position = $position_handler->get();
    if ($position === POSITION_ABSOLUTE || $position === POSITION_FIXED) {
      $float_handler->replace(FLOAT_NONE);
      $need_block_wrapper |= _fix_display_position_float();
    };

    // 3. Otherwise, if 'float' has a value other than 'none', the box is floated and 'display' is set
    //    according to the table below.
    $float = $float_handler->get();
    if ($float != FLOAT_NONE) {
      $need_block_wrapper |= _fix_display_position_float();
    };

    // Process some special nodes
    // BR
    if ($root->tagname() == "br") { 
      $handler =& get_css_handler('display');
      $handler->css('-break');
    };

    if ($root->tagname() == "img") {
      $handler =& get_css_handler('display');
      $need_block_wrapper |= ($handler->get() == "block");
      $handler->css('-image');
    };

    // 4. Otherwise, if the element is the root element, 'display' is set according to the table below.
    // 5. Otherwise, the remaining 'display' property values apply as specified. (see _fix_display_position_float)

    $display_handler =& get_css_handler('display');
    switch($display_handler->get()) {
    case "block":
      $box =& BlockBox::create($root);
      break;
    case "-break":
      $box =& BRBox::create(); 
      break;
    case "-button":
      $box =& ButtonBox::create($root);
      break;      
    case "-checkbox":
      $box =& CheckBox::create($root);
      break;
    case "-frame":
      inc_frame_level();
      $box =& FrameBox::create($root);
      dec_frame_level();
      break;
    case "-frameset":
      inc_frame_level();
      $box =& FramesetBox::create($root);
      dec_frame_level();
      break;      
    case "-iframe":
      inc_frame_level();
      $box =& IFrameBox::create($root);
      dec_frame_level();
      break;
    case "-image":
      $box =& IMGBox::create($root);      
      break;
    case "inline":
      $box =& InlineBox::create($root);
      break;
    case "inline-block":
      $box =& InlineBlockBox::create($root);
      break;
    case "-legend":
      $box =& LegendBox::create($root);
      break;
    case "list-item":
      $box =& ListItemBox::create($root);
      break;
    case "none":
      $box =& NullBox::create($root);
      break;
    case "-radio":
      $box =& RadioBox::create($root);
      break;
    case "-select":
      $box =& SelectBox::create($root);
      break;
    case "table":
      $box =& TableBox::create($root);
      break;
    case "table-cell":
      $box =& TableCellBox::create($root);
      break;
    case "table-row":
      $box =& TableRowBox::create($root);
      break;
    case "-table-section":
      $box =& TableSectionBox::create($root);
      break;
    case "-text":
      $box =& TextInputBox::create($root);
      break;
    default:
      die("Unsupported 'display' value: ".$display_handler->get());
    }

    // Now check if pseudoelement should be created; in this case we'll use the "inline wrapper" box
    // containing both generated box and pseudoelements
    //
    if ($box->content_pseudoelement !== "") {
      $content_handler =& get_css_handler('content');
      
      // Check if :before preudoelement exists
      $before = create_pdf_pseudoelement($root, SELECTOR_PSEUDOELEMENT_BEFORE);
      if ($before) {
        $box->insert_child(0, $before);
      };

      // Check if :after pseudoelement exists
      $after = create_pdf_pseudoelement($root, SELECTOR_PSEUDOELEMENT_AFTER);
      if ($after) {
        $box->add_child($after);
      };
    };

    // Check if this box needs a block wrapper (for example, floating button)
    // Note that to keep float/position information, we clear the CSS stack only
    // AFTER the wrapper box have been created; BUT we should clear the following CSS properties
    // to avoid the fake wrapper box actually affect the layout:
    // - margin
    // - border 
    // - padding 
    // - background
    //
    if ($need_block_wrapper) {
      // Note that if element width have been set as a percentage constraint and we're adding a block wrapper,
      // then we need to:
      // 1. set the same percentage width constraint to the wrapper element (will be done implicilty if we will not
      // modify the 'width' CSS handler stack
      // 2. set the wrapped element's width constraint to 100%, otherwise it will be narrower than expected
      if (is_a($box->_width_constraint, "WCFraction")) {
        $box->_width_constraint = new WCFraction(1);
      } 

      $handler =& get_css_handler('margin');
      $box->margin = $handler->default_value();

      $box->border = new BorderPDF(default_border());

      $handler =& get_css_handler('padding');
      $box->padding = $handler->default_value();

      $handler =& get_css_handler('background');
      $box->background = $handler->default_value();

//       $handler =& get_css_handler('margin');
//       $box->margin = $handler->parse("0");

//       // Clear CSS properties
//       pop_border();
//       push_border(default_border());

//       $handler =& get_css_handler('padding');
//       $handler->css('0');

//       $handler =& get_css_handler('background');
//       $handler->css('transparent');

      // Create "clean" block box
      $wrapper =& new BlockBox();
      $wrapper->add_child($box);

      // Remove CSS propery values from stack
      execute_attrs_after($root);
      pop_css_defaults();

      // Clear CSS properties handled by wrapper
      $box->float = FLOAT_NONE;
      $box->position = POSITION_STATIC;

      return $wrapper;
    } else {
      // Remove CSS propery values from stack
      execute_attrs_after($root);
      pop_css_defaults();
      
      return $box;
    };

    break;
  case XML_TEXT_NODE:
    // Determine CSS property value for current child
    push_css_text_defaults();
    // No text boxes generated by empty text nodes 
    //    if (trim($root->content) !== "") {
    if ($root->content !== "") {
      $box =& InlineBox::create($root);
    } else {
      $box = null;
    }
    // Remove CSS property values from stack
    pop_css_defaults();

    return $box;
    break;
  default:
    die("Unsupported node type:".$root->node_type());
  }  
}

function &create_pdf_pseudoelement($root, $pe_type) {     
  // Store initial values to CSS stack
  //
  push_css_defaults();

  // Apply default stylesheet rules (using base element)
  global $g_css_defaults_obj;
  $g_css_defaults_obj->apply($root);

  // Initially generated boxes do not require block wrappers
  // Block wrappers are required in following cases:
  // - float property is specified for non-block box which cannot be directly converted to block box
  //   (a button, for example)
  // - display set to block for such box 
  $need_block_wrapper = false;

  // Order is important. Items with most priority should be applied last
  // Tag attributes
  execute_attrs_before($root);

  // CSS stylesheet
  global $g_css_obj;
  $g_css_obj->apply($root);
  
  // values from 'style' attribute
  if ($root->has_attribute("style")) { parse_style_attr(null, $root); };
  
  // Pseudoelement-specific rules; be default, it should flow inline
  //
  $handler =& get_css_handler('display');
  $handler->css('inline');
  $handler =& get_css_handler('content');
  $handler->css("");
  $handler =& get_css_handler('float');
  $handler->css("none");
  $handler =& get_css_handler('position');
  $handler->css("static");
  $handler =& get_css_handler('margin');
  $handler->css("0");
  $handler =& get_css_handler('width');
  $handler->css("auto");
  $handler =& get_css_handler('height');
  $handler->css("auto");

  $g_css_obj->apply_pseudoelement($pe_type, $root);

  // Now, if no content found, just return
  //
  $handler =& get_css_handler('content');
  $content = $handler->get();
  if ($content === "") { 
    pop_css_defaults();
    $dummy = null;
    return $dummy; 
  };
  
  // CSS 2.1:
  // 9.7 Relationships between 'display', 'position', and 'float'
  // The three properties that affect box generation and layout — 
  // 'display', 'position', and 'float' — interact as follows:
  // 1. If 'display' has the value 'none', then 'position' and 'float' do not apply. 
  //    In this case, the element generates no box.
  $position_handler =& get_css_handler('position');
  $float_handler    =& get_css_handler('float');
    
  // 2. Otherwise, if 'position' has the value 'absolute' or 'fixed', the box is absolutely positioned, 
  //    the computed value of 'float' is 'none', and display is set according to the table below. 
  //    The position of the box will be determined by the 'top', 'right', 'bottom' and 'left' properties and 
  //    the box's containing block.
  $position = $position_handler->get();
  if ($position === POSITION_ABSOLUTE || $position === POSITION_FIXED) {
    $float_handler->replace(FLOAT_NONE);
    $need_block_wrapper |= _fix_display_position_float();
  };

  // 3. Otherwise, if 'float' has a value other than 'none', the box is floated and 'display' is set
  //    according to the table below.
  $float = $float_handler->get();
  if ($float != FLOAT_NONE) {
    $need_block_wrapper |= _fix_display_position_float();
  };
  
  // 4. Otherwise, if the element is the root element, 'display' is set according to the table below.
  // 5. Otherwise, the remaining 'display' property values apply as specified. (see _fix_display_position_float)
  
  // Note that pseudoelements may get only standard display values
  $display_handler =& get_css_handler('display');
  switch($display_handler->get()) {
  case "block":
    $box =& BlockBox::create_from_text($content);
    break;
  case "inline":
    $box =& InlineBox::create_from_text($content);
    break;
  default:
    die("Unsupported 'display' value: ".$display_handler->get());
  }

  // Check if this box needs a block wrapper (for example, floating button)
  // Note that to keep float/position information, we clear the CSS stack only
  // AFTER the wrapper box have been created; BUT we should clear the following CSS properties
  // to avoid the fake wrapper box actually affect the layout:
  // - margin
  // - border 
  // - padding 
  // - background
  //
  if ($need_block_wrapper) {
    $handler =& get_css_handler('margin');
    $handler->css("0");
    
    pop_border();
    push_border(default_border());
    
    pop_padding();
    push_padding(default_padding());
    
    $handler =& get_css_handler('background');
    $handler->css('transparent');
    
    // Create "clean" block box
    $wrapper =& new BlockBox();
    $wrapper->add_child($box);
    
    // Remove CSS propery values from stack
    execute_attrs_after($root);
    pop_css_defaults();
    
    return $wrapper;
  } else {
    // Remove CSS propery values from stack
    execute_attrs_after($root);
    pop_css_defaults();
    
    return $box;
  };
}

function is_inline(&$box) {
  return 
    $box->display === '-button' ||
    $box->display === '-checkbox' ||
    $box->display === '-image' ||
    $box->display === 'inline' || 
    $box->display === 'inline-block' ||
    $box->display === 'none' ||
    $box->display === '-radio' ||
    $box->display === '-select' ||
    $box->display === '-text';
}

function is_whitespace(&$box) {
  return 
    is_a($box, "WhitespaceBox") ||
    is_a($box, "NullBox");
}

function is_container(&$box) {
  return is_a($box, "GenericContainerBox") && 
    !is_a($box, "GenericInlineBox") || 
    is_a($box, "InlineBox");
}

function is_span(&$box) {
  return is_a($box, "InlineBox");
}

function is_table_cell(&$box) {
  return is_a($box, "TableCellBox");
}
?>