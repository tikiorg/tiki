<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/box.table.cell.php,v 1.1 2008-01-15 09:20:27 mose Exp $

class TableCellBox extends GenericContainerBox {
  var $colspan;
  var $rowspan;
  var $column;

  function &create(&$root) {
    $box =& new TableCellBox($root);
    return $box;
  }

  function TableCellBox(&$root) {
    $this->colspan = 1;
    $this->rowspan = 1;

    // This value will be overwritten in table 'normalize_parent' method
    //
    $this->column  = 0;
    $this->row     = 0;

    if ($root->tagname() === 'td') {
      // Use cellspacing / cellpadding values from the containing table
      $handler =& get_css_handler('-cellspacing');
      $cellspacing = $handler->get();

      $cp_handler =& get_css_handler('-cellpadding');
      $cellpadding = $cp_handler->get();

      // FIXME: I'll need to resolve that issue with COLLAPSING border model. Now borders
      // are rendered separated

      // if not border set explicitly, inherit value set via border attribute of TABLE tag
      if (is_default_border(get_border())) {
        $border = get_table_border(); 
        pop_border();
        push_border($border);
      };

      $margin =& get_css_handler('margin');
      $margin->replace($margin->default_value());
      
      $handler =& get_css_handler('border-collapse');
      if ($handler->get() == BORDER_COLLAPSE) {
        $h_padding =& get_css_handler('padding');
        
        if ($h_padding->is_default($h_padding->get())) {
          $h_padding->css($cellpadding);
        };
      } else {
        $h_padding =& get_css_handler('padding');

        if ($h_padding->is_default($h_padding->get())) {
          $h_padding->css($cellpadding);
        };
        
        if ($margin->is_default($margin->get())) {
          $margin->css($cellspacing/2);
        }
      };
      
      // Save colspan and rowspan information
      $this->colspan = max(1,(int)$root->get_attribute('colspan'));
      $this->rowspan = max(1,(int)$root->get_attribute('rowspan'));
    } // $root->tagname() == 'td'

    // Call parent constructor
    $this->GenericContainerBox();

    // 'vertical-align' CSS value is not inherited from the table cells
    $handler =& get_css_handler('vertical-align');

    $handler->push_default();

    $this->create_content($root);

    // H1-H6 and P elements should have their top/bottom margin suppressed if they occur as the first/last table cell child 
    // correspondingly; note that we cannot do it usung CSS rules, as there's no selectors for the last child. 
    //
    $child = $root->first_child();
    if ($child) {
      if ($child->node_type() == XML_ELEMENT_NODE) {
        if (array_search(strtolower($child->tagname()), array("h1","h2","h3","h4","h5","h6","p"))) {
          $this->content[0]->margin->top->value = 0;
        }
      };
    };

    $child = $root->last_child();
    if ($child) {
      if ($child->node_type() == XML_ELEMENT_NODE) {
        if (array_search(strtolower($child->tagname()), array("h1","h2","h3","h4","h5","h6","p"))) {
          $this->content[count($this->content)-1]->margin->bottom->value = 0;
        }
      };
    };

    // pop the default vertical-align value
    $handler->pop();
  }

  // Inherited from GenericBox

  function get_cell_baseline() {
    $content = $this->get_first_data();
    if ($content === null) { return 0; }
    return $content->baseline;
  }

  // Flow-control
  function reflow(&$parent, &$context) {
    GenericBox::reflow($parent, $context);

    // Determine upper-left _content_ corner position of current box 
    $this->put_left($parent->_current_x + $this->get_extra_left());

    // NOTE: Table cell margin is used as a cell-spacing value
    $this->put_top($parent->_current_y - $this->border->top->get_width() - $this->padding->top->value);

    // CSS 2.1: 
    // Floats, absolutely positioned elements, inline-blocks, table-cells, and elements with 'overflow' other than
    // 'visible' establish new block formatting contexts.
    $context->push();
    $context->push_container_uid($this->uid);

    // Reflow cell content
    $this->reflow_content($context);

    // Extend the table cell height to fit all contained floats
    //
    // Determine the bottom edge corrdinate of the bottommost float
    //
    $float_bottom = $context->float_bottom();
      
    if ($float_bottom !== null) {
      $this->extend_height($float_bottom);
    };

    // Restore old context
    $context->pop_container_uid();
    $context->pop();
  }

  function to_ps(&$psdata) {
    $psdata->write("box-table-cell-create\n");
    $psdata->write($this->colspan." 1 index box-table-cell-put-colspan\n");
    $psdata->write($this->rowspan." 1 index box-table-cell-put-rowspan\n");
    $psdata->write("dup /column ".$this->column ." put \n");
    $psdata->write("dup /row ".$this->row ." put \n");

    $this->to_ps_common($psdata);
    $this->to_ps_css($psdata);
    $this->to_ps_content($psdata);

    $psdata->write("add-child\n");
  }
}

class FakeTableCellBox extends TableCellBox {
  var $colspan;
  var $rowspan;

  function FakeTableCellBox() {
    // Required to reset any constraints initiated by CSS properties
    push_css_defaults();

    $this->colspan = 1;
    $this->rowspan = 1;
    $this->GenericContainerBox();

    $this->content[] = new NullBox;

    pop_css_defaults();
  }

  function show(&$viewport) {
    return true;
  }

  function to_ps(&$psdata) {
    $psdata->write("box-table-cell-fake-create\n");
    $psdata->write("add-child\n");
  }
}

?>