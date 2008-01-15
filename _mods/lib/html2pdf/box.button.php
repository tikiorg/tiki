<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/box.button.php,v 1.1 2008-01-15 09:20:25 mose Exp $
/**
 * @package HTML2PS
 * @subpackage Document
 *
 * This file contains the class desribing layout and behavior of 'input type="button"' 
 * elements
 */

/**
 * @package HTML2PS
 * @subpackage Document
 * 
 * The ButtonBox class desribes the HTML buttons layout. (Note that 
 * button elements have 'display' CSS property set to HTML2PS-specific
 * '-button' value )
 *
 * @link http://www.w3.org/TR/html4/interact/forms.html#h-17.4 HTML 4.01 The INPUT element
 */
class ButtonBox extends InlineControlBox {
  /**
   * @var String URL to post the form to; may be null if this is not a 'submit' button
   * @access private
   */
  var $_action_url;

  /**
   * Create a new button element
   *
   * @param string $text text to be rendered on the button
   */
  function ButtonBox($text) {
    global $g_css_handlers;

    $handler =& get_css_handler('-html2ps-form-action');
    $this->_action_url = $handler->get();

    $this->InlineBox();

    /**
     * Button height includes vertical padding (e.g. the following two buttons 
     * <input type="button" value="test" style="padding: 10px; height: 50px;"/>
     * <input type="button" value="test" style="padding: 0px; height: 30px;"/>
     * are render by browsers with the same height!), so we'll need to adjust the 
     * height constraint, subtracting the vertical padding value from the constraint 
     * height value.
     */
    $hc = $this->get_height_constraint();
    if (!is_null($hc->constant)) {
      $hc->constant[0] -= $this->get_padding_top() + $this->get_padding_bottom();
    };
    $this->put_height_constraint($hc);
  
    /**
     * If button width is not constrained, then we'll add some space around the button text
     */
    $text = " ".$text." ";

    /**
     * Contents of the text box are somewhat similar to the inline box: 
     * a sequence of the text and whitespace boxes; we generate this sequence using
     * the InlineBox, then copy contents of the created inline box to our button.
     *
     * @todo probably, create_from_text() function should be extracted to the common parent 
     * of inline boxes.
     */
    $ibox = InlineBox::create_from_text($text);

    $size = count($ibox->content);
    for ($i=0; $i<$size; $i++) {
      $this->add_child($ibox->content[$i]);
    };
  }

  /**
   * Create a new button element from the DOM tree element
   *
   * @param DOMElement $root pointer to the DOM tree element corresponding to the button.
   * 
   * @return ButtonBox new button element
   */
  function &create(&$root) {
    /**
     * Button text is defined by its 'value' attrubute;
     * if this attribute is not specified, we should provide some 
     * appropriate defaults depending on the exact button type: 
     * reset, submit or generic button.
     *
     * Default button text values are specified in config file config.inc.php.
     *
     * @see config.inc.php
     * @see DEFAULT_SUBMIT_TEXT
     * @see DEFAULT_RESET_TEXT
     * @see DEFAULT_BUTTON_TEXT
     */
    if ($root->has_attribute("value")) {
      $text = $root->get_attribute("value");
    } else {
      switch ($root->tagname()) {
      case "submit":
        $text = DEFAULT_SUBMIT_TEXT;
        break;
      case "reset":
        $text = DEFAULT_RESET_TEXT;
        break;
      case "button":
        $text = DEFAULT_BUTTON_TEXT;
        break;
      default:
        $text = DEFAULT_BUTTON_TEXT;
        break;
      }
    };

    $box =& new ButtonBox($text);
    return $box;
  }

  /**
   * Render the button using the specified output driver
   * 
   * @param OutputDriver $driver The output driver object
   * 
   * @return boolean flag indicating an error (null value) or success (true)
   */
  function show(&$driver) {   
    /**
     * Set the baseline of a button box so that the button text will be aligned with 
     * the line box baseline
     */
    $this->default_baseline = $this->content[0]->baseline + $this->get_extra_top();
    $this->baseline         = $this->content[0]->baseline + $this->get_extra_top();

    /**
     * Render the button using default 'show' routine
     */
    $status = GenericContainerBox::show($driver);

    /**
     * Render the interactive button (if requested and possible)
     */
    global $g_config;
    if ($g_config['renderforms']) {
      $driver->field_pushbutton($this->get_left(), 
                                $this->get_top(), 
                                $this->get_width(), 
                                $this->get_height(),
                                $this->_action_url);
    };

    return $status;
  }
}
?>