<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/html.attrs.inc.php,v 1.1 2008-01-15 09:21:05 mose Exp $

$g_tag_attrs = array(
                     'a'       => array(
                                        'href' => 'attr_href',
                                        'name' => 'attr_name'
                                        ),
                     'body'    => array(
                                        'background'   => 'attr_background',
                                        'bgcolor'      => 'attr_bgcolor',
                                        'text'         => 'attr_body_text',
                                        'link'         => 'attr_body_link',
                                        'topmargin'    => 'attr_body_topmargin',
                                        'leftmargin'   => 'attr_body_leftmargin',
                                        'marginheight' => 'attr_body_marginheight',
                                        'marginwidth'  => 'attr_body_marginwidth'
                                        ),
                     'div'     => array(
                                        'align' => 'attr_align'
                                        ),
                     'font'    => array(
                                        'size'  => 'attr_font_size',
                                        'color' => 'attr_font_color',
                                        'face'  => 'attr_font_face'
                                        ),
                     'form'    => array(
                                          'action'  => 'attr_form_action'
                                          ),
                     'frame'   => array(
                                        'frameborder'  => 'attr_frameborder',
                                        'marginwidth'  => 'attr_iframe_marginwidth',
                                        'marginheight' => 'attr_iframe_marginheight'
                                        ),
                     'frameset'=> array(
                                        'frameborder' => 'attr_frameborder'
                                        ),
                     'h1'      => array(
                                        'align' => 'attr_align'
                                        ),
                     'h2'      => array(
                                        'align' => 'attr_align'
                                        ),
                     'h3'      => array(
                                        'align' => 'attr_align'
                                        ),
                     'h4'      => array(
                                        'align' => 'attr_align'
                                        ),
                     'h5'      => array(
                                        'align' => 'attr_align'
                                        ),
                     'h6'      => array(
                                        'align' => 'attr_align'
                                        ),
                     'hr'      => array(
                                        'align' => 'attr_self_align',
                                        'width' => 'attr_width'
                                        ),
                     'input'   => array(
                                        'name'  => 'attr_input_name'
                                        ),
                     'iframe'  => array(
                                        'frameborder'  => 'attr_frameborder',
                                        'marginwidth'  => 'attr_iframe_marginwidth',
                                        'marginheight' => 'attr_iframe_marginheight',
                                        'height'       => 'attr_height_required',
                                        'width'        => 'attr_width'
                                        ),
                     'img'     => array(
                                        'width'  => 'attr_width',
                                        'height' => 'attr_height',
                                        'border' => 'attr_border',
                                        'hspace' => 'attr_hspace',
                                        'vspace' => 'attr_vspace',
                                        'align'  => 'attr_img_align'
                                        ),
                     'marquee' => array(
                                        'width'  => 'attr_width', 
                                        'height' => 'attr_height_required'
                                        ),
                     'object'  => array(
                                        'width'  => 'attr_width', 
                                        'height' => 'attr_height'
                                        ),
                     'ol'      => array(
                                        'start' => 'attr_start'
                                        ),
                     'p'       => array(
                                        'align' => 'attr_align'
                                        ),
                     'table'   => array(
                                        'border'      => 'attr_table_border', 
                                        'bordercolor' => 'attr_table_bordercolor', 
                                        'align'       => 'attr_table_float_align',
                                        'bgcolor'     => 'attr_bgcolor',
                                        'width'       => 'attr_width',
                                        'background'  => 'attr_background', 
                                        'height'      => 'attr_height', 
                                        'cellspacing' => 'attr_cellspacing', 
                                        'cellpadding' => 'attr_cellpadding'
                                        ),
                     'td'      => array(
                                        'align'      => 'attr_align', 
                                        'valign'     => 'attr_valign', 
                                        'height'     => 'attr_height', 
                                        'background' => 'attr_background', 
                                        'bgcolor'    => 'attr_bgcolor',
                                        'nowrap'     => 'attr_nowrap',
                                        'width'      => 'attr_width'
                                        ),
                     'th'      => array(
                                        'align'      => 'attr_align', 
                                        'valign'     => 'attr_valign', 
                                        'height'     => 'attr_height', 
                                        'background' => 'attr_background', 
                                        'bgcolor'    => 'attr_bgcolor',
                                        'nowrap'     => 'attr_nowrap',
                                        'width'      => 'attr_width'
                                        ),
                     'tr'      => array(
                                        'align'   => 'attr_align',
                                        'bgcolor' => 'attr_bgcolor', 
                                        'valign'  => 'attr_row_valign', 
                                        'height'  => 'attr_height'
                                        ),
                     'ul'      => array(
                                        'start' => 'attr_start'
                                        )
);


function execute_attrs_before($root) { execute_attrs($root, "_before"); }
function execute_attrs_after($root) { execute_attrs($root, "_after"); }
function execute_attrs_after_styles($root) { execute_attrs($root, "_after_styles"); }

function execute_attrs(&$root, $suffix) {
  global $g_tag_attrs;

  if (array_key_exists($root->tagname(), $g_tag_attrs)) {
    foreach ($g_tag_attrs[$root->tagname()] as $attr => $fun) {
      if ($root->has_attribute($attr)) {
        $fun = $fun.$suffix;
        $fun($root);
      };
    };
  };
};

// ========= Handlers

// A NAME
function attr_name_before(&$root) {
  $handler =& get_css_handler('-html2ps-link-destination');
  $handler->css($root->get_attribute("name"));
}
function attr_name_after_styles(&$root) {};
function attr_name_after(&$root) {};


// A HREF
function attr_href_before(&$root) {
  $handler =& get_css_handler('-html2ps-link-target');
  $handler->css($root->get_attribute("href"));
}
function attr_href_after_styles(&$root) {};
function attr_href_after(&$root) {};

// IFRAME 
function attr_frameborder_before(&$root) {
  if ($root->get_attribute("frameborder") == "1") {
    css_border("inset black 1px",$root);
  } else {
    pop_border();
    push_border(default_border());
  };
}
function attr_frameborder_after_styles(&$root) {};
function attr_frameborder_after(&$root) {};

function attr_iframe_marginheight_before(&$root) {
  $handler =& get_css_handler('padding-top');
  $handler->css((int)$root->get_attribute("marginheight")."px");
  $handler =& get_css_handler('padding-bottom');
  $handler->css((int)$root->get_attribute("marginheight")."px");
}
function attr_iframe_marginheight_after_styles(&$root) {};
function attr_iframe_marginheight_after(&$root) {};

function attr_iframe_marginwidth_before(&$root) {
  $handler =& get_css_handler('padding-right');
  $handler->css((int)$root->get_attribute("marginwidth")."px");
  $handler =& get_css_handler('padding-left');
  $handler->css((int)$root->get_attribute("marginwidth")."px");
}
function attr_iframe_marginwidth_after_styles(&$root) {};
function attr_iframe_marginwidth_after(&$root) {};


// BODY-specific
function attr_body_text_before(&$root) {
  $handler =& get_css_handler('color');
  $handler->css($root->get_attribute("text"));
}
function attr_body_text_after_styles(&$root) {};
function attr_body_text_after(&$root) {};

function attr_body_link_before(&$root) {
  $color = $root->get_attribute("link");

  // -1000 means priority modifier; so, any real CSS rule will have more priority than 
  // this fake rule

  $rule = array(array(SELECTOR_SEQUENCE, array(array(SELECTOR_TAG, "a"),
                                               array(SELECTOR_PSEUDOCLASS_LINK_LOW_PRIORITY))),
                array('color' => $color),
                "",
                -1000);

  global $g_css_obj;
  $g_css_obj->add_rule($rule);
} 
function attr_body_link_after_styles(&$root) {};
function attr_body_link_after(&$root) {};

function attr_body_topmargin_before(&$root) {
  $handler =& get_css_handler('padding-top');
  $handler->css((int)$root->get_attribute("topmargin")."px");
}
function attr_body_topmargin_after_styles(&$root) {};
function attr_body_topmargin_after(&$root) {};

function attr_body_leftmargin_before(&$root) {
  $handler =& get_css_handler('padding-left');
  $handler->css((int)$root->get_attribute("leftmargin")."px");
}
function attr_body_leftmargin_after_styles(&$root) {};
function attr_body_leftmargin_after(&$root) {};

function attr_body_marginheight_before(&$root) {
  $h_top    =& get_css_handler('padding-top');
  $h_bottom =& get_css_handler('padding-bottom');

  $top      = $h_top->get();

  $h_bottom->css(((int)$root->get_attribute("marginheight") - $top->value)."px");
}
function attr_body_marginheight_after_styles(&$root) {};
function attr_body_marginheight_after(&$root) {};

function attr_body_marginwidth_before(&$root) {
  $h_left  =& get_css_handler('padding-left');
  $h_right =& get_css_handler('padding-right');

  $left = $h_left->get();

  $h_right->css(((int)$root->get_attribute("marginwidth") - $left->value)."px");
}
function attr_body_marginwidth_after_styles(&$root) {};
function attr_body_marginwidth_after(&$root) {};

// === nowrap
function attr_nowrap_before(&$root) {
  $handler =& get_css_handler('-nowrap');
  $handler->push(NOWRAP_NOWRAP);
} 

function attr_nowrap_after_styles(&$root) {}
function attr_nowrap_after(&$root) {}

// === hspace

function attr_hspace_before(&$root) {
  $handler =& get_css_handler('padding-left');
  $handler->css((int)$root->get_attribute("hspace")."px");
  $handler =& get_css_handler('padding-right');
  $handler->css((int)$root->get_attribute("hspace")."px");
}

function attr_hspace_after_styles(&$root) {}

function attr_hspace_after(&$root) {}

// === vspace

function attr_vspace_before(&$root) {
  $handler =& get_css_handler('padding-top');
  $handler->css((int)$root->get_attribute("vspace")."px");
  $handler =& get_css_handler('padding-bottom');
  $handler->css((int)$root->get_attribute("vspace")."px");
}

function attr_vspace_after_styles(&$root) {}
function attr_vspace_after(&$root) {}

// === background

function attr_background_before(&$root) {
  global $g_baseurl;

  $handler =& get_css_handler('background-image');
  $handler->css("url(".$root->get_attribute("background").")");
}
function attr_background_after_styles(&$root) {}
function attr_background_after(&$root) {}

// === align

function attr_table_float_align_before(&$root) {
  if ($root->get_attribute("align") === "center") {
//       $handler =& get_css_handler('-localalign');
//       $handler->replace(LA_CENTER);
      
    $margin_left =& get_css_handler('margin-left');
    $margin_left->css('auto');
    
    $margin_right =& get_css_handler('margin-right');
    $margin_right->css('auto');
  } else {
    $float =& get_css_handler('float');
    $float->replace($float->parse($root->get_attribute("align")));
  };
}
function attr_table_float_align_after_styles(&$root) {}
function attr_table_float_align_after(&$root) {}

function attr_img_align_before(&$root) {
  if (preg_match("/left|right/", $root->get_attribute("align"))) {
    $float =& get_css_handler('float');
    $float->replace($float->parse($root->get_attribute("align")));
  } else {
    $handler =& get_css_handler('vertical-align');
    $handler->replace($handler->parse($root->get_attribute("align")));
  };
}
function attr_img_align_after_styles(&$root) {}
function attr_img_align_after(&$root) {}

function attr_self_align_before(&$root) {
  $handler =& get_css_handler('-localalign');
  switch ($root->get_attribute("align")) {
  case "left":
    $handler->replace(LA_LEFT);
    break;
  case "center":
    $handler->replace(LA_CENTER);
    break;
  case "right":
    $handler->replace(LA_RIGHT);
    break;
  default:
    $handler->replace(LA_LEFT);
    break;
  };
}

function attr_self_align_after_styles(&$root) {}
function attr_self_align_after(&$root) {}

// === bordercolor

function attr_table_bordercolor_before(&$root) {
  $color = parse_color_declaration($root->get_attribute("bordercolor"), array(0,0,0));

  $border = get_table_border();
  
  $border['left']['color']   = $color;
  $border['right']['color']  = $color;
  $border['top']['color']    = $color;
  $border['bottom']['color'] = $color;
  
  pop_border();
  push_border($border); 
  push_table_border($border);
}

function attr_table_bordercolor_after_styles(&$root) {
  pop_border();
}

function attr_table_bordercolor_after(&$root) {
  pop_table_border();
}

// === border

function attr_border_before(&$root) {
  $width = (int)$root->get_attribute("border");

  $border = get_border();
  $border['left']['width']   = $width . "px";
  $border['right']['width']  = $width . "px";
  $border['top']['width']    = $width . "px";
  $border['bottom']['width'] = $width . "px";
  
  pop_border();
  push_border($border); 
}

function attr_border_after_styles(&$root) {}
function attr_border_after(&$root) {}

// === border (table)

function attr_table_border_before(&$root) {
  $width = (int)$root->get_attribute("border");

  $border = get_table_border();
  $border['left']['width']   = $width . "px";
  $border['right']['width']  = $width . "px";
  $border['top']['width']    = $width . "px";
  $border['bottom']['width'] = $width . "px";
  
  $border['left']['style']   = BS_SOLID;
  $border['right']['style']  = BS_SOLID;
  $border['top']['style']    = BS_SOLID;
  $border['bottom']['style'] = BS_SOLID;
  
  pop_border();
  push_border($border); 

  push_table_border($border);
}

function attr_table_border_after_styles(&$root) {}

function attr_table_border_after(&$root) {
  pop_table_border(); 
}

// === align
function attr_align_before(&$root) {
  $handler =& get_css_handler('text-align');
  $handler->css($root->get_attribute("align")); 

  $handler =& get_css_handler('-align');
  $handler->css($root->get_attribute("align"));
}

function attr_align_after_styles(&$root) {}

function attr_align_after(&$root) {}

// valign
// 'valign' attribute value for table rows is inherited
function attr_row_valign_before(&$root) {
  $handler =& get_css_handler('vertical-align');
  $handler->css($root->get_attribute("valign"));
}
function attr_row_valign_after_styles(&$root) {}
function attr_row_valign_after(&$root) {}

// 'valign' attribute value for boxes other than table rows is not inherited
function attr_valign_before(&$root) {
  $handler =& get_css_handler('vertical-align');
  $handler->css($root->get_attribute("valign"));
}

function attr_valign_after_styles(&$root) {}
function attr_valign_after(&$root) {}
              
// bgcolor

function attr_bgcolor_before(&$root) {
  $handler =& get_css_handler('background-color');
  $handler->css($root->get_attribute("bgcolor")); 
}
function attr_bgcolor_after_styles(&$root) {}
function attr_bgcolor_after(&$root) {}

// width

function attr_width_before(&$root) {
  $width =& get_css_handler('width');
  $width->css($root->get_attribute("width"));
}

function attr_width_after_styles(&$root) {}
function attr_width_after(&$root) {}

// height

// Difference between "attr_height" and "attr_height_required":
// attr_height sets the minimal box height so that is cal be expanded by it content;
// a good example is table rows and cells; on the other side, attr_height_required
// sets the fixed box height - it is useful for boxes which content height can be greater
// that box height - marquee or iframe, for example

function attr_height_required_before(&$root) {
  $handler =& get_css_handler('height');
  $handler->css($root->get_attribute("height"));
}

function attr_height_required_after_styles(&$root) {}

function attr_height_required_after(&$root) {}

function attr_height_before(&$root) {
  $handler =& get_css_handler('min-height');
  $handler->css($root->get_attribute("height"));
}

function attr_height_after_styles(&$root) {}
function attr_height_after(&$root) {}

// FONT attributes
function attr_font_size_before(&$root) {
  $size = $root->get_attribute("size");
  if ($size{0} == "-") {
    $fs = get_font_size();
    $fs_parts = explode(" ", $fs);
    $newsize = $fs_parts[0];
    $unit = count($fs_parts) > 1 ? $fs_parts[1] : "pt"; 
    
    $koeff = 1/1.2;
    $repeats = (int)substr($size,1);
    for ($i=0; $i<$repeats; $i++) {
      $newsize *= $koeff;
    };
    
    $newsize = $newsize . " " . $unit;
  } else if ($size{0} == "+") {
    $fs = get_font_size();
    $fs_parts = explode(" ", $fs);
    $newsize = $fs_parts[0];
    $unit = count($fs_parts) > 1 ? $fs_parts[1] : "pt"; 
    
    $newsize = get_font_size();
    $koeff = 1.2;
    $repeats = (int)substr($size,1);
    for ($i=0; $i<$repeats; $i++) {
      $newsize *= $koeff;
    };    
    
    $newsize = $newsize . " " . $unit;
  } else {
    switch ((int)$size) {
    case 1:
      $newsize = BASE_FONT_SIZE_PT/1.2/1.2;
      break;
    case 2:
      $newsize = BASE_FONT_SIZE_PT/1.2;
      break;
    case 3:
      $newsize = BASE_FONT_SIZE_PT;
      break;
    case 4:
      $newsize = BASE_FONT_SIZE_PT*1.2;
      break;
    case 5:
      $newsize = BASE_FONT_SIZE_PT*1.2*1.2;
      break;
    case 6:
      $newsize = BASE_FONT_SIZE_PT*1.2*1.2*1.2;
      break;
    case 7:
      $newsize = BASE_FONT_SIZE_PT*1.2*1.2*1.2*1.2;
      break;
    default:
      $newsize = BASE_FONT_SIZE_PT;
      break;
    };
    $newsize = $newsize . " pt";
  };

  pop_font_size(); push_font_size($newsize);
}
function attr_font_size_after_styles(&$root) {}
function attr_font_size_after(&$root) {}

function attr_font_color_before(&$root) {
  $handler =& get_css_handler('color');
  $handler->css($root->get_attribute("color"));
}
function attr_font_color_after_styles(&$root) {}
function attr_font_color_after(&$root) {}

function attr_font_face_before(&$root) {
  pop_font_family();
  push_font_family(parse_font_family($root->get_attribute("face")));
}
function attr_font_face_after_styles(&$root) {}
function attr_font_face_after(&$root) {}

function attr_form_action_before(&$root) {
  $handler =& get_css_handler('-html2ps-form-action');
  if ($root->has_attribute('action')) {
    global $g_baseurl;
    $handler->css(guess_url($root->get_attribute('action'), $g_baseurl));
  } else {
    $handler->css(null);
  };
}
function attr_form_action_after_styles(&$root) {}
function attr_form_action_after(&$root) {}

function attr_input_name_before(&$root) {
  $handler =& get_css_handler('-html2ps-form-radiogroup');
  if ($root->has_attribute('name')) {
    $handler->css($root->get_attribute('name'));
  };
}
function attr_input_name_after_styles(&$root) {}
function attr_input_name_after(&$root) {}

// TABLE

function attr_cellspacing_before(&$root) {
  $handler =& get_css_handler('-cellspacing');
  $handler->css($root->get_attribute("cellspacing"));
}
function attr_cellspacing_after_styles(&$root) {}
function attr_cellspacing_after(&$root) {}

function attr_cellpadding_before(&$root) {
  $handler =& get_css_handler('-cellpadding');
  $handler->css($root->get_attribute("cellpadding"));
}
function attr_cellpadding_after_styles(&$root) {}
function attr_cellpadding_after(&$root) {}

// UL/OL 'start' attribute
function attr_start_before(&$root) {
  $handler =& get_css_handler('-list-counter');
  $handler->replace((int)$root->get_attribute("start"));
}
function attr_start_after_styles(&$root) {}
function attr_start_after(&$root) {}

?>