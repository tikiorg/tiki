<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/box.input.text.php,v 1.1 2008-01-15 09:20:26 mose Exp $

/// define('SIZE_SPACE_KOEFF',1.65); (defined in tag.input.inc.php)

class TextInputBox extends InlineControlBox {
  /**
   * @var String contains the default value of this text field
   * @access private
   */
  var $_value;

  function &create(&$root) {
    // Control size
    $size = (int)$root->get_attribute("size"); 
    if (!$size) { $size = DEFAULT_TEXT_SIZE; };

    // Text to be displayed
    if ($root->has_attribute('value')) {
      $text = str_pad($root->get_attribute("value"), $size, " ");
    } else {
      $text = str_repeat(" ",$size*SIZE_SPACE_KOEFF);
    };

    $box =& new TextInputBox($size, $text);
    return $box;
  }

  function TextInputBox($size, $text) {
    // Call parent constructor
    $this->InlineBox();

    $this->_value = $text;
    
    // TODO: international symbols! neet to use somewhat similar to 'process_word' in InlineBox
    push_css_text_defaults();
    $this->add_child(TextBox::create($text, 'iso-8859-1'));
    pop_css_defaults();
  }

  function show(&$driver) {   
    // Now set the baseline of a button box to align it vertically when flowing isude the 
    // text line
    $this->default_baseline = $this->content[0]->baseline + $this->get_extra_top();
    $this->baseline         = $this->content[0]->baseline + $this->get_extra_top();

    /**
     * If we're rendering the interactive form, the field content should not be rendered
     */
    global $g_config;
    if ($g_config['renderforms']) {
      /**
       * Render background/borders only
       */
      $status = GenericBox::show($driver);

      /**
       * @todo encoding name?
       * @todo font name?
       * @todo check if font is embedded for PDFLIB
       */
      $driver->setfontcore('Helvetica', $this->font_size);
      $driver->field_text($this->get_left(), 
                          $this->get_top(),
                          $this->get_width(),
                          $this->get_height(),
                          $this->_value);
    } else {
      /**
       * Render everything, including content
       */ 
      $status = GenericContainerBox::show($driver);
    }

    return $status;
  }
}
?>