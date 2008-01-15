<?php

class Pipeline {
  var $fetchers;
  var $data_filters;
  var $error_message;
  var $parser;
  var $pre_tree_filters;
  var $layout_engine;
  var $post_tree_filters;
  var $output_driver;
  var $output_filters;
  var $destination;
  
  function process($data_id, &$media) {
    global $g_css;
    $g_css = array();

    if (count($this->fetchers) == 0) { 
      ob_start();
      include('templates/error._no_fetchers.tpl');
      $this->error_message = ob_get_contents();
      ob_end_clean();

      return null; 
    };

    // Fetch data
    $i=0;
    do {
      $fetcher =& $this->fetchers[$i];
      $data = $fetcher->get_data($data_id);
      $i++;
    } while ($data == null && $i < count($this->fetchers));
    if ($data == null) { return null; };
    // Possibly we have been redirected somewhere; update baseurl
    global $g_baseurl;
    $g_baseurl = $fetcher->get_base_url();
    // Run raw data filters
    for ($i=0; $i<count($this->data_filters); $i++) {
      $data = $this->data_filters[$i]->process($data);
    };

    // Parse the raw data
    $box =& $this->parser->process($data->get_content());
    // Run pre-layout tree filters
    for ($i=0; $i<count($this->pre_tree_filters); $i++) {
      $this->pre_tree_filters[$i]->process($box);
    };

    $this->output_driver->reset($media);
    $context = $this->layout_engine->process($box, $media, $this->output_driver);
    if (is_null($context)) { return null; };

    // Run post-layout tree filters
    for ($i=0; $i<count($this->post_tree_filters); $i++) {
      $this->post_tree_filters[$i]->process($box);
    };

    $context->sort_absolute_positioned_by_z_index();
    // Output PDF pages using chosen PDF driver
    for ($i=0; $i<$this->output_driver->get_expected_pages(); $i++) {
      $this->output_driver->save();
      $this->output_driver->setup_clip();

      if (is_null($box->show($this->output_driver))) { 
        return null; 
      };

      // Absolute positioned boxes should be shown after all other boxes, because 
      // they're placed higher in the stack-order
      for ($j=0; $j<count($context->absolute_positioned); $j++) {
        if ($context->absolute_positioned[$j]->visibility === VISIBILITY_VISIBLE) {
          if (is_null($context->absolute_positioned[$j]->show($this->output_driver))) {
            return null;
          };
        };
      };

      $this->output_driver->restore();

      for ($j=0; $j<count($context->fixed_positioned); $j++) {
        if ($context->fixed_positioned[$j]->visibility === VISIBILITY_VISIBLE) {
          if (is_null($context->fixed_positioned[$j]->show_fixed($this->output_driver))) { 
            return null;
          };
        };
      };

      global $g_config;
      if ($g_config['draw_page_border']) { $this->output_driver->draw_page_border(); };
      // Add page if currently rendered page is not last
      if ($i<$this->output_driver->get_expected_pages()-1) { $this->output_driver->next_page(); }
    }

    $this->output_driver->close();

    $filename = $this->output_driver->get_filename();
    for ($i=0; $i<count($this->output_filters); $i++) {
      $filename = $this->output_filters[$i]->process($filename);
    };

    // Determine the content type of the result
    $content_type = null;
    $i = count($this->output_filters)-1;
    while (($i >= 0) && (is_null($content_type))) {
      $content_type = $this->output_filters[$i]->content_type();
      $i--;
    };

    if (is_null($content_type)) {
      $content_type = $this->output_driver->content_type();
    };

    $this->destination->process($filename, $content_type);
    $this->output_driver->release();

    // Non HTML-specific cleanup
    //
    Image::clear_cache();

    return true;
  }

  function error_message() {
    $message = file_get_contents('templates/error._header.tpl');

    $message .= $this->error_message;

    for ($i=0; $i<count($this->fetchers); $i++) {
      $message .= $this->fetchers[$i]->error_message();
    };

    $message .= $this->output_driver->error_message();
    
    $message .= file_get_contents('templates/error._footer.tpl');
    return $message;
  }
}

?>