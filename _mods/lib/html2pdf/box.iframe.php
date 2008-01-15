<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/box.iframe.php,v 1.1 2008-01-15 09:20:26 mose Exp $

class IFrameBox extends InlineBlockBox {
  function &create(&$root) {
    return new IFrameBox($root);
  }

  // Note that IFRAME width is NOT determined by its content, thus we need to override 'get_min_width' and
  // 'get_max_width'; they should return the constrained frame width.
  function get_min_width(&$context) { 
    return $this->get_max_width($context);
  } 

  function get_max_width(&$context) {
    return $this->get_width();
  }

  function IFrameBox(&$root) {
    // Inherit 'border' CSS value from parent (FRAMESET tag), if current FRAME 
    // has no FRAMEBORDER attribute, and FRAMESET has one
    $parent = $root->parent();
    if (!$root->has_attribute('frameborder') &&
        $parent->has_attribute('frameborder')) {
      pop_border();
      push_border(get_border());
    }

    $this->InlineBlockBox();

    // If NO src attribute specified, just return.
    if (!$root->has_attribute('src')) { return; };

    // Determine the fullly qualified URL of the frame content
    $src = $root->get_attribute('src');
    global $g_baseurl;
    $url = guess_url($src, $g_baseurl);

    // Fetch the given URL
    $fetcher = new FetcherURL();

    $data = $fetcher->get_data($url);

    if ($fetcher->code == HTTP_OK) {
      $html = $data->get_content();
      
      // Possilby we have been redirected somewhere; update baseurl
      global $g_baseurl;
      $old_base_url = $g_baseurl;
      $g_baseurl = $fetcher->url;

      // Remove control symbols if any
      $html = preg_replace('/[\x00-\x07]/', "", $html);
      $converter = Converter::create();
      $html = $converter->to_utf8($html, $data->detect_encoding());
      $html = html2xhtml($html);
      $tree = TreeBuilder::build($html);
        
      // Save current stylesheet, as each frame may load its own stylesheets
      //
      global $g_css;
      $old_css = $g_css;
      global $g_css_obj;
      $old_obj = $g_css_obj;

      scan_styles($tree);
      // Temporary hack: convert CSS rule array to CSS object
      $g_css_obj = new CSSObject;
      foreach ($g_css as $rule) {
        $g_css_obj->add_rule($rule);
      }
 
      // TODO: stinks. Rewrite
      global $psdata;
      $frame_root = traverse_dom_tree_pdf($tree);

      $box_child =& create_pdf_box($frame_root);
      $this->add_child($box_child);

      // Restore old stylesheet
      //
      $g_css = $old_css;
      $g_css_obj = $old_obj;

      $g_baseurl = $old_base_url;
    }
  }

  function to_ps(&$psdata) {
    $psdata->write("box-iframe-create\n");
    $this->to_ps_common($psdata);
    $this->to_ps_css($psdata);
    $this->to_ps_content($psdata);
    $psdata->write("add-child\n");    
  }
}

?>