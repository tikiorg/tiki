<?php
class DataFilterDoctype extends DataFilter {
  function DataFilterDoctype() { }

  function process(&$data) {
    global $g_config;

    $html = $data->get_content();

    if (preg_match('#<!DOCTYPE\s+HTML\s+PUBLIC\s+"-//W3C//DTD HTML 4.01//EN"\s+"http://www.w3.org/TR/html4/strict.dtd">#is',
                   $html)) {
      $g_config['mode'] = 'html';
      return $data;
    };

    if (preg_match('#<!DOCTYPE\s+HTML\s+PUBLIC\s+"-//W3C//DTD HTML 4.01 Transitional//EN"\s+"http://www.w3.org/TR/html4/loose.dtd">#is',
                   $html)) {
      $g_config['mode'] = 'html';
      return $data;
    };

    if (preg_match('#<!DOCTYPE\s+HTML\s+PUBLIC\s+"-//W3C//DTD HTML 4.01 Frameset//EN"\s+"http://www.w3.org/TR/html4/frameset.dtd">#is',
                   $html)) {
      $g_config['mode'] = 'html';
      return $data;
    };

    if (preg_match('#<!DOCTYPE\s+html\s+PUBLIC\s+"-//W3C//DTD XHTML 1.0 Strict//EN"\s+"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">#is',
                   $html)) {
      $g_config['mode'] = 'xhtml';
      return $data;
    };

    if (preg_match('#<!DOCTYPE\s+html\s+PUBLIC\s+"-//W3C//DTD XHTML 1.0 Transitional//EN"\s+"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">#is',
                   $html)) {
      $g_config['mode'] = 'xhtml';
      return $data;
    };

   if (preg_match('#<!DOCTYPE\s+html\s+PUBLIC\s+"-//W3C//DTD XHTML 1.0 Frameset//EN"\s+"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">#is',
                   $html)) {
      $g_config['mode'] = 'xhtml';
      return $data;
    };

    $g_config['mode'] = 'quirks';
    return $data;
  }
}
?>